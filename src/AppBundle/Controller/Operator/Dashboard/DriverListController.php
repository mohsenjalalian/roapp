<?php

namespace AppBundle\Controller\Operator\Dashboard;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Driver;
use AppBundle\Form\DriverType;

class DriverListController extends Controller
{
    /**
     * Lists all Driver entities.
     *
     * @Route("/driverList", name="operator_dashboard_driver_list")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM AppBundle:Driver a ORDER BY a.id";
        $query = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );
        return $this->render
        (
            'operator/dashboard/driverList/index.html.twig',
            [
                'pagination' => $pagination
            ]
        );
    }

    /**
     * Creates a new Driver entity.
     *
     * @Route("/createDriver", name="operator_dashboard_driver_create")
     * @Method({"GET", "POST"})
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

            return $this->redirectToRoute('operator_dashboard_driver_show', array('id' => $driver->getId()));
        }

        return $this->render('operator/dashboard/driverList/new.html.twig', array(
            'driver' => $driver,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Driver entity.
     *
     * @Route("/driverShow/{id}", name="operator_dashboard_driver_show")
     * @Method("GET")
     */
    public function showAction(Driver $driver)
    {
        $deleteForm = $this->createDeleteForm($driver);

        return $this->render('operator/dashboard/driverList/show.html.twig', array(
            'driver' => $driver,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Driver entity.
     *
     * @Route("/driver{id}/edit", name="operator_dashboard_driver_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Driver $driver)
    {
        $deleteForm = $this->createDeleteForm($driver);
        $editForm = $this->createForm('AppBundle\Form\Operator\Dashboard\DriverType', $driver);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($driver);
            $em->flush();

            return $this->redirectToRoute('operator_dashboard_driver_edit', array('id' => $driver->getId()));
        }

        return $this->render('operator/dashboard/driverList/edit.html.twig', array(
            'driver' => $driver,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Driver entity.
     *
     * @Route("/{id}", name="operator_dashboard_driver_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Driver $driver)
    {
        $form = $this->createDeleteForm($driver);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($driver);
            $em->flush();
        }

        return $this->redirectToRoute('operator_dashboard_driver_list');
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
            ->setAction($this->generateUrl('operator_dashboard_driver_delete', array('id' => $driver->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
