<?php

namespace AppBundle\Controller\Operator\Dashboard;

use AppBundle\Entity\Operator;
use AppBundle\Form\Operator\Dashboard\OperatorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Operator controller.
 *
 * @Route("/operator")
 * @Security("has_role('ROLE_USER')")
 */
class OperatorController extends Controller
{
    /**
     * Lists all operator entities.
     *
     * @Route("/", name="app_operator_dashboard_operator_index")
     * @Method("GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $query = $this->getDoctrine()
            ->getRepository('AppBundle:Operator')
            ->createQueryBuilder('op')
            ->orderBy('op.id', 'Asc')
            ->getQuery()
        ;
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render(
            'operator/dashboard/operator/index.html.twig',
            [
                'pagination' => $pagination,
            ]
        );
    }

    /**
     * Creates a new operator entity.
     *
     * @Route("/new", name="app_operator_dashboard_operator_create")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $operator = new Operator();
        $form = $this->createForm(OperatorType::class, $operator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($operator);
            $em->flush($operator);

            return $this->redirectToRoute('app_operator_dashboard_operator_show', array('id' => $operator->getId()));
        }

        return $this->render('operator/dashboard/operator/new.html.twig', array(
            'operator' => $operator,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a operator entity.
     *
     * @Route("/{id}", name="app_operator_dashboard_operator_show")
     * @Method("GET")
     * @param Operator $operator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Operator $operator)
    {
        $deleteForm = $this->createDeleteForm($operator);

        return $this->render('operator/dashboard/operator/show.html.twig', array(
            'operator' => $operator,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing operator entity.
     *
     * @Route("/{id}/edit", name="app_operator_dashboard_operator_edit")
     * @Method({"GET", "POST"})
     * @param Request  $request
     * @param Operator $operator
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Operator $operator)
    {
        $deleteForm = $this->createDeleteForm($operator);
        $editForm = $this->createForm(OperatorType::class, $operator);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_operator_dashboard_operator_edit', array('id' => $operator->getId()));
        }

        return $this->render('operator/dashboard/operator/edit.html.twig', array(
            'operator' => $operator,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a operator entity.
     *
     * @Route("/{id}", name="app_operator_dashboard_operator_delete")
     * @Method("DELETE")
     * @param Request  $request
     * @param Operator $operator
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Operator $operator)
    {
        $form = $this->createDeleteForm($operator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($operator);
            $em->flush($operator);
        }

        return $this->redirectToRoute('app_operator_dashboard_operator_index');
    }

    /**
     * Creates a form to delete a operator entity.
     *
     * @param Operator $operator The operator entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Operator $operator)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_operator_dashboard_operator_delete', array('id' => $operator->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
