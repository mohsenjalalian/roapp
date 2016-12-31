<?php

namespace AppBundle\Controller\Customer\Dashboard;

use AppBundle\Entity\Customer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Config\Definition\Exception\Exception;
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
            ->setParameter('user_id', $this->getUser()->getId())
            ->getQuery();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );

        return $this->render('customer/dashboard/address/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Creates a new Address entity.
     *
     * @Route("/new", name="app_customer_dashboard_address_new")
     * @param Request $request
     * @Method({"GET", "POST"})
     * @return \Symfony\Component\HttpFoundation\Response
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
                $address->setBusinessUnit($this->getUser()->getBusinessUnit());
            } else {
                $customer = $this->getDoctrine()
                    ->getRepository('AppBundle:Customer')
                    ->findOneBy(
                        ['phone' => $owner]
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

                $translated = $this->get('translator');
                $this->addFlash('registered_success', $translated->trans('address_registered_successfully'));

                return $this->redirectToRoute(
                    'app_customer_dashboard_address_show',
                    [
                        'id' => $address->getId(),
                    ]
                );
            }
        }

        return $this->render('customer/dashboard/address/new.html.twig', [
            'address' => $address,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Address entity.
     *
     * @Route("/{id}", name="app_customer_dashboard_address_show")
     * @param Address $address
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Address $address)
    {
        $deleteForm = $this->createDeleteForm($address);

        return $this->render('customer/dashboard/address/show.html.twig', [
            'address' => $address,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Address entity.
     *
     * @Route("/{id}/edit", name="app_customer_dashboard_address_edit")
     * @param Request $request
     * @param Address $address
     * @Method({"GET", "POST"})
     * @return \Symfony\Component\HttpFoundation\Response
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

            $translated = $this->get('translator');
            $this->addFlash('edited_success', $translated->trans('edited_successfully'));

            return $this->redirectToRoute('app_customer_dashboard_address_edit', ['id' => $address->getId()]);
        }

        return $this->render('customer/dashboard/address/edit.html.twig', [
            'address' => $address,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Address entity.
     *
     * @param Request $request
     * @param Address $address
     * @Route("/{id}", name="customer_dashboard_address_delete")
     * @Method("DELETE")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, Address $address)
    {
        $form = $this->createDeleteForm($address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $isShipmentExists = $this->getDoctrine()->getRepository("AppBundle:Shipment")->isShipmentExists($address);
                if ($isShipmentExists) {
                    $em->remove($address);
                    $em->flush();
                    $translator = $this->get('translator');
                    $this->addFlash('deleted_success', $translator->trans('address_deleted_successfully'));
                } else {
                    throw new Exception();
                }
            } catch (Exception $e) {
                $translator = $this->get('translator');
                $this->addFlash('edited_success', $translator->trans('address_deleted_unsuccessfully'));
            }
        }

        return $this->redirectToRoute('app_customer_dashboard_address_index');
    }

    /**
     * @Route("/add_address",name="customer_dashboard_address_add_address")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
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
                'isPublic' => $address->getIsPublic(),
                'addressId' => $address->getId(),
            ];
            $res = json_encode($arr);

            return new JsonResponse($res);
        } else {
            throw new \Exception('Invalid information');
        }
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
            ->setAction($this->generateUrl('customer_dashboard_address_delete', ['id' => $address->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
