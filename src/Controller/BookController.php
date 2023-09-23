<?php

namespace App\Controller;

use App\Entity\BookEntity;
use App\Form\BookEntityType;
use App\Repository\BookCategoryEntityRepository;
use App\Repository\BookEntityRepository;
use App\Service\BookEntityService;
use Doctrine\Common\Collections\Criteria;
use ErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class BookController extends AbstractController
{
    public function __construct(private BookEntityRepository $bookEntityRepository,
                                private BookEntityService    $bookService,
                                private SluggerInterface     $slugger,
                                private BookCategoryEntityRepository $categoryEntityRepository)
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


    #[Route('/books/{book}', name: 'app_guest_book_page')]
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

            try{
                $categoriesFromForm = $request->request->all()['category-for-book'];
                $bookCategories = $this->categoryEntityRepository->findBy(['title' => $categoriesFromForm]);

                foreach ($bookCategories as $category)
                {
                    $book->addCategory($category);
                }
            }catch (ErrorException)
            {
                $categoriesFromForm = null;
            }


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

        return $this->render('book/add-book.html.twig', [
            'form' => $form->createView(),
            'categories' => $this->categoryEntityRepository->findBy([],['level' => Criteria::ASC])]);
    }

    #[Route('/admin/books/edit/{book}', name: 'app_admin_books_edit')]
    public function editBook(Request $request, BookEntity $book): Response
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

        return $this->render('book/edit-book.html.twig', ['form' => $form->createView(),
            'categories' => $this->categoryEntityRepository->findBy([],['level' => Criteria::ASC])]);
    }

    #[Route('/admin/books/delete/{book}', name: 'app_admin_books_delete')]
    public function deleteBook(BookEntity $book): Response
    {
        $this->bookEntityRepository->remove($book,true);

        $this->addFlash('success','Книга Успешно Удалена');

        return $this->redirectToRoute('app_admin_books');
    }


}
