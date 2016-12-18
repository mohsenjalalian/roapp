<?php

namespace AppBundle\Controller\Customer\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\BusinessType;
use AppBundle\Entity\BusinessUnit;
use AppBundle\Entity\Customer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BusinessUnitController
 * @package AppBundle\Controller
 * @Route("/businessunit")
 */
class BusinessUnitController extends Controller
{
    /**
     * Displays a form to edit an existing businessUnit entity.
     *
     * @Route("/edit", name="app_customer_dashboard_businessunit_edit")
     * @param Request $request
     * @Method({"GET", "POST"})
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request)
    {
        /** @var Customer $customer */
        $customer = $this->getUser();
        $businessUnit = $customer->getBusinessUnit();
        $editForm = $this->createForm($businessUnit->getBusinessType()->getBusinessUnitForm(), $businessUnit);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_customer_dashboard_businessunit_edit');
        }

        return $this->render('operator/dashboard/businessunit/edit.html.twig', [
            'businessUnit' => $businessUnit,
            'edit_form' => $editForm->createView(),
        ]);
    }
}
