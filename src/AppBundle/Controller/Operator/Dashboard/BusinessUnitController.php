<?php

namespace AppBundle\Controller\Operator\Dashboard;

use AppBundle\Entity\BusinessUnit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BusinessUnitController
 * @package AppBundle\Controller
 *
 * @Route("/businessunit")
 */
class BusinessUnitController extends Controller
{
    /**
     * Lists all businessUnit entities.
     *
     * @Route("/", name="businessunit_index")
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $businessUnits = $em->getRepository('AppBundle:BusinessUnit')->findAll();

        return $this->render('operator/dashboard/businessunit/index.html.twig', [
            'businessUnits' => $businessUnits,
        ]);
    }

    /**
     * Creates a new businessUnit entity.
     *
     * @Route("/new", name="businessunit_new")
     * @param Request $request
     * @Method({"GET", "POST"})
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $businessUnit = new Businessunit();
        $form = $this->createForm('AppBundle\Form\BusinessUnitType', $businessUnit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($businessUnit);
            $em->flush();

            return $this->redirectToRoute('businessunit_show', ['id' => $businessUnit->getId()]);
        }

        return $this->render('operator/dashboard/businessunit/new.html.twig', [
            'businessUnit' => $businessUnit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a businessUnit entity.
     *
     * @Route("/{id}", name="businessunit_show")
     * @param BusinessUnit $businessUnit
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(BusinessUnit $businessUnit)
    {
        $deleteForm = $this->createDeleteForm($businessUnit);

        return $this->render('operator/dashboard/businessunit/show.html.twig', [
            'businessUnit' => $businessUnit,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing businessUnit entity.
     *
     * @Route("/{id}/edit", name="businessunit_edit")
     * @param Request      $request
     * @param BusinessUnit $businessUnit
     * @Method({"GET", "POST"})
     *@return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, BusinessUnit $businessUnit)
    {
        $deleteForm = $this->createDeleteForm($businessUnit);
        $editForm = $this->createForm('AppBundle\Form\Operator\Dashboard\BusinessUnitType', $businessUnit);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('businessunit_edit', ['id' => $businessUnit->getId()]);
        }

        return $this->render('operator/dashboard/businessunit/edit.html.twig', [
            'businessUnit' => $businessUnit,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a businessUnit entity.
     *
     * @Route("/{id}", name="businessunit_delete")
     * @param Request      $request
     * @param BusinessUnit $businessUnit
     * @Method("DELETE")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, BusinessUnit $businessUnit)
    {
        $form = $this->createDeleteForm($businessUnit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($businessUnit);
            $em->flush();
        }

        return $this->redirectToRoute('businessunit_index');
    }

    /**
     * Creates a form to delete a businessUnit entity.
     *
     * @param BusinessUnit $businessUnit The businessUnit entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(BusinessUnit $businessUnit)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('businessunit_delete', ['id' => $businessUnit->getId()]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
