<?php

namespace App\Service;

use App\Entity\BookEntity;
use App\Repository\BookEntityRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminBookService
{
    public function __construct(private BookEntityRepository $bookEntityRepository,
                                private SluggerInterface $slugger)
    {
    }


    public function addBook(FormInterface $form,BookEntity $book)
    {
        $bookImage = $form->get('image')->getData();

        /*if ($bookImage)
        {
            $originalFileName = pathinfo($bookImage->getClientOriginalName(),PATHINFO_FILENAME);

            $sluggerFileName = $this->slugger->slug($originalFileName);

            $newFileName = $sluggerFileName . '-' . uniqid() . '.' . $bookImage->guessExtension();

            try{
                $bookImage->move(
                    $this->container->get('parameter_bag')->get('books_image_directory'),
                    $newFileName
                );
            } catch (FileException $exception)
            {
            }

            $book->setImage($newFileName);

            $this->bookEntityRepository->save($book,true);

            return ['success','Книга добавлена','app_admin_books'];
        }*/

    }

}