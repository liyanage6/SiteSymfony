<?php

namespace NL\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use NL\UserBundle\Entity\User;

class LoadUser implements FixtureInterface
{
    public function load (ObjectManager $manager)
    {
        $listNames = ['Nicholas', 'Pele', 'Maradonna'];

        foreach ($listNames as $name) {
            $user = new User;

            $user->setUsername($name);
            $user->setPassword($name);

            $user->setSalt('');
            $user->setRoles(['ROLE_USER']);

            $manager->persist($user);
        }
        $manager->flush();
    }
}