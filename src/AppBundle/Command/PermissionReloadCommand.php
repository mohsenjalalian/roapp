<?php

namespace AppBundle\Command;

use AppBundle\Annotation\Permission;
use AppBundle\Annotation\Permissions;
use AppBundle\Entity\PermissionScope;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\ClassLoader\UniversalClassLoader;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Doctrine\ORM\NoResultException;
use AppBundle\Entity\Permission as PermissionEntity;
use AppBundle\Entity\Person;

/**
 * Class PermissionReloadCommand
 * @package AppBundle\Command
 */
class PermissionReloadCommand extends ContainerAwareCommand
{
    /**
     * @var []
     */
    private $permissions;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:permission:reload')
            ->setDescription('Reload Permissions');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $rootDir = $this->getContainer()->get('kernel')->getRootDir();
        $path = $rootDir.'/../src/AppBundle/Entity';
        $finder = new Finder();
        $finder->files()->in($path);
        $scopes = $this->getContainer()->get('doctrine')->getManager()->getClassMetadata(Person::class)->subClasses;

        $this->updateScopes($scopes);

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            if (strstr($file->getBasename(), '~')) {
                continue;
            }

            $class = 'AppBundle\\Entity\\'.$file->getBasename('.php');
            $annotation = $this->getContainer()->get('annotation_reader')->getClassAnnotation(new \ReflectionClass($class), Permissions::class);
            if (!$annotation) {
                continue;
            }

            /** @var Permission $annotation */
            $this->permissions[] = [
                'class' => $class,
                'annotation' => $annotation,
            ];
        }

        /** @var Permissions $permissionsAnnotation */
        foreach ($this->permissions as $permissionsAnnotationArray) {
            $class = $permissionsAnnotationArray['class'];
            /** @var Permissions $permissionsAnnotation */
            $permissionsAnnotation = $permissionsAnnotationArray['annotation'];

            /** @var Permission $permission */
            foreach ($permissionsAnnotation->getPermissions() as $permission) {
                try {
                    /** @var EntityManager $em */
                    $permissionEntity = $entityManager
                        ->getRepository('AppBundle:Permission')
                        ->createQueryBuilder('permission')
                        ->where('permission.subjectClass = :class')->setParameter('class', $class)
                        ->andWhere('permission.name = :name')->setParameter('name', $permission->getMappedConst())
                        ->getQuery()
                        ->getSingleResult();
                } catch (NoResultException $e) {
                    $permissionEntity = new PermissionEntity();
                    $permissionEntity->setName($permission->getMappedConst());
                    $permissionEntity->setSubjectClass($class);
                }

                $permissionEntity->setLabel($permission->getLabel());

                if (count(array_intersect($permission->getScopes(), $scopes)) != count($permission->getScopes())) {
                    throw new \Exception("Permission scopes are not valid.");
                }

                foreach ($permission->getScopes() as $scope) {
                    $permissionScopeEntity = $entityManager
                        ->getRepository('AppBundle:PermissionScope')
                        ->createQueryBuilder('permission_scope')
                        ->where('permission_scope.name = :name')->setParameter('name', $scope)
                        ->getQuery()
                        ->getSingleResult();

                    $permissionEntity->addScope($permissionScopeEntity);
                }

                $permissionEntity->setType($permission->getType());

                $entityManager->persist($permissionEntity);
                $entityManager->flush();
            }
        }
    }

    /**
     * @param array $scopes
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function updateScopes($scopes)
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        foreach ($scopes as $scope) {
            try {
                /** @var EntityManager $em */
                $permissionScopeEntity = $entityManager
                    ->getRepository('AppBundle:PermissionScope')
                    ->createQueryBuilder('permission_scope')
                    ->where('permission_scope.name = :name')->setParameter('name', $scope)
                    ->getQuery()
                    ->getSingleResult();
            } catch (NoResultException $e) {
                $permissionScopeEntity = new PermissionScope();
                $permissionScopeEntity->setName($scope);
                $entityManager->persist($permissionScopeEntity);
                $entityManager->flush();
            }
        }
    }
}
