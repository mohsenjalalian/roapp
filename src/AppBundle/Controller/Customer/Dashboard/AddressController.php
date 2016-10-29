<?php

namespace AppBundle\Controller\Customer\Dashboard;

use AppBundle\Entity\Customer;
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
 */
class AddressController extends Controller
{
    /**
     * Lists all Address entities.
     *
     * @Route("/", name="customer_dashboard_address_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $addresses = $em->getRepository('AppBundle:Address')->findAll();

        return $this->render(
            'customer/dashboard/address/index.html.twig',
            [
                'addresses' => $addresses,
            ]
        );
    }

    /**
     * Creates a new Address entity.
     *
     * @Route("/new", name="customer_dashboard_address_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);
        $owner = $request->query->get('owner');
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('app.address_service')->createAddress($address, $owner, $user);

                return $this->redirectToRoute(
                    'customer_dashboard_address_show',
                    [
                        'id' => $address->getId(),
                    ]
                );
        }

        return $this->render(
            'customer/dashboard/address/new.html.twig',
            [
                'address' => $address,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Address entity.
     *
     * @Route("/{id}", name="customer_dashboard_address_show")
     * @Method("GET")
     */
    public function showAction(Address $address)
    {
        $deleteForm = $this->createDeleteForm($address);

        return $this->render(
            'customer/dashboard/address/show.html.twig',
            [
                'address' => $address,
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing Address entity.
     *
     * @Route("/{id}/edit", name="customer_dashboard_address_edit")
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

            return $this->redirectToRoute(
                'customer_dashboard_address_show',
                [
                    'id' => $address->getId(),
                ]
            );
        }

        return $this->render(
            'customer/dashboard/address/edit.html.twig',
            [
                'address' => $address,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Deletes a Address entity.
     *
     * @Route("/{id}", name="customer_dashboard_address_delete")
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

        return $this->redirectToRoute('customer_dashboard_address_index');
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
            ->setAction(
                $this->generateUrl(
                    'customer_dashboard_address_delete',
                    [
                        'id' => $address->getId(),
                    ]
                ))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @Route("/add_address",name="customer_dashboard_address_add_address")
     * @param Request $request
     * @return JsonResponse
     */
    // add address with ajax method
    public function addAddressAction(Request $request){
//        die(dump($request->request->get('number')));
        $address = new Address();
        $currentUser = 6;
        $customer = $this->getDoctrine()
            ->getRepository("AppBundle:Customer")
            ->find($currentUser);
        $form =$this->createForm(AddressType::class, $address);
        $form->handleRequest($request);
        if($form->isValid()) {
            $reciverMobile = $request->request->get('mobile_reciver_number');
            if($reciverMobile) {
                $address = $this->get("app.address_service")->createAddress($address,$reciverMobile,$customer);
            } else {
                $address = $this->get("app.address_service")->createAddress($address,null,$customer);
            }
            $arr = ['description'=>$address->getDescription(),'cId'=>$address->getCustomer()->getId(),'isPublic'=>$address->getIsPublic()];
            $res = json_encode($arr);

            return new JsonResponse($res);
//            die(dump($request->request->get('mobile_reciver_number')));

//            $description = $form->get("description")->getData();
//            $latitude = $form->get("latitude")->getData();
//            $longtitude = $form->get("longitude")->getData();
//            $isPublic = $form->get("isPublic")->getData();
//            $address->setDescription($description);
//            $address->setLatitude($latitude);
//            $address->setLongitude($longtitude);
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($address);
//
//            $em->flush();
//            die(dump($address->getDescription()));
//            $arr = ['description'=>$address->getDescription(),'cId'=>$address->getCustomer()->getId(),'isPublic'=>$address->getIsPublic()];
//            $res = json_encode($arr);
//
//            return new JsonResponse($res);

        }
        else {
            die("not valid");
        }
    }
}
