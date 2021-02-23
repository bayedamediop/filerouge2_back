<?php

namespace App\DataFixtures;

use App\Entity\Profils;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $repo;
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder )
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $profils =["ADMIN" ,"CAISSIER" ,"PARTENAIRE" ,"ADMIN_PARTENAIRE"];
        foreach ($profils as $key => $libelle) {
            $profil =new Profils() ;
            $profil ->setLibelle ($libelle );
            $manager ->persist ($profil );

                $user = new User() ;
                $user ->setProfils ($profil );

//Génération des Users
                $password = $this ->encoder ->encodePassword ($user, 'diop' );
                $user ->setPassword ($password );
                $user ->setEmail($faker->email) ;
            $user ->setEmail($faker->email) ;
            $user ->setPrenom($faker->lastName) ;
            $user ->setNom($faker->firstName) ;
            $user ->setPhone($faker->phoneNumber) ;
            $user ->setCni(23245333737) ;
            $user ->setAdresse($faker->city) ;
            $user ->setArchivage(1) ;
                $manager ->persist ($user);
            }
            $manager ->flush();
        }

}

