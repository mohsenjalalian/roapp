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
     * @Route("/drivers_list", name="app_operator_dashboard_driver_list_index")
     * @Method("GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $query = $this->getDoctrine()
            ->getRepository('AppBundle:Driver')
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
            'operator/dashboard/driverList/index.html.twig',
            [
                'pagination' => $pagination
            ]
        );
    }

    /**
     * Creates a new Driver entity.
     *
     * @Route("/create_driver", name="app_operator_dashboard_driver_list_create")
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

            return $this->redirectToRoute('app_operator_dashboard_driver_list_show', array('id' => $driver->getId()));
        }

        return $this->render('operator/dashboard/driverList/new.html.twig', array(
            'driver' => $driver,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Driver entity.
     *
     * @Route("/driver_show/{id}", name="app_operator_dashboard_driver_list_show")
     * @Method("GET")
     * @param Driver $driver
     * @return \Symfony\Component\HttpFoundation\Response
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
     * @Route("/driver{id}/edit", name="app_operator_dashboard_driver_list_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Driver $driver
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
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

            return $this->redirectToRoute('app_operator_dashboard_driver_list_edit', array('id' => $driver->getId()));
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
     * @Route("/{id}", name="app_operator_dashboard_driver_list_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Driver $driver
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
        }

        return $this->redirectToRoute('app_operator_dashboard_driver_list_index');
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
            ->setAction($this->generateUrl('app_operator_dashboard_driver_list_delete', array('id' => $driver->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
