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
     * @Route("/customers_list", name="app_operator_dashboard_customer_list_index")
     * @Method("GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $query = $this->getDoctrine()
            ->getRepository('AppBundle:Customer')
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
            'operator/dashboard/customerList/index.html.twig',
            [
                'pagination' => $pagination
            ]
        );
    }

    /**
     * Creates a new Customer entity.
     *
     * @Route("/create_customer", name="app_operator_dashboard_customer_list_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
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

            return $this->redirectToRoute('app_operator_dashboard_customer_list_show', array('id' => $customer->getId()));
        }

        return $this->render('operator/dashboard/customerList/new.html.twig', array(
            'customer' => $customer,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Customer entity.
     *
     * @Route("/customer_show/{id}", name="app_operator_dashboard_customer_list_show")
     * @Method("GET")
     * @param Customer $customer
     * @return \Symfony\Component\HttpFoundation\Response
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
     * @Route("/customer{id}/edit", name="app_operator_dashboard_customer_list_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Customer $customer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
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

            return $this->redirectToRoute('app_operator_dashboard_customer_list_edit', array('id' => $customer->getId()));
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
     * @Route("/delete_customer{id}", name="app_operator_dashboard_customer_list_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Customer $customer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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

        return $this->redirectToRoute('app_operator_dashboard_customer_list_index');
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
            ->setAction($this->generateUrl('app_operator_dashboard_customer_list_delete', array('id' => $customer->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}