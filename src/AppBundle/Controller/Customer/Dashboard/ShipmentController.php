<?php

namespace AppBundle\Controller\Customer\Dashboard;

use AppBundle\Entity\Address;
use AppBundle\Entity\Customer;
use AppBundle\Form\Customer\Dashboard\AddressType;
use AppBundle\Form\Customer\Dashboard\ShipmentType;
use AppBundle\Form\Customer\Dashboard\ValidationCodeType;
use DateTime;
use jDateTime;
//use Symfony\Component\BrowserKit\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Shipment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Url;
use r;

/**
 * Shipment controller.
 *
 * @Route("/shipment")
 * @Security("has_role('ROLE_CUSTOMER')")
 */
class ShipmentController extends Controller
{
    /**
     * Lists all Shipment entities.
     *
     * @Route("/", name="app_customer_dashboard_shipment_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('AppBundle:Shipment')
            ->createQueryBuilder('s')
            ->join('s.ownerAddress','ow')
            ->join('ow.customer','oc')
            ->where('oc.id =:user_id')
            ->setParameter('user_id',$this->getUser()->getId())
            ->orderBy('s.pickUpTime','Desc')
            ->getQuery();
        
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );
        
        //        $shipments = $em->getRepository('AppBundle:Shipment')->findAll();

        return $this->render(':customer/dashboard/shipment:index.html.twig', array(
//            'shipments' => $qb,
            'pagination' => $pagination,
        ));
    }

    /**
     * Creates a new Shipment entity.
     *
     * @Route("/new", name="app_customer_dashboard_shipment_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $shipment = new Shipment();
        $addressEntity = new Address();
        $customerId = $this->getUser()->getId(); // get current customer id
        $address = $this->getDoctrine()
            ->getRepository("AppBundle:Address")
            ->getPublicAddressCustomer($customerId);
        $now = new \DateTime();
        $tomorrow = $now->add(new \DateInterval('P1D'));
        $shipment->setPickUpTime($tomorrow);
        $form = $this->createForm(ShipmentType::class, $shipment);
        $addressForm = $this
            ->createForm(
                AddressType::class,
                $addressEntity,
                [
                    'action' => $this->generateUrl(
                        'customer_dashboard_address_add_address'
                    ),
                    'attr' => [
                        'id' => 'add_address'
                    ]
                ]
            )
        ;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ownerAddressId =  $request->request->get('publicAddress');
            $otherPhoneNumber = $form->get('other')->getData();
            $otherAddressId = $request->request->get('reciver_public_address');
            $ownerAddress = $this->getDoctrine()
                ->getRepository("AppBundle:Address")
                ->find($ownerAddressId)
            ;
            /** @var Customer $customer */
            $customer = $this->getDoctrine()
                ->getRepository('AppBundle:Customer')
                ->findOrCreateByPhone($otherPhoneNumber);

            $shipment->setOther($customer);
            $shipment->setOwnerAddress($ownerAddress);

            if ($otherAddressId) {
                $otherAddress = $this->getDoctrine()
                    ->getRepository("AppBundle:Address")
                    ->find($otherAddressId)
                ;
                $shipment->setOtherAddress($otherAddress);
            }

            $description = $form->get("description")
                ->getData();
            $value = $form->get("value")
                ->getData();
            $pickUpTime = $form->get('pickUpTime')
                ->getData();
            $shipmentPrice = $request
                ->request->get('price_shipment');

            $createdAt = new \DateTime();
            $shipment->setDescription($description);
            $shipment->setValue($value);
            $shipment->setPrice(floatval($shipmentPrice));
            $shipment->setPickUpTime($pickUpTime);
            $shipment->setCreatedAt($createdAt);
            $shipment->setStatus(Shipment::STATUS_NOT_ASSIGNED);
            $shipment->setType("send");

            $em = $this->getDoctrine()->getManager();
            $em->persist($shipment);
            $em->flush();

            $conn = r\connect('localhost');
            $driverToken = uniqid();
            $trackingToken = uniqid();
            $document = [
                'shipment_id' => $shipment->getId(),
                'driver_token' => $driverToken,
                'tracking_token' => $trackingToken,
            ];
            r\db("roapp")
                ->table("shipment")
                ->insert($document)
                ->run($conn);

