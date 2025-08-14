<?php

namespace App\DataFixtures;

use App\Entity\Serie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SerieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $faker = \Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 1000; $i++) {
            $serie = new Serie();
            $serie->setName($faker->realText(30,true))
                ->setOverview($faker->paragraph(2))
                ->setGenre($faker->randomElement(['Action', 'Drama', 'Comedy', 'Sci-Fi', 'Fantasy']))
                ->setStatus($faker->randomElement(['Returning', 'Ended', 'Canceled']))
                ->setVote($faker->randomFloat(2, 0, 10))
                ->setPopularity($faker->randomFloat(2, 200, 900))
                ->setFirstAirDate($faker->dateTimeBetween('-20 years', '-1 month'))
                ->setDateCreated(new \DateTime())
            ;

            if($serie->getStatus() === 'Returning') {
                $serie->setLastAirDate($faker->dateTimeBetween($serie->getFirstAirDate(), '- 1day'));
            }

            $manager->persist($serie);

        }

        $manager->flush();
    }
}
