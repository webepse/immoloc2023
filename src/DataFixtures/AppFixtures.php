<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Image;
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

            // gestion de l'image de l'annonce
            for($j=1; $j<= rand(2,5); $j++)
            {
                $image = new Image();
                $image->setUrl('https://picsum.photos/200/200')
                    ->setCaption($faker->sentence())
                    ->setAd($ad);
                $manager->persist($image);    
            }

        }


        $manager->flush();
    }
}
