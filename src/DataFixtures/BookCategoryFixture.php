<?php

namespace App\DataFixtures;

use App\Entity\BookCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BookCategoryFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist((new BookCategory())->setTitle('Data')->setSlug('data'));
        $manager->persist((new BookCategory())->setTitle('android')->setSlug('android'));
        $manager->persist((new BookCategory())->setTitle('Networking')->setSlug('Networking'));

        $manager->flush();
    }
}
