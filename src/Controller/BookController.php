<?php

namespace App\Controller;

use App\Entity\BookCategoryEntity;
use App\Entity\BookEntity;
use App\Form\BookEntityType;
use App\Repository\BookCategoryEntityRepository;
use App\Repository\BookEntityRepository;
use App\Service\BookEntityService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\PersistentCollection;
use ErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class BookController extends AbstractController
{
    public function __construct(private BookEntityRepository $bookEntityRepository,
                                private BookEntityService    $bookService,
                                private SluggerInterface     $slugger,
                                private Filesystem $filesystem)
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
            /**
             * @var BookEntity $newBook
             */
            $newBook = $form->getData();

            $categories = $form->get('categories')->getData()->toArray();

            foreach ($categories as $category)
            {
                /**
                 * @var BookCategoryEntity $category
                 */
                //$category->addBook($newBook);

                $newBook->addCategory($category);
            }

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

                $newBook->setImage($newFileName);
            }

            $this->bookEntityRepository->save($newBook,true);

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
            /**
             * @var PersistentCollection $categories
             */
            $categoriesData = $form->get('categories')->getData();

            $categories = $categoriesData->toArray();

            foreach ($categories as $category)
            {
                $book->addCategory($category);
            }

            /** @var UploadedFile $bookImage */
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

                $this->filesystem->remove($this->getParameter('books_image_directory') . DIRECTORY_SEPARATOR . $bookOldImage);

                $book->setImage($newFileName);

            }else{
                $book->setImage($bookOldImage);
            }

            $this->bookEntityRepository->save($book,true);

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
        $this->bookEntityRepository->remove($book,true);

        $this->addFlash('success','Книга Успешно Удалена');

        return $this->redirectToRoute('app_admin_books');
    }

    #[Route('/admin/books/delete-image/{book}', name: 'app_admin_books_image_delete')]
    public function deleteBookPhoto(BookEntity $book): Response
    {
        $bookImage = $book->getImage();

        if ($bookImage)
        {
            $this->filesystem->remove($this->getParameter('books_image_directory') . DIRECTORY_SEPARATOR . $bookImage);
        }

        $book->setImage(null);

        $this->bookEntityRepository->save($book,true);

        $this->addFlash('success','Картинка книги успешно удалена');

        return $this->redirectToRoute('app_admin_books');
    }


}
