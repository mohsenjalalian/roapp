<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/**
 * Class MenuBuilder
 * @package AppBundle\Menu
 */
class MenuBuilder
{
    private $factory;
    /**
     * @var Request
     */
    private $request;
    /**
     * @var AuthorizationChecker
     */
    private $auth;
    /**
     * @var TokenStorage
     */
    private $security;
    /**
     * @param FactoryInterface     $factory
     * @param Container            $container
     * @param AuthorizationChecker $auth
     * @param TokenStorage         $security
     *
     * Add any other dependency you need
     */
    public function __construct(FactoryInterface $factory, Container $container, AuthorizationChecker $auth, TokenStorage $security)
    {
        $this->factory = $factory;
        $this->request = $container->get('request_stack')->getCurrentRequest();
        $this->auth = $auth;
        $this->security = $security;
    }

    /**
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     */
    public function createProfileSidebarMenu(array $options)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'ui secondary vertical pointing menu');
        $menu->addChild('پروفایل', array('route' => 'fos_user_profile_show', 'linkAttributes' => ['class' => 'item']));
        $menu->addChild('ویرایش پروفایل', array('route' => 'fos_user_profile_edit', 'linkAttributes' => ['class' => 'item']));
        $menu->addChild('آگهی ها', array('route' => 'profile_ads', 'linkAttributes' => ['class' => 'item']));
        foreach ($menu->getChildren() as $child) {
            if ($this->request->getRequestUri() == $child->getUri()) {
                $child->setCurrent(true);
            }
        }

        return $menu;
    }

    /**
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     */
    public function createMainMenu(array $options)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'ui large secondary inverted pointing menu');
        $menu->addChild('خانه', array('route' => 'app_customer_dashboard_default_index', 'linkAttributes' => ['class' => 'item']));
        $menu->addChild('جستجوی خانه', array('route' => 'app_customer_dashboard_default_index', 'routeParameters' => ['context' => 'householder'], 'linkAttributes' => ['class' => 'item']));
        $menu->addChild('جستجوی همخانه', array('route' => 'app_customer_dashboard_default_index', 'routeParameters' => ['context' => 'housemate'], 'linkAttributes' => ['class' => 'item']));
        $menu->addChild('user', ['childrenAttributes' => ['class' => 'item right'], 'extras' => ['hideLable' => true]]);
//        if ($this->auth->isGranted('IS_AUTHENTICATED_FULLY')) {
////            $menu['user']->addChild('welcome', ['childrenAttributes' => ['class' => 'item'], 'extras' => ['hideLable' => true]]);
////            $menu['user']['welcome']->addChild($this->security->getToken()->getUser()->getUserName() . ' عزیز، خوش آمدید');
//            $menu['user']->addChild('پروفایل', array('route' => 'app_customer_dashboard_default_index', 'linkAttributes' => ['class' => 'ui primary button']));
//            $menu['user']->addChild('خروج', array('route' => 'app_customer_dashboard_default_index', 'linkAttributes' => ['class' => 'ui button']));
//        } else {
//            $menu['user']->addChild('ورود', array('route' => 'app_customer_dashboard_default_index', 'linkAttributes' => ['class' => 'ui button']));
//            $menu['user']->addChild('ثبت نام', array('route' => 'app_customer_dashboard_default_index', 'linkAttributes' => ['class' => 'ui primary button']));
//        }
        foreach ($menu->getChildren() as $child) {
            if ($this->request->getRequestUri() == $child->getUri()) {
                $child->setCurrent(true);
            }
        }

        return $menu;
    }
}
