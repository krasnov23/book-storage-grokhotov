<?php

namespace App\Controller;

use App\Entity\BookEntity;
use App\Form\BookEntityType;
use App\Repository\BookEntityRepository;
use App\Service\BookEntityService;
use App\Service\BookImageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    public function __construct(private BookEntityRepository $bookEntityRepository,
                                private BookEntityService    $bookService,
                                private BookImageService     $bookImageService)
    {
    }

    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_books_by_pages');
    }

    #[Route('/page-books/{pageNumber<\d+>?1}', name: 'app_books_by_pages')]
    public function getBookByPage(int $pageNumber): Response
    {
        $booksPagination = $this->bookService->getBooksByPage($pageNumber);

        return $this->render('book/main-page.html.twig',[
            'books' => $booksPagination[0],
            'currentPage' => $pageNumber,
            'lastPage' => $booksPagination[1]
        ]);
    }


    #[Route('/books/{bookId}', name: 'app_guest_book_page')]
    public function getBook(int $bookId): Response
    {
        $book = $this->bookEntityRepository->getBookWithCategories($bookId);

        return $this->render('book/book-page.html.twig',[
            'book' => $book,
            'sameCategoriesBooks' => $this->bookEntityRepository->getBookWithSameCategories($book->getCategories()->toArray())
        ]);
    }

    #[Route('/books/{categoryId}/{pageNumber}', name: 'app_guest_book_by_category')]
    public function getBookByCategory(int $categoryId,int $pageNumber): Response
    {
        $books = $this->bookService->getBooksByCategoryAndPages($categoryId,$pageNumber);

        return $this->render('book/book-by-category-page.html.twig',[
            'books' => $books[0],
            'lastPage' => $books[1],
            'currentPage' => $pageNumber,
            'categoryId' => $categoryId
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
            // Отправляем данные формы и загруженную картинку в наш микросервис если такая есть
            $this->bookService->addBook($form->getData(),$form->get('image')->getData());

            $this->addFlash('success','Книга Успешно Добавлена');

            return $this->redirectToRoute('app_admin_books');
        }

        return $this->render('book/add-book.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/books/edit/{book}', name: 'app_admin_books_edit')]
    public function editBook(Request $request, BookEntity $book): Response
    {
        $form = $this->createForm(BookEntityType::class,$book);
        $bookOldImage = $book->getImage();

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid())
        {
            $this->bookService->editBook($form->getData(),$bookOldImage,$form->get('image')->getData());

            $this->addFlash('success','Книга Успешно Изменена');

            return $this->redirectToRoute('app_admin_books');
        }

        return $this->render('book/edit-book.html.twig', [
            'form' => $form->createView(),
            'book' => $book
            ]);
    }

    #[Route('/admin/books/delete/{book}', name: 'app_admin_books_delete')]
    public function deleteBook(BookEntity $book): Response
    {
        $book = $this->bookImageService->deleteBookImage($book);

        $this->bookEntityRepository->remove($book,true);

        $this->addFlash('success','Книга Успешно Удалена');

        return $this->redirectToRoute('app_admin_books');
    }

    #[Route('/admin/books/delete-image/{book}', name: 'app_admin_books_image_delete')]
    public function deleteBookPhoto(BookEntity $book): Response
    {
        $book = $this->bookImageService->deleteBookImage($book);

        $this->bookEntityRepository->save($book,true);

        $this->addFlash('success','Картинка книги успешно удалена');

        return $this->redirectToRoute('app_admin_books');
    }


}
