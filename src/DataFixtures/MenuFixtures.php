<?php

namespace App\DataFixtures;

use App\Entity\Menu;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class MenuFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $menuArr = array(
            array('name' => 'users', 'display_name' => 'Kullanıcılar', 'order_index' => 1, 'router_link' => 'kullanicilar', 'icon_class' => 'fa-users'),
            array('name' => 'parameters', 'display_name' => 'Parametreler', 'order_index' => 3, 'router_link' => 'parametreler', 'icon_class' => 'fa-folder'),
            array('name' => 'parameter_types', 'display_name' => 'Parametre Türleri', 'order_index' => 2, 'router_link' => 'parametre-turleri', 'icon_class' => 'fa-folder-open'),
            array('name' => 'products', 'display_name' => 'Ürünler', 'order_index' => 6, 'router_link' => 'urunler', 'icon_class' => 'fa-barcode'),
            array('name' => 'roles', 'display_name' => 'Roller', 'order_index' => 4, 'router_link' => 'roller', 'icon_class' => 'fa-address-book'),
            array('name' => 'menus', 'display_name' => 'Menüler', 'order_index' => 5, 'router_link' => 'menuler', 'icon_class' => 'fa-bars')
        );

        foreach ($menuArr as $menuItem) {
            $menu = new Menu();
            $menu->setName($menuItem['name']);
            $menu->setDisplayName($menuItem['display_name']);
            $menu->setRouterLink($menuItem['router_link']);
            $menu->setOrderIndex($menuItem['order_index']);
            $menu->setIconClass($menuItem['icon_class']);
            $manager->persist($menu);
        }

        $manager->flush();
    }
}