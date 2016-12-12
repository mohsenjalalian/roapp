<?php

namespace AppBundle\Controller\Operator\Dashboard;

use AppBundle\Entity\Permission;
use AppBundle\Entity\PermissionScope;
use AppBundle\Form\Operator\Dashboard\ScopePermissionsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PermissionScopeController
 * @package AppBundle\Controller\Operator\Dashboard
 * @Route("/permission_scope")
 */
class PermissionScopeController extends Controller
{
    /**
     * @param PermissionScope                           $permissionScope
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/{permissionScope}/permission", name="app_operator_dashboard_permission_scope_permission")
     */
    public function permissionAction(PermissionScope $permissionScope, Request $request)
    {
        $permissions = new \stdClass();
        $permissions->permissions = $permissionScope->getPermissions()->toArray();
        $subjectClasses = $this->getDoctrine()
            ->getRepository('AppBundle:Permission')
            ->getSubjectClasses($permissionScope);

        foreach ($subjectClasses as &$subjectClass) {
            $subjectClass['permissions'] = $this->getDoctrine()
                ->getRepository('AppBundle:Permission')
                ->getPermissionsPerScope($permissionScope, $subjectClass);
        }

        $scopePermissions = new \stdClass();
        $scopePermissions->subjectTypes = $subjectClasses;

        $form = $this->createForm(ScopePermissionsType::class, $scopePermissions)
            ->add('submit', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            /** @var Permission $permission */
            foreach ($scopePermissions->subjectTypes as $subjectType) {
                foreach ($subjectType['permissions'] as $permission) {
                    $em->persist($permission);
                }
            }
            $em->flush();

            return $this->redirectToRoute('app_operator_dashboard_permission_scope_permission', [
                'permissionScope' => $permissionScope->getId(),
            ]);
        }

        return $this->render(':operator/dashboard/permission_scope:permission.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
