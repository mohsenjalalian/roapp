<?php

namespace AppBundle\Controller\Customer\Dashboard;

use AppBundle\Entity\Customer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Address;
use AppBundle\Form\Customer\Dashboard\AddressType;

/**
 * Address controller.
 *
 * @Route("/address")
 * @Security("has_role('ROLE_CUSTOMER')")
 */
class AddressController extends Controller
{
    /**
     * Lists all Address entities.
     *
     * @Route("/", name="app_customer_dashboard_address_index")
     * @Method("GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('AppBundle:Address')
            ->createQueryBuilder('a')
            ->where('a.customer =:user_id')
            ->setParameter('user_id',$this->getUser()->getId())
            ->getQuery();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );

        return $this->render('customer/dashboard/address/index.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     * Creates a new Address entity.
     *
     * @Route("/new", name="app_customer_dashboard_address_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $address = new Address();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);
        $owner = $request->query->get('owner');

        if ($form->isSubmitted()) {
            $address->setCreator($this->getUser());

            if ($owner == null) {
                $address->setCustomer($this->getUser());
            } else {
                $customer = $this->getDoctrine()
                    ->getRepository('AppBundle:Customer')
                    ->findOneBy(
                        array('phone' => $owner)
                    );

                if (!$customer) {
                    $customer = new Customer();
                    $customer->setPhone($owner);
                    $customer->setPassword('1234');
                    $em->persist($customer);
                }
                $address->setIsPublic(false);
                $address->setCustomer($customer);
            }

            if ($form->isValid()) {
                $em->persist($address);
                $em->flush();

                return $this->redirectToRoute('app_customer_dashboard_address_show',
                    array('id' => $address->getId())
                );
            }
        }

        return $this->render('customer/dashboard/address/new.html.twig', array(
            'address' => $address,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Address entity.
     *
     * @Route("/{id}", name="app_customer_dashboard_address_show")
     * @Method("GET")
     */
    public function showAction(Address $address)
    {
        $deleteForm = $this->createDeleteForm($address);

        return $this->render('customer/dashboard/address/show.html.twig', array(
            'address' => $address,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Address entity.
     *
     * @Route("/{id}/edit", name="app_customer_dashboard_address_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Address $address)
    {
        $deleteForm = $this->createDeleteForm($address);
        $editForm = $this->createForm(AddressType::class, $address);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($address);
            $em->flush();

            return $this->redirectToRoute('app_customer_dashboard_address_edit', array('id' => $address->getId()));
        }

        return $this->render('customer/dashboard/address/edit.html.twig', array(
            'address' => $address,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Address entity.
     *
     * @Route("/{id}", name="app_customer_dashboard_address_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Address $address)
    {
        $form = $this->createDeleteForm($address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($address);
            $em->flush();
        }

        return $this->redirectToRoute('app_customer_dashboard_address_index');
    }

    /**
     * Creates a form to delete a Address entity.
     *
     * @param Address $address The Address entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Address $address)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_customer_dashboard_address_delete', array('id' => $address->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Route("/add_address",name="customer_dashboard_address_add_address")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    // add address with ajax method
    public function addAddressAction(Request $request)
    {
        $address = new Address();
        $currentUser = $this->getUser()->getId();
        $customer = $this->getDoctrine()
            ->getRepository("AppBundle:Customer")
            ->find($currentUser);
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);
        if ($form->isValid()) {
           $reciverMobile = $request->request->get('shipment_otherAddress_number');
            if ($reciverMobile) {
                $address = $this->get("app.address_service")
                    ->createAddress($address, $reciverMobile, $customer);
            } else {
                $address = $this->get("app.address_service")
                    ->createAddress($address, null, $customer);
            }
            $arr = [
                'description' => $address->getDescription(),
                'cId' => $address->getCustomer()->getId(), 
                'isPublic' => $address->getIsPublic()
            ];
            $res = json_encode($arr);

            return new JsonResponse($res);
            
        } else {
            throw new \Exception('Invalid information');
        }
    }
}