            return $this->redirectToRoute('app_customer_dashboard_shipment_show', array('id' => $shipment->getId()));
        }

        return $this->render(
            'customer/dashboard/shipment/new.html.twig',
            [
                'customerId' => $customerId,
                'addressFrom' => $addressForm->createView(),
                'address' => $address,
                'shipment' => $shipment,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/load_owner_form",name="app_customer_dashboard_shipment_load_owner_form")
     */
    // load owner address form for show in modalBox
    public function loadOwnerAddressFormAction()
    {
        $addressEntity = new Address();
        $addressForm = $this
            ->createForm(
                AddressType::class,
                $addressEntity,
                [
                    'action' => $this->generateUrl(
                        'customer_dashboard_address_add_address'
                    ),
                    'attr' => [
                        'id' => 'add_address'
                    ]
                ]
            )
        ;

        return $this->render(
            'customer/dashboard/shipment/load_owner_form.html.twig',
            [
                'addressFrom' => $addressForm->createView(),
            ]
        );
    }

    /**
     * @Route("/load_other_form",name="app_customer_dashboard_shipment_load_other_form")
     * @param Request $request
     * @return Response
     */
    // load reciver address form for show in modalBox
    public function loadOtherAddressFormAction(Request $request)
    {
        $addressEntity = new Address();
        $addressForm = $this
            ->createForm(
                AddressType::class,
                $addressEntity,
                [
                    'action' => $this->generateUrl(
                        'customer_dashboard_address_add_address'
                    ),
                    'attr' => [
                        'id' => 'add_address'
                    ],
                ]
            )
        ;

        return $this->render(
            'customer/dashboard/shipment/load_other_form.html.twig',
            [
                'addressFrom' => $addressForm->createView(),
            ]
        );
    }

    /**
     * @Route("/get_customer_address",name="app_customer_dashboard_shipment_get_customer_address")
     * @param Request $request
     * @return JsonResponse
     */
    // fetch customer Address from DB
    public function getCustomerAddressAction(Request $request)
    {
        $currentCustomer = $this->getUser()->getId();
        $phoneNumber = $request->request->get("phoneNumber");
        $customerInfo = $this->getDoctrine()
            ->getRepository("AppBundle:Customer")
            ->findOneBy(
                [
                    'phone' => $phoneNumber
                ]
            );
        // check does customer exist with entered phone number ??
        if ($customerInfo) {
            $address = $this->getDoctrine()
                ->getRepository("AppBundle:Address")
                ->getPublicAddressOrCreator(
                    $customerInfo->getId(),
                    $currentCustomer
                );
            // check customer has any address ??
            if ($address) {
                foreach ($address as $ind => $val) {
                    $description [] = $val->getDescription();
                    $addressId [] = $val->getId();
                }
                $res = array_combine($addressId, $description);
                $res = json_encode($res);

                return new JsonResponse($res);
            } else {
                $msg = "there is no address";

                return new JsonResponse($msg);
            }
        } else {
            $msg = "there is no address";

            return new Response($msg);
        }
    }

    /**
     * @Route("/calc_shipment_price",name="app_customer_dashboard_shipment_calc_shipment_price")
     * @param Request $request
     * @return Response
     */
    // calculation shipment's price 
    public function calcShipmentPriceAction(Request $request)
    {
        $ownerAddressId = $request->request->get('ownerAddressId');
        $otherAddressId = $request->request->get('otherAddressId');
        $shipmentValue = $request->request->get('shipmentValue');
        $shipmentPickUpTime = $request->request->get('shipmentPickUpTime');
        $shipmentPickUpTime = $this->get("app.jdate_service")
            ->convertToGregorian($shipmentPickUpTime);
        $ownerAddress = $this->getDoctrine()
            ->getRepository("AppBundle:Address")
            ->find($ownerAddressId);
        $otherAddress = $this->getDoctrine()
            ->getRepository("AppBundle:Address")
            ->find($otherAddressId);
        if($otherAddress == null){
            $em = $this->getDoctrine()
                ->getRepository("AppBundle:Address");
            $qb = $em->createQueryBuilder('p')
                ->where('p.customer=:reciverId')
                ->setParameter('reciverId',$otherAddressId)
                ->orderBy('p.id','DESC')
                ->getQuery()
                ->getResult();
            $otherAddress = $this->getDoctrine()
                ->getRepository("AppBundle:Address")
                ->find($qb[0]->getId());
        }
        $shipmentCost = $this->get("app.cost_calculator")
            ->getCost(
                $ownerAddress,
                $otherAddress,
                $shipmentValue,
                $shipmentPickUpTime
            );

        return new Response($shipmentCost);
    }

    /**
     * @Route("/cancel_shipment", name="app_customer_dashboard_shipment_cancel_shipment")
     * @param Request $request
     * @return Response
     */
    public function cancelShipmentAction(Request $request)
    {
        $shipmentId = $request->request->get("id");
        $em = $this->getDoctrine()->getManager();
        $shipment = $this->getDoctrine()
            ->getRepository("AppBundle:Shipment")
            ->find($shipmentId);
        $shipment->setStatus(Shipment::STATUS_ASSIGNMENT_CANCEL);
        $em->persist($shipment);

        $em->flush();

        return new Response("true");
    }

    /**
     * @Route("/fail_shipment", name="app_customer_dashboard_shipment_fail_shipment")
     * @param Request $request
     * @return Response
     */
    public function failShipmentAction(Request $request)
    {
        $shipmentId = $request->request->get("id");
        $failReason = $request->request->get("reason");
        $em = $this->getDoctrine()->getManager();
        $shipment = $this->getDoctrine()
            ->getRepository("AppBundle:Shipment")
            ->find($shipmentId);
        $shipment->setStatus(Shipment::STATUS_ASSIGNMENT_FAIL);
        $shipment->setReason($failReason);

        $em->persist($shipment);
        $em->flush();

        //send message to driver and operator
        $logger = $this->get('logger');
        $logger->info("the shipment failed by customer");

        return new Response("true");
    }

    /**
     * Finds and displays a Shipment entity.
     *
     * @Route("/{id}", name="app_customer_dashboard_shipment_show")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param Shipment $shipment
     * @return Response
     */
    public function showAction(Request $request, Shipment $shipment)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ValidationCodeType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $shipmentId = $request->request->get("shipment_id");
            $driverExchangeCode = $form->get("exchange_code")->getData();
            $shipmentAssignment = $this->getDoctrine()
                ->getRepository("AppBundle:ShipmentAssignment")
                ->findBy(
                    [
                        'shipment' => $shipmentId,
                        'driverExchangeCode' => $driverExchangeCode
                    ]
                )
            ;
            if ($shipmentAssignment) {
                $reciverExchangeCode = $shipmentAssignment[0]->getReciverExchangeCode();
                // send code via sms to reciver
                $logger = $this->get("logger");
                $logger->info($reciverExchangeCode." sent to reciver customer");
                $shipmentAssignment[0]->getShipment()
                    ->setStatus(Shipment::STATUS_PICKED_UP);

                $em ->persist($shipmentAssignment[0]);
                $em->flush();

                return new JsonResponse(true);
            } else {
                return new JsonResponse(false);
            }

        }
        $conn = r\connect('localhost', '28015', 'roapp', '09126354397');
        $t = r\table('shipment')
            ->filter(
                [
                    'shipment_id' => $shipment->getId(),
                ]
            )
            ->run($conn);
        /** @var \ArrayObject $current */
        $current = $t->current();
        $trackingToken = $current->getArrayCopy()['tracking_token'];

        $deleteForm = $this->createDeleteForm($shipment);
        return $this->render('customer/dashboard/shipment/show.html.twig', array(
            'shipment' => $shipment,
            'tracking_token' =>$trackingToken,
            'delete_form' => $deleteForm->createView(),
            'form' => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Shipment entity.
     *
     * @Route("/{id}/edit", name="app_customer_dashboard_shipment_edit")
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

            return $this->redirectToRoute('app_customer_dashboard_shipment_edit', array('id' => $shipment->getId()));
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
     * @Route("/{id}", name="app_customer_dashboard_shipment_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Shipment $shipment
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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

        return $this->redirectToRoute('app_customer_dashboard_shipment_index');
    }

    /**
     * @Route("/load_map",name="app_customer_dashboard_shipment_load_map")
     * @param Request $request
     * @return JsonResponse
     */
    public function loadMapAction (Request $request) {

        $conn = r\connect('localhost', '28015', 'roapp', '09126354397');
        $result = r\table('shipment')
            ->filter(
                [
                    'tracking_token' => $request->get('token')
                ]
            )
            ->run($conn);
        /** @var \ArrayObject $current */
        $current = $result->current();
        $id = $current->getArrayCopy()['id'];
        $cursor = r\table('driver_location')
            ->filter(
                [
                    'shipment_id' => $id,
                ]
            )
            ->limit(4)
            ->run($conn);

        $last_location = r\table('driver_location')
            ->filter(
                [
                    'shipment_id' => $id,
                ]
            )
            ->orderBy(r\desc('date_time'))
            ->limit(1)
            ->run($conn);

        $counter = 0;
        foreach ($cursor as $value) {
            $output[$counter]['lat'] = $value->getArrayCopy()['lat'];
            $output[$counter]['lng'] = $value->getArrayCopy()['lng'];
            $output[$counter]['lastLat'] = $last_location[0]->getArrayCopy()['lat'];
            $output[$counter]['lastLng'] = $last_location[0]->getArrayCopy()['lng'];
            $counter = $counter+1;
        }

        return new JsonResponse($output );
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
            ->setAction($this->generateUrl('app_customer_dashboard_shipment_delete', array('id' => $shipment->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
