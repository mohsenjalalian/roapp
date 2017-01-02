<?php

namespace AppBundle\Controller\Customer\Dashboard;

use AppBundle\Entity\Customer;
use AppBundle\Form\Customer\Dashboard\CustomerType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CustomerController
 *
 * @Route("/customer")
 * @Security("has_role('ROLE_CUSTOMER')")
 * @package AppBundle\Controller\Customer\Dashboard
 */
class CustomerController extends Controller
{
    /**
     * @Route("/{id}", name="app_customer_dashboard_customer_index")
     * @Method("GET")
     * @param Customer $customer
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Customer $customer)
    {
        return $this->render('customer/dashboard/customer/index.html.twig', [
            'customer' => $customer,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_customer_dashboard_customer_edit")
     * @param Request  $request
     * @param Customer $customer
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Customer $customer)
    {
        $editForm = $this->createForm(
            CustomerType::class,
            $customer,
            [
                'attr' => [
                    'id' => 'profile_edit_form_customer',
                ],
            ]
        );
        $editForm->handleRequest($request);
        if ($editForm->isValid() && $editForm->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $currentPass = $editForm->get('currentPassword')->getData();
            // check field current password filled by user
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

        return $this->render('customer/dashboard/customer/edit.html.twig', [
            'customer' => $customer,
            'edit_form' => $editForm->createView(),
        ]);
    }
}
