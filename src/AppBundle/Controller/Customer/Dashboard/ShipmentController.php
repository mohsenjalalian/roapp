<?php

namespace AppBundle\Controller\Customer\Dashboard;

use AppBundle\Entity\Address;
use AppBundle\Entity\Customer;
use AppBundle\Form\Customer\Dashboard\AddressType;
use AppBundle\Form\Customer\Dashboard\ShipmentType;
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
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $shipments = $em->getRepository('AppBundle:Shipment')->findAll();

        return $this->render(':customer/dashboard/shipment:index.html.twig', array(
            'shipments' => $shipments,
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
     * Finds and displays a Shipment entity.
     *
     * @Route("/{id}", name="app_customer_dashboard_shipment_show")
     * @Method("GET")
     */
    public function showAction(Shipment $shipment)
    {
        $deleteForm = $this->createDeleteForm($shipment);

        return $this->render('customer/dashboard/shipment/show.html.twig', array(
            'shipment' => $shipment,
            'delete_form' => $deleteForm->createView(),
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
