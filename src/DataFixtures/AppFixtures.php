<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');


        for($i = 1; $i <= 30; $i++)
        {
            $ad = new Ad();
            $title = $faker->sentence();
            $introduction = $faker->paragraph(2);
            $content = '<p>'.join('</p><p>', $faker->paragraphs(5)).'</p>';

            $ad->setTitle($title)
                ->setCoverImage('https://picsum.photos/1000/350')
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(rand(40,200))
                ->setRooms(rand(1,5));
            
            $manager->persist($ad);

        }


        $manager->flush();
    }
}
