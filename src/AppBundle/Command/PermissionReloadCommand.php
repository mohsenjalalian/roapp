<?php

namespace AppBundle\Command;

use AppBundle\Annotation\Permission;
use AppBundle\Annotation\Permissions;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\ClassLoader\UniversalClassLoader;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Doctrine\ORM\NoResultException;
use AppBundle\Entity\Permission as PermissionEntity;

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
                $doctrine = $this->getContainer()->get('doctrine');

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
                $permissionEntity->setScope($permission->getScope());
                $permissionEntity->setType($permission->getType());

                $entityManager->persist($permissionEntity);
                $entityManager->flush();
            }
        }
    }
}
