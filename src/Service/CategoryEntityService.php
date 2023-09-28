<?php

namespace App\Service;

use App\Entity\BookCategoryEntity;
use App\Repository\BookCategoryEntityRepository;
use ErrorException;
use Symfony\Component\HttpFoundation\Request;

class CategoryEntityService
{
    public function __construct(private BookCategoryEntityRepository $categoryEntityRepository)
    {
    }

    public function addCategory(Request $request)
    {
        $parentCategoryName = $request->request->all()['parent-category-name'];
        $nameOfCategory = $request->request->all()['field-category-name'];

        $categoryWithSameName = $this->categoryEntityRepository->findOneBy(['title' => $nameOfCategory]);

        if ($categoryWithSameName)
        {
            return ['warning','Категория с таким именем уже существует','app_book_category_list_admin'];
        }

        $newCategory = new BookCategoryEntity();

        if ($parentCategoryName)
        {
            $parentCategory = $this->categoryEntityRepository->findOneBy(['title' => $parentCategoryName]);

            $newCategory->setTitle($nameOfCategory);
            $newCategory->setLevel(2);
            $newCategory->setParentCategory($parentCategory);
        }else{
            $newCategory->setTitle($nameOfCategory);
            $newCategory->setLevel(1);
        }

        $this->categoryEntityRepository->save($newCategory,true);

        return ['success','Категория успешно добавлена','app_book_category_list_admin'];
    }

    public function editCategory(Request $request,BookCategoryEntity $bookCategoryEntity)
    {
        try{
            $parentCategoryName = $request->request->all()['parent-category-name'];
        } catch (ErrorException){
            $parentCategoryName = null;
        }

        $nameOfCategory = $request->request->all()['field-category-name'];

        $categoryWithSameName = $this->categoryEntityRepository->findOneBy(['title' => $nameOfCategory]);

        if ($categoryWithSameName and $categoryWithSameName->getTitle() !== $bookCategoryEntity->getTitle())
        {
            return ['warning','Категория с таким именем уже существует','app_book_category_list_admin'];
        }

        if ($parentCategoryName)
        {
            $parentCategory = $this->categoryEntityRepository->findOneBy(['title' => $parentCategoryName]);

            $bookCategoryEntity->setTitle($nameOfCategory);
            $bookCategoryEntity->setLevel(2);
            $bookCategoryEntity->setParentCategory($parentCategory);
        }else{
            $bookCategoryEntity->setTitle($nameOfCategory);
            $bookCategoryEntity->setLevel(1);
        }

        $this->categoryEntityRepository->save($bookCategoryEntity,true);

        return ['success','Категория успешно добавлена','app_book_category_list_admin'];
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function getCategoryWithFilterBooks(Request $request, int $categoryId)
    {
        $requestData = $request->request->all();
        //dd($requestData);

        return $this->categoryEntityRepository->getBooksAndSubcategoryByCategory($categoryId,
            $requestData['book-name'],$requestData['author-name'],$requestData['book-status']);
    }



}