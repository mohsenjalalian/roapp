<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Rate;
use AppBundle\Entity\Shipment;
use AppBundle\Form\RateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RateController
 * @Route("/rate")
 * @package AppBundle\Controller
 */
class RateController extends Controller
{
    /**
     * @param Request  $request
     * @param Shipment $shipment
     * @Route("/register/{id}", name="app_rate_register")
     * @Method("POST")
     * @return JsonResponse|Response
     */
    public function registerAction(Request $request, Shipment $shipment)
    {
        $rate = new Rate();

        $rateForm = $this->createForm(
            RateType::class,
            $rate,
            [
                'action' => $this->generateUrl('app_rate_register', ['id' => $shipment->getId()]),
                'attr' => ['id' => 'register_rate_form'],
            ]
        );
        $rateForm->handleRequest($request);
        if ($rateForm->isSubmitted() && $rateForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $rate->setCreator($shipment->getOtherAddress()->getCustomer());
            $rate->setShipment($shipment);
            $em->persist($rate);
            $em->flush();

            return new JsonResponse(Response::HTTP_OK);
        }

        return $this->render('shipment/register_rate.html.twig', [
            'shipment' => $shipment,
            'rate_form' => $rateForm->createView(),
        ]);
    }
}
