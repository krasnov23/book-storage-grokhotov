<?php

namespace App\Controller;

use App\Entity\BookEntity;
use App\Form\BookEntityType;
use App\Repository\BookEntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class BookController extends AbstractController
{
    public function __construct(private BookEntityRepository $bookEntityRepository,
                                private SluggerInterface $slugger)
    {
    }

    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        return $this->render('book/main-page.html.twig', [
            'books' => $this->bookEntityRepository->findAll()
        ]);
    }

    #[Route('/user/books/{book}', name: 'app_guest_book_page')]
    public function getBook(BookEntity $book): Response
    {
        return $this->render('book/book-page.html.twig',[
           'book' => $book
        ]);
    }

    #[Route('/admin/books', name: 'app_admin_books')]
    public function admin(): Response
    {
        return $this->render('book/index.html.twig', [
            'books' => $this->bookEntityRepository->findAll()
        ]);
    }

    #[Route('/admin/books/add', name: 'app_admin_books_add')]
    public function addBook(Request $request): Response
    {
        $book = new BookEntity();

        $form = $this->createForm(BookEntityType::class,$book);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid())
        {
            // bookimage - HttpFoundation\UploadedFile
            $bookImage = $form->get('image')->getData();

            if ($bookImage)
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

                $this->addFlash('success','Книга Успешно Добавлена');

                return $this->redirectToRoute('app_admin_books');
            }
        }

        return $this->render('book/add-book.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/admin/books/edit/{book}', name: 'app_admin_books_edit')]
    public function editBook(Request $request,BookEntity $book): Response
    {
        $form = $this->createForm(BookEntityType::class,$book);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid())
        {
            // bookimage - HttpFoundation\UploadedFile
            $bookImage = $form->get('image')->getData();

            if ($bookImage)
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

                $this->addFlash('success','Книга Успешно Изменена');

                return $this->redirectToRoute('app_admin_books');
            }
        }

        return $this->render('book/edit-book.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/admin/books/delete/{book}', name: 'app_admin_books_delete')]
    public function deleteBook(BookEntity $book): Response
    {
        $this->bookEntityRepository->remove($book,true);

        $this->addFlash('success','Книга Успешно Удалена');

        return $this->redirectToRoute('app_admin_books');
    }


}
