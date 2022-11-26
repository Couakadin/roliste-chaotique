<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class MenuBuilder
{
    /**
     * Add any other dependency you need...
     */
    public function __construct(private readonly FactoryInterface $factory) {}

    public function createMainMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('Home', ['route' => 'home.index']);
        // ... add more children

        return $menu;
    }
}