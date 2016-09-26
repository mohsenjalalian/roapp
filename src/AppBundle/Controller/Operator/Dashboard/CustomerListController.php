<?php

namespace AppBundle\Controller\Operator\Dashboard;

use AppBundle\Entity\Customer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CustomerListController extends Controller
{
    /**
     * Lists all Customer entities.
     * @Route("/customersList", name="operator_dashboard_customer_list")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM AppBundle:Customer a ORDER BY a.id";
        $query = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );
        return $this->render
        (
            'operator/dashboard/customerList/index.html.twig',
            [
                'pagination' => $pagination
            ]
        );
    }
    /**
     * Creates a new Customer entity.
     *
     * @Route("/createCustomer", name="operator_dashboard_create_customer")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $customer = new Customer();
        $form = $this->createForm('AppBundle\Form\Operator\Dashboard\CustomerType', $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($customer);
            $em->flush();

            return $this->redirectToRoute('operator_dashboard_customer_show', array('id' => $customer->getId()));
        }

        return $this->render('operator/dashboard/customerList/new.html.twig', array(
            'customer' => $customer,
            'form' => $form->createView(),
        ));
    }
    /**
     * Finds and displays a Customer entity.
     *
     * @Route("/customerShow/{id}", name="operator_dashboard_customer_show")
     * @Method("GET")
     */
    public function showAction(Customer $customer)
    {
        $deleteForm = $this->createDeleteForm($customer);

        return $this->render('operator/dashboard/customerList/show.html.twig', array(
            'customer' => $customer,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Displays a form to edit an existing Customer entity.
     *
     * @Route("/customer{id}/edit", name="operator_dashboard_customer_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Customer $customer)
    {
        $deleteForm = $this->createDeleteForm($customer);
        $editForm = $this->createForm('AppBundle\Form\Operator\Dashboard\CustomerType', $customer);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($customer);
            $em->flush();

            return $this->redirectToRoute('operator_dashboard_customer_edit', array('id' => $customer->getId()));
        }

        return $this->render('operator/dashboard/customerList/edit.html.twig', array(
            'customer' => $customer,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Customer entity.
     *
     * @Route("/deleteCustomer{id}", name="operator_dashboard_customer_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Customer $customer)
    {
        $form = $this->createDeleteForm($customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($customer);
            $em->flush();
        }

        return $this->redirectToRoute('operator_dashboard_customer_list');
    }
    /**
     * Creates a form to delete a Customer entity.
     *
     * @param Customer $customer The Customer entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Customer $customer)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('operator_dashboard_customer_delete', array('id' => $customer->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}