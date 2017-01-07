<?php

namespace AppBundle\Controller\Customer\Dashboard;

use AppBundle\Entity\Driver;
use AppBundle\Form\Customer\Dashboard\DriverType;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use r;

/**
 * Driver controller.
 *
 * @Route("/driver")
 * @Security("has_role('ROLE_CUSTOMER')")
 */
class DriverController extends Controller
{
    /**
     * @Route("/track", name="app_customer_dashboard_driver_track")
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function trackAction()
    {
        $conn = r\connect('localhost', '28015', 'roapp', $this->getParameter('rethink_password'));
        $em = $this->getDoctrine()->getManager();
        $customer = $this->getUser();
        $businessUnit = $customer->getBusinessUnit();
        $shipments = $em->getRepository('AppBundle:Shipment')
            ->createQueryBuilder('s')
            ->join('s.ownerAddress', 'ow')
            ->join('ow.businessUnit', 'oc')
            ->where('oc.id =:business_unit_id')
            ->setParameter('business_unit_id', $businessUnit)
            ->getQuery()
            ->getResult();

        $trackingTokens = [];
        foreach ($shipments as $shipment) {
            /**
             * @var Shipment $shipment
             */
            $rethinkShipment = r\table('shipment')
                ->filter(
                    [
                        'shipment_id' => $shipment->getId(),
                    ]
                )
                ->run($conn);
            /** @var \ArrayObject $current */
            $current = $rethinkShipment->current();
            $trackingTokens[] = $current->getArrayCopy()['tracking_token'];
        }

        return $this->render('customer/dashboard/driver/track.html.twig', [
            'trackingTokens' => $trackingTokens,
        ]);
    }

    /**
     * @Route("/init_map",name="app_customer_dashboard_driver_init_map")
     * @param Request $request
     * @return JsonResponse | null
     */
    public function initMapAction(Request $request)
    {
        $token = $request->get('token');
        $conn = r\connect(
            $this->getParameter('rethinkdb_host'),
            $this->getParameter('rethinkdb_port'),
            'roapp',
            $this->getParameter('rethink_password')
        );
        if (!isset($conn)) {
            return new JsonResponse(null);
        }
        $result = r\table('shipment')
            ->filter(
                [
                    'tracking_token' => $token,
                ]
            )
            ->run($conn);
        if (!isset($result)) {
            return new JsonResponse(null);
        }
        /** @var \ArrayObject $current */
        $current = $result->current();
        $id = $current->getArrayCopy()['id'];
        $lastLocation = r\table('driver_location')
            ->filter(
                [
                    'shipment_id' => $id,
                ]
            )
            ->orderBy(r\desc('date_time'))
            ->limit(1)
            ->run($conn);
        if (!isset($lastLocation)) {
            return new JsonResponse(null);
        }
        $counter = 0;
        $output = [];
        foreach ($lastLocation as $value) {
            /** @var \ArrayObject $value */
            $output[$counter]['lat'] = $value->getArrayCopy()['lat'];
            $output[$counter]['lng'] = $value->getArrayCopy()['lng'];
            $output[$counter]['tracking_token'] = $token;
            $counter = $counter+1;
        }

        return new JsonResponse($output);
    }
    /**
     * Lists all driver entities.
     *
     * @Route("/", name="app_customer_dashboard_driver_index")
     * @Method("GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userBusinessUnit = $this->getUser()->getBusinessUnit();
        $query = $em->getRepository('AppBundle:Driver')
            ->createQueryBuilder('d')
            ->where('d.businessUnit =:user_business_unit')
            ->setParameter('user_business_unit', $userBusinessUnit)
            ->getQuery();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );

        return $this->render('customer/dashboard/driver/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Creates a new driver entity.
     *
     * @Route("/new", name="app_customer_dashboard_driver_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $driver = new Driver();
        $form = $this->createForm(DriverType::class, $driver);
        $form->handleRequest($request);
        $userBusinessUnit = $this->getUser()->getBusinessUnit();
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $driver->setBusinessUnit($userBusinessUnit);
            $em->persist($driver);
            /** @var EntityManager $em */
            $em->flush($driver);

            $translator = $this->get('translator');
            $this->addFlash('registered_success', $translator->trans('driver_registered_successfully'));


            return $this->redirectToRoute('app_customer_dashboard_driver_show', array('id' => $driver->getId()));
        }

        return $this->render('customer/dashboard/driver/new.html.twig', array(
            'driver' => $driver,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a driver entity.
     *
     * @Route("/{id}", name="app_customer_dashboard_driver_show")
     * @Method("GET")
     * @param Driver $driver
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Driver $driver)
    {
        $deleteForm = $this->createDeleteForm($driver);

        return $this->render('customer/dashboard/driver/show.html.twig', array(
            'driver' => $driver,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing driver entity.
     *
     * @Route("/{id}/edit", name="app_customer_dashboard_driver_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Driver  $driver
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Driver $driver)
    {
        $deleteForm = $this->createDeleteForm($driver);
        $editForm = $this->createForm(DriverType::class, $driver);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $translator = $this->get('translator');
            $this->addFlash('edited_success', $translator->trans('edited_successfully'));

            return $this->redirectToRoute('app_customer_dashboard_driver_edit', array('id' => $driver->getId()));
        }

        return $this->render('customer/dashboard/driver/edit.html.twig', array(
            'driver' => $driver,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a driver entity.
     *
     * @Route("/{id}", name="driver_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Driver  $driver
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Driver $driver)
    {
        $form = $this->createDeleteForm($driver);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($driver);
            /** @var EntityManager $em */
            $em->flush($driver);
            $translator = $this->get('translator');
            $this->addFlash('deleted_success', $translator->trans('driver_deleted_successfully'));
        }

        return $this->redirectToRoute('app_customer_dashboard_driver_index');
    }

    /**
     * Creates a form to delete a driver entity.
     *
     * @param Driver $driver The driver entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Driver $driver)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('driver_delete', array('id' => $driver->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
