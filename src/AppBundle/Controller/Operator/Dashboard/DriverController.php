<?php

namespace AppBundle\Controller\Operator\Dashboard;

use AppBundle\Entity\Shipment;
use AppBundle\Form\Operator\Dashboard\DriverType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Driver;

/**
 * Class DriverListController
 * @package AppBundle\Controller\Operator\Dashboard
 * @Route("/driver")
 * @Security("has_role('ROLE_USER')")
 */
class DriverController extends Controller
{
    /**
     * Lists all Driver entities.
     *
     * @Route("/", name="app_operator_dashboard_driver_index")
     * @Method("GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $query = $this->getDoctrine()
            ->getRepository('AppBundle:Driver')
            ->createQueryBuilder('d')
            ->where('d.businessUnit IS NULL')
            ->orderBy('d.id', 'Asc')
            ->getQuery()
        ;

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );

        return $this->render(
            'operator/dashboard/driver/index.html.twig',
            [
                'pagination' => $pagination,
            ]
        );
    }

    /**
     * Creates a new Driver entity.
     *
     * @Route("/create", name="app_operator_dashboard_driver_create")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $driver = new Driver();
        $form = $this->createForm('AppBundle\Form\Operator\Dashboard\DriverType', $driver);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($driver);
            $em->flush();
            $translator = $this->get('translator');
            $this->addFlash('registered_success', $translator->trans('driver_registered_successfully'));


            return $this->redirectToRoute('app_operator_dashboard_driver_show', ['id' => $driver->getId()]);
        }

        return $this->render('operator/dashboard/driver/new.html.twig', [
            'driver' => $driver,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Driver entity.
     *
     * @Route("/show/{id}", name="app_operator_dashboard_driver_show")
     * @Method("GET")
     * @param Driver $driver
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Driver $driver)
    {
        $deleteForm = $this->createDeleteForm($driver);

        return $this->render('operator/dashboard/driver/show.html.twig', [
            'driver' => $driver,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Driver entity.
     *
     * @Route("/{id}/edit", name="app_operator_dashboard_driver_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Driver  $driver
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Driver $driver)
    {
        $deleteForm = $this->createDeleteForm($driver);
        $editForm = $this->createForm(
            DriverType::class,
            $driver,
            [
                'attr' => [
                    'id' => 'drive_edit_form_operator',
                ],
            ]
        );
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $currentPass = $editForm->get('currentPassword')->getData();
            $result = $this->getDoctrine()
                ->getRepository("AppBundle:Driver")
                ->validationCurrentPassword($driver->getId(), $currentPass);
            if ($result instanceof Driver) {
                $newPassword = $editForm->get('newPassword')->getData();
                $driver->setPassword($newPassword);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($driver);
            $em->flush();
            $translator = $this->get('translator');
            $this->addFlash('edited_success', $translator->trans('edited_successfully'));


            return new JsonResponse();
        }

        return $this->render('operator/dashboard/driver/edit.html.twig', [
            'driver' => $driver,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Driver entity.
     *
     * @Route("/delete/{id}", name="app_operator_dashboard_driver_delete")
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
            $em->flush();
            $translator = $this->get('translator');
            $this->addFlash('deleted_success', $translator->trans('driver_deleted_successfully'));
        }

        return $this->redirectToRoute('app_operator_dashboard_driver_index');
    }
    /**
     * @Route("/list/{shipment}" , name="app_operator_dashboard_driver_list")
     * @param Request       $request
     * @param Shipment|null $shipment
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request, Shipment $shipment = null)
    {
        // check shipment is exist
        if ($shipment) {
            // check shipment rejected by some driver or no
            $banDriver = $this
                ->getDoctrine()
                ->getRepository("AppBundle:Driver")
                ->filterDriverAction($shipment);

            $query = $this->getDoctrine()
                ->getRepository('AppBundle:Driver')
                ->createQueryBuilder('d')
                ->where("d.status=:free")
                ->andWhere('d.businessUnit IS NULL')
                ->setParameter('free', Driver::STATUS_FREE)
                ->orderBy('d.fullName', 'Asc')
                ->getQuery()
            ;

            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1)/*page number*/,
                10/*limit per page*/
            );

            return $this->render(
                "operator/dashboard/driver/list.html.twig",
                [
                    'shipmentId' => $shipment->getId(),
                    'banDriverList' => $banDriver,
                    'pagination' => $pagination,
                ]
            );
        } else {
            return $this->redirectToRoute(
                "app_operator_dashboard_shipment_list"
            );
        }
    }
    /**
     * Creates a form to delete a Driver entity.
     *
     * @param Driver $driver The Driver entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Driver $driver)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_operator_dashboard_driver_delete', ['id' => $driver->getId()]))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
