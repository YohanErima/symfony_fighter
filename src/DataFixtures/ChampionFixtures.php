<?php

namespace App\DataFixtures;

use App\Entity\Champion;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ChampionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $championsName = ["Batman", "Superman", "Hulk", "Iron Man", "saitama", "Goku", "Vegeta","Naruto", "Luffy","Zoro"];
        foreach($championsName as $championName){
            $champion = new Champion();
            $champion->setName($championName)
                ->setPv(mt_rand(50, 1500))
                ->setPower(mt_rand(20, 500));
            $manager->persist($champion);
        }
       

        $manager->flush();
    }
}
