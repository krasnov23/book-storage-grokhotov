<?php

namespace App\DataFixtures;

use App\Entity\BookEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $book = new BookEntity();
        $book->setName('NameBook1');
        $book->setIsbn('123abc');
        $book->setImage('https://i.ytimg.com/vi/kwUOs6FilIg/maxresdefault.jpg');
        $book->setAmountOfPages(500);
        $manager->persist($book);


        $book2 = new BookEntity();
        $book2->setName('NameBook2');
        $book2->setIsbn('123abc');
        $book2->setImage('https://i.ytimg.com/vi/kwUOs6FilIg/maxresdefault.jpg');
        $book2->setAmountOfPages(500);

        $manager->persist($book2);

        $manager->flush();
    }
}
