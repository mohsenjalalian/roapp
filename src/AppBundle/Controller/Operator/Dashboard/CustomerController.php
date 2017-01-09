<?php

namespace AppBundle\Controller\Operator\Dashboard;

use AppBundle\Entity\Customer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CustomerListController
 * @package AppBundle\Controller\Operator\Dashboard
 * @Route("/customer")
 * @Security("has_role('ROLE_USER')")
 */
class CustomerController extends Controller
{
    /**
     * Lists all Customer entities.
     * @Route("/{id}", name="app_operator_dashboard_customer_index", requirements={"id": "\d+"})
     * @Method("GET")
     * @param Request $request
     * @param int     $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, $id = null)
    {
        if ($id) {
            //TODO implement query for get each customer in businessUnit
        } else {
            $query = $this->getDoctrine()
                ->getRepository('AppBundle:Customer')
                ->createQueryBuilder('a')
                ->orderBy('a.id', 'Asc')
                ->getQuery();
        }

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );

        return $this->render(
            'operator/dashboard/customer/index.html.twig',
            [
                'pagination' => $pagination,
            ]
        );
    }

    /**
     * Creates a new Customer entity.
     *
     * @Route("/create", name="app_operator_dashboard_customer_new")
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
            $translator = $this->get('translator');
            $this->addFlash('registered_success', $translator->trans('customer_registered_successfully'));


            // send userName, password and login page link to customer's email
            $router = $this->get('router');
            $loginPageUrl = $router->generate("app_operator_dashboard_security_login");
            $loginPageLink = $this->get("roapp_media.upload_manager")->getAbsoluteUrl($loginPageUrl);
            $logger = $this->get('logger');
            $logger->info("userName:".$customer->getPhone().' '."password:".$customer->getPassword().' '.'link:'.$loginPageLink);

            return $this->redirectToRoute('app_operator_dashboard_customer_show', ['id' => $customer->getId()]);
        }

        return $this->render('operator/dashboard/customer/new.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Customer entity.
     *
     * @Route("/show/{id}", name="app_operator_dashboard_customer_show")
     * @Method("GET")
     * @param Customer $customer
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Customer $customer)
    {
        $deleteForm = $this->createDeleteForm($customer);

        return $this->render('operator/dashboard/customer/show.html.twig', [
            'customer' => $customer,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Customer entity.
     *
     * @Route("/{id}/edit", name="app_operator_dashboard_customer_edit")
     * @Method({"GET", "POST"})
     * @param Request  $request
     * @param Customer $customer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Customer $customer)
    {
        $deleteForm = $this->createDeleteForm($customer);
        $editForm = $this
            ->createForm(
                'AppBundle\Form\Operator\Dashboard\CustomerType',
                $customer,
                [
                    'attr' => [
                        'id' => 'customer_form_operator_dashboard',
                    ],
                ]
            );
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $currentPass = $editForm->get('currentPassword')->getData();
            if (trim($currentPass)) {
                $result = $this->getDoctrine()
                    ->getRepository("AppBundle:Customer")
                    ->validationCurrentPassword($customer->getId(), $currentPass);
                // check current password is correct
                if ($result instanceof Customer) {
                    $newPassword = $editForm->get('newPassword')->getData();
                    $customer->setPassword($newPassword);
                    $em->persist($customer);
                    $em->flush();

                    $translator = $this->get('translator');
                    $this->addFlash('edited_success', $translator->trans('edited_successfully'));

                    return new JsonResponse();
                } else {
                    return new JsonResponse("current password is wrong");
                }
            } else {
                $em->persist($customer);
                $em->flush();
                $translator = $this->get('translator');
                $this->addFlash('edited_success', $translator->trans('edited_successfully'));

                return new JsonResponse();
            }
        }

        return $this->render('operator/dashboard/customer/edit.html.twig', [
            'customer' => $customer,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Customer entity.
     *
     * @Route("/delete/{id}", name="app_operator_dashboard_customer_delete")
     * @Method("DELETE")
     * @param Request  $request
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
            $translator = $this->get('translator');
            $this->addFlash('deleted_success', $translator->trans('customer_deleted_successfully'));
        }

        return $this->redirectToRoute('app_operator_dashboard_customer_index');
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
            ->setAction($this->generateUrl('app_operator_dashboard_customer_delete', ['id' => $customer->getId()]))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
