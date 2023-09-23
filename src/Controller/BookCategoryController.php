<?php

namespace App\Controller;

use App\Entity\BookCategoryEntity;
use App\Repository\BookCategoryEntityRepository;
use App\Service\CategoryEntityService;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookCategoryController extends AbstractController
{
    public function __construct(private BookCategoryEntityRepository $bookCategoryRepository,
                                private CategoryEntityService $categoryEntityService)
    {
    }

    #[Route('/categories/all-categories', name: 'app_book_category_list')]
    public function index(): Response
    {
        return $this->render('book_category/get-categories.html.twig', [
            'categories' => $this->bookCategoryRepository->getAllHighLevelCategories(1),
        ]);
    }

    #[Route('/categories/show-category/{categoryId}', name: 'app_book_category_show_one')]
    public function showCategory(int $categoryId): Response
    {
        return $this->render('book_category/get-category.html.twig',[
           'category' => $this->bookCategoryRepository->getBooksAndSubcategoryByCategory($categoryId)
        ]);
    }

    #[Route('/admin/categories', name: 'app_book_category_list_admin')]
    public function manageCategories(): Response
    {
        return $this->render('book_category/manage-categories.html.twig',[
           'categories' => $this->bookCategoryRepository->findBy([],['id' => Criteria::DESC])
        ]);
    }


    #[Route('/admin/add-category', name: 'app_book_add_category')]
    public function addCategory(Request $request): Response
    {

        if ('POST' === $request->getMethod()) {
            $addCategory = $this->categoryEntityService->addCategory($request);
            $this->addFlash($addCategory[0],$addCategory[1]);
            $this->redirectToRoute($addCategory[2]);
        }

        return $this->render('book_category/add-category.html.twig',
        [
         'categories' => $this->bookCategoryRepository->getAllHighLevelCategories(1)
         ]);
    }

    #[Route('/admin/edit-category/{category}', name: 'app_book_edit_category')]
    public function editCategory(BookCategoryEntity $category,Request $request): Response
    {

        if ('POST' === $request->getMethod())
        {
            $editCategory = $this->categoryEntityService->editCategory($request,$category);
            $this->addFlash($editCategory[0],$editCategory[1]);
            $this->redirectToRoute($editCategory[2]);
        }

        return $this->render('book_category/edit-category.html.twig',[
            'categories' => $this->bookCategoryRepository->getAllHighLevelCategories(1),
            'currentCategory' => $category
        ]);
    }

    #[Route('/admin/delete-category/{category}', name: 'app_book_delete_category')]
    public function deleteCategory(BookCategoryEntity $category): Response
    {
        $this->bookCategoryRepository->remove($category,true);
        $this->addFlash('success','категория успешно удалена');

        return $this->redirectToRoute('app_book_category_list_admin');
    }











}
