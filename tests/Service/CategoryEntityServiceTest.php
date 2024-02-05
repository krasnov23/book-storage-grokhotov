<?php

namespace App\Tests\Service;

use App\Entity\BookCategoryEntity;
use App\Entity\BookEntity;
use App\Repository\BookCategoryEntityRepository;
use App\Service\CategoryEntityService;
use App\Tests\AbstractTestClass;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;

class CategoryEntityServiceTest extends AbstractTestClass
{
    private BookCategoryEntityRepository $categoryRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryRepository = $this->createMock(BookCategoryEntityRepository::class);
    }

    public function testAddCategoryWithParentCategory(): void
    {
        $request = new Request();
        $parentCategory = (new BookCategoryEntity())->setTitle('parentCategory');
        
        $inputData = new InputBag();
        $inputData->set('parent-category-name','parentCategory');
        $inputData->set('field-category-name','daughterCategory');
        
        $request->request = $inputData;
        
        $this->categoryRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['title' => 'daughterCategory'])
            ->willReturn(null);
        
        $this->categoryRepository->expects($this->once())
            ->method('findParentCategory')
            ->with(['title' => 'parentCategory'])
            ->willReturn($parentCategory);
        
        $this->categoryRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (BookCategoryEntity $category){
                if ($category->getLevel() !== 2 || $category->getTitle() !== 'daughterCategory' 
                    || $category->getParentCategory()->getTitle() !== 'parentCategory'){
                    return false;
                }
                return true;
            }),true);
            
        $categoryService = new CategoryEntityService($this->categoryRepository);
        $this->assertEquals(['success','Категория успешно добавлена','app_book_category_list_admin'],
            $categoryService->addCategory($request));
        
    }

    public function testAddCategoryWithoutParentCategory(): void
    {
        $request = new Request();

        $inputData = new InputBag();
        $inputData->set('field-category-name','daughterCategory');

        $request->request = $inputData;

        $this->categoryRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['title' => 'daughterCategory'])
            ->willReturn(null);

        $this->categoryRepository->expects($this->never())
            ->method('findParentCategory');

        $this->categoryRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (BookCategoryEntity $category){
                if ($category->getLevel() !== 1 || $category->getTitle() !== 'daughterCategory'){
                    return false;
                }
                return true;
            }),true);

        $categoryService = new CategoryEntityService($this->categoryRepository);
        $this->assertEquals(['success','Категория успешно добавлена','app_book_category_list_admin'],
            $categoryService->addCategory($request));

    }

    public function testTryToAddExistsCategory(): void
    {
        $request = new Request();
        $category = new BookCategoryEntity();


        $inputData = new InputBag();
        $inputData->set('field-category-name','daughterCategory');

        $request->request = $inputData;

        $this->categoryRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['title' => 'daughterCategory'])
            ->willReturn($category);

        $categoryService = new CategoryEntityService($this->categoryRepository);
        $this->assertEquals(['warning','Категория с таким именем уже существует','app_book_category_list_admin'],
            $categoryService->addCategory($request));

    }

    public function testEditCategoryWithParent(): void
    {
        $request = new Request();
        $parentCategory = (new BookCategoryEntity())->setTitle('parentCategory');
        $daughterCategory = (new BookCategoryEntity())->setTitle('daughterCategory');

        $inputData = new InputBag();
        $inputData->set('parent-category-name','parentCategory');
        $inputData->set('field-category-name','newDaughterCategory');
        $request->request = $inputData;

        $this->categoryRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['title' => 'newDaughterCategory'])
            ->willReturn(null);

        $this->categoryRepository->expects($this->once())
            ->method('findParentCategory')
            ->with(['title' => 'parentCategory'])
            ->willReturn($parentCategory);

        $this->categoryRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (BookCategoryEntity $category){
                if ($category->getLevel() !== 2 || $category->getTitle() !== 'newDaughterCategory'
                    || $category->getParentCategory()->getTitle() !== 'parentCategory' ){
                    return false;
                }
                return true;
            }),true);

        $categoryService = new CategoryEntityService($this->categoryRepository);
        $this->assertEquals(['success','Категория успешно изменена','app_book_category_list_admin'],
            $categoryService->editCategory($request,$daughterCategory));
    }

    public function testEditCategoryWithoutParent(): void
    {
        $request = new Request();
        $daughterCategory = (new BookCategoryEntity())->setTitle('daughterCategory');

        $inputData = new InputBag();
        $inputData->set('field-category-name','newDaughterCategory');
        $request->request = $inputData;

        $this->categoryRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['title' => 'newDaughterCategory'])
            ->willReturn(null);

        $this->categoryRepository->expects($this->never())
            ->method('findParentCategory');

        $this->categoryRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (BookCategoryEntity $category){
                if ($category->getLevel() !== 1 || $category->getTitle() !== 'newDaughterCategory'){
                    return false;
                }
                return true;
            }),true);

        $categoryService = new CategoryEntityService($this->categoryRepository);
        $this->assertEquals(['success','Категория успешно изменена','app_book_category_list_admin'],
            $categoryService->editCategory($request,$daughterCategory));
    }

    public function testTryToEditCategoryWithExistsName(): void
    {
        $request = new Request();
        $daughterCategory = (new BookCategoryEntity())->setTitle('daughterCategory');

        $inputData = new InputBag();
        $inputData->set('field-category-name','newDaughterCategory');
        $request->request = $inputData;

        $this->categoryRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['title' => 'newDaughterCategory'])
            ->willReturn((new BookCategoryEntity())->setTitle('newDaughterCategory'));

        $this->categoryRepository->expects($this->never())
            ->method('findParentCategory');

        $this->categoryRepository->expects($this->never())
            ->method('save');

        $categoryService = new CategoryEntityService($this->categoryRepository);
        $this->assertEquals(['warning','Категория с таким именем уже существует','app_book_category_list_admin'],
            $categoryService->editCategory($request,$daughterCategory));
    }

}