<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
    }
    public function getDependencies()
    {
        return array(
            RoleFixtures::class,
            ParameterTypeFixtures::class,
            ParameterFixtures::class,
            UserFixtures::class,
            ProductFixtures::class,
            MenuFixtures::class
        );
    }
}
