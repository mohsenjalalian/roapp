<?php

namespace AppBundle\Controller\Customer\Dashboard;

use AppBundle\Entity\Driver;
use AppBundle\Form\Customer\Dashboard\DriverType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Driver controller.
 *
 * @Route("/driver")
 * @Security("has_role('ROLE_CUSTOMER')")
 */
class DriverController extends Controller
{
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
            $em->flush($driver);

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
            $em->flush($driver);
        }

        return $this->redirectToRoute('driver_index');
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
