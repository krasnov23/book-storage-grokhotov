<?php

namespace App\DataFixtures;


use App\Entity\BookCategoryEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $category1 = new BookCategoryEntity();
        $category1->setLevel(1);
        $category1->setTitle('Категория 3');
        $manager->persist($category1);

        $category2 = new BookCategoryEntity();
        $category2->setLevel(1);
        $category2->setTitle('Категория 4');
        $manager->persist($category2);

        for ($i=0;$i <= 3;$i++)
        {
            $category = new BookCategoryEntity();
            $category->setLevel(2);
            $category->setTitle("Категория 3.$i");
            $category->setParentCategory($category1);
            $manager->persist($category);
        }

        $manager->flush();
    }
}
