<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\{Fixture,FixtureGroupInterface};
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    public const USER_NB_TUPLE = 20;

    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    /** @throws Exception */
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= self::USER_NB_TUPLE; $i++) {
            $user = (new User())
                ->setFirstName("Firstname $i")
                ->setLastName("Lastname $i")
                ->setGuestNumber(random_int(0,5))
                ->setEmail("email.$i@studi.fr")
                ->setCreatedAt(new DateTimeImmutable());

            $user->setPassword($this->passwordHasher->hashPassword($user, "password.$i"));

            $manager->persist($user);
        }
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['independent', 'user'];
    }
}
// with faker version 1.9.2 > composer require fakerphp/faker
// <?php
// namespace App\DataFixtures;
// use App\Entity\Restaurant;
// use DateTimeImmutable;
// use Doctrine\Bundle\FixturesBundle\Fixture;
// use Doctrine\Persistence\ObjectManager;
// use Exception;
// use Faker ;
// class RestaurantFixtures extends Fixture
// {
//     /** @throws Exception */
//     public function load(ObjectManager $manager): void
//     {
//         $faker = Faker\Factory::create();
//         for ($i = 1; $i <= 20; $i++) {
//             $restaurant = (new Restaurant())
//                 ->setName($faker->company())
//                 ->setDescription($faker->text())
//                 ->setAmOpeningTime([])
//                 ->setPmOpeningTime([])
//                 ->setMaxGuest(random_int(10,50))
//                 ->setCreatedAt(new DateTimeImmutable());
//             $manager->persist($restaurant);
//             $this->addReference("restaurant" . $i, $restaurant);
//         }
//         $manager->flush();
//     }
// }
