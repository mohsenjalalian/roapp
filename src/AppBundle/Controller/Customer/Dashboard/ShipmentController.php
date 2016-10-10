<?php

namespace AppBundle\Controller\Customer\Dashboard;

use AppBundle\Form\Customer\Dashboard\ShipmentType;
use DateTime;
use jDateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Shipment;

/**
 * Shipment controller.
 *
 * @Route("/shipment")
 */
class ShipmentController extends Controller
{
    /**
     * Lists all Shipment entities.
     *
     * @Route("/", name="customer_dashboard_shipment_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $shipments = $em->getRepository('AppBundle:Shipment')->findAll();

        return $this->render(':customer/dashboard/shipment:index.html.twig', array(
            'shipments' => $shipments,
        ));
    }

    /**
     * Creates a new Shipment entity.
     *
     * @Route("/new", name="customer_dashboard_shipment_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $shipment = new Shipment();
        $now = new \DateTime();
        $tomorrow = $now->add(new \DateInterval('P1D'));
        $shipment->setPickUpTime($tomorrow);
        $form = $this->createForm(ShipmentType::class, $shipment);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $description = $form->get("description")
                ->getData();
            $value = $form->get("value")
                ->getData();
            $shipment->setDescription($description);
            $shipment->setValue($value);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($shipment);
            $em->flush();

            return $this->redirectToRoute('customer_dashboard_shipment_show', array('id' => $shipment->getId()));
        }

        return $this->render('customer/dashboard/shipment/new.html.twig', array(
            'shipment' => $shipment,
            'form' => $form->createView(),
        ));
    }
    /**
     * Finds and displays a Shipment entity.
     *
     * @Route("/{id}", name="customer_dashboard_shipment_show")
     * @Method("GET")
     */
    public function showAction(Shipment $shipment)
    {
        $deleteForm = $this->createDeleteForm($shipment);

        return $this->render('customer/dashboard/shipment/show.html.twig', array(
            'shipment' => $shipment,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Shipment entity.
     *
     * @Route("/{id}/edit", name="customer_dashboard_shipment_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Shipment $shipment)
    {
        $deleteForm = $this->createDeleteForm($shipment);
        $editForm = $this->createForm(ShipmentType::class, $shipment);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($shipment);
            $em->flush();

            return $this->redirectToRoute('customer_dashboard_shipment_edit', array('id' => $shipment->getId()));
        }

        return $this->render('customer/dashboard/shipment/edit.html.twig', array(
            'shipment' => $shipment,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Shipment entity.
     *
     * @Route("/{id}", name="customer_dashboard_shipment_delete")
     * @Method("DELETE")
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

        return $this->redirectToRoute('customer_dashboard_shipment_index');
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
            ->setAction($this->generateUrl('customer_dashboard_shipment_delete', array('id' => $shipment->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
