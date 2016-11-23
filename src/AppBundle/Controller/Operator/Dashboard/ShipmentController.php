<?php

namespace AppBundle\Controller\Operator\Dashboard;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Shipment;
use AppBundle\Form\ShipmentType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use r;

/**
 * Class ShipmentController
 * @package AppBundle\Controller\Operator\Dashboard
 * @Route("/shipment")
 * @Security("has_role('ROLE_USER')")
 */
class ShipmentController extends Controller
{
    /**
     * Lists all Shipment entities.
     *
     * @Route("/", name="app_operator_dashboard_shipment_index")
     * @Method("GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $query = $this->getDoctrine()
            ->getRepository('AppBundle:Shipment')
            ->createQueryBuilder('a')
            ->orderBy('a.id', 'Asc')
            ->getQuery()
        ;
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );
        
        return $this->render
        (
            'operator/dashboard/shipment/index.html.twig',
            [
                'pagination' => $pagination
            ]
        );
    }

    /**
     * Finds and displays a Shipment entity.
     *
     * @Route("/show/{id}", name="app_operator_dashboard_shipment_show")
     * @Method("GET")
     * @param Shipment $shipment
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Shipment $shipment)
    {
        $conn = r\connect('localhost', '28015', 'roapp', '09126354397');
        $t = r\table('shipment')
            ->filter(
                [
                    'shipment_id' => $shipment->getId(),
                ]
            )
            ->run($conn);
        /** @var \ArrayObject $current */
        $current = $t->current();
        $trackingToken = $current->getArrayCopy()['tracking_token'];

        $deleteForm = $this->createDeleteForm($shipment);

        return $this->render('operator/dashboard/shipment/show.html.twig', array(
            'shipment' => $shipment,
            'tracking_token' =>$trackingToken,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Shipment entity.
     *
     * @Route("/{id}/edit", name="app_operator_dashboard_shipment_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Shipment $shipment
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Shipment $shipment)
    {
        $deleteForm = $this->createDeleteForm($shipment);
        $editForm = $this->createForm('AppBundle\Form\Operator\Dashboard\ShipmentType', $shipment);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($shipment);
            $em->flush();

            return $this->redirectToRoute('app_operator_dashboard_shipment_edit', array('id' => $shipment->getId()));
        }

        return $this->render('operator/dashboard/shipment/edit.html.twig', array(
            'shipment' => $shipment,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Shipment entity.
     *
     * @Route("/delete/{id}", name="app_operator_dashboard_shipment_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Shipment $shipment
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Shipment $shipment)
    {
        $form = $this->createDeleteForm($shipment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($shipment);
            $em->flush();
        }

        return $this->redirectToRoute('app_operator_dashboard_shipment_index');
    }

    /**
     * Creates a form to delete a Shipment entity.
     *
     * @param Shipment $shipment The Shipment entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Shipment $shipment)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_operator_dashboard_shipment_delete', array('id' => $shipment->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @Route("/list",name="app_operator_dashboard_shipment_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request){
        $query = $this->getDoctrine()
            ->getRepository('AppBundle:Shipment')
            ->createQueryBuilder('s')
            ->where('s.status=:notAssign')
            ->setParameter('notAssign',0)
            ->orderBy('s.pickUpTime', 'Asc')
            ->getQuery()
        ;

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render(
            ":operator/dashboard/shipment:list.html.twig",
            [
                'pagination' => $pagination
            ]
        );
    }

    /**
     * @Route("/load_map",name="app_operator_dashboard_shipment_load_map")
     * @param Request $request
     * @return JsonResponse
     */
    public function loadMapAction (Request $request) {

            $conn = r\connect('localhost', '28015', 'roapp', '09126354397');
        $result = r\table('shipment')
            ->filter(
                [
                    'tracking_token' => $request->get('token')
                ]
            )
            ->run($conn);
        /** @var \ArrayObject $current */
        $current = $result->current();
        $id = $current->getArrayCopy()['id'];
        $cursor = r\table('driver_location')
            ->filter(
                [
                    'shipment_id' => $id,
                ]
            )
            ->limit(4)
            ->run($conn);

        $last_location = r\table('driver_location')
            ->filter(
                [
                    'shipment_id' => $id,
                ]
            )
            ->orderBy(r\desc('date_time'))
            ->limit(1)
            ->run($conn);

        $counter = 0;
        foreach ($cursor as $value) {
            $output[$counter]['lat'] = $value->getArrayCopy()['lat'];
            $output[$counter]['lng'] = $value->getArrayCopy()['lng'];
            $output[$counter]['lastLat'] = $last_location[0]->getArrayCopy()['lat'];
            $output[$counter]['lastLng'] = $last_location[0]->getArrayCopy()['lng'];
            $counter = $counter+1;
        }

        return new JsonResponse($output );
    }

    /**
     * @Route("/reject",name="app_operator_dashboard_shipment_reject")
     * @param Request $request
     * @return Response
     */
    public function rejectAction(Request $request)
    {
        $shipmentId = $request->request->get("id");
        $em = $this->getDoctrine()->getManager();
        $shipment = $this->getDoctrine()
            ->getRepository("AppBundle:Shipment")
            ->find($shipmentId);
        $shipment->setStatus(Shipment::STATUS_ASSIGNMENT_REJECT);
        $em->persist($shipment);

        $em->flush();
//        send notification to customer
        $customerId = $shipment->getOwnerAddress()
            ->getCustomer()
            ->getId();
        $logger = $this->get('logger');
        $logger->info('send notification to'." ".$customerId);

        return new Response($shipmentId);
    }
}
