<?php

namespace App\Service;

use App\Entity\BookEntity;
use App\Repository\BookEntityRepository;
use App\Repository\SettingsRepository;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class BookEntityService
{

    public function __construct(private BookEntityRepository $bookEntityRepository,
                                private SettingsRepository $settingsRepository,
                                private BookImageService $bookImageService

    )
    {
    }

    public function addBook(BookEntity $newBook,UploadedFile $bookImage = null)
    {

        $categories = $newBook->getCategories()->toArray();

        foreach ($categories as $category)
        {
            $newBook->addCategory($category);
        }

        if ($bookImage)
        {
            $newFileName = $this->bookImageService->addNewBookImage($bookImage);
            $newBook->setImage($newFileName);
        }

        $this->bookEntityRepository->save($newBook,true);
    }

    public function editBook(BookEntity $book,string $bookOldImage = null,UploadedFile $bookImage = null)
    {
        /**
         * @var PersistentCollection $categories
         */
        $categoriesData = $book->getCategories();

        $categories = $categoriesData->toArray();

        foreach ($categories as $category)
        {
            $book->addCategory($category);
        }

        if ($bookImage)
        {
            $newFileName = $this->bookImageService->editBookImage($bookImage,$bookOldImage);
            $book->setImage($newFileName);
        }else{
            $book->setImage($bookOldImage);
        }

        $this->bookEntityRepository->save($book,true);
    }

    public function getBooksByPage(int $page): array
    {
        // Ищет заданы ли параметры настроек
        $settings = $this->settingsRepository->findOneBy(['nameSelector' => 'settings']);

        $pageLimit = 5;

        if ($settings)
        {
            // В случае если в настройках в кабинете админа заданы параметры кол-ва книг на страницу, то берет их
            $pageLimit = $settings->getAmountBookPagination() ?? 5;
        }

        $offset = max($page - 1,0) * $pageLimit;

        // вывод определенного количества книг
        $paginator = $this->bookEntityRepository->bookPagination($offset,$pageLimit);

        $books = [];

        foreach ($paginator as $item)
        {
            $books[] = $item;
        }


        // последняя страница
        $total = ceil(count($paginator) / $pageLimit);

        // книги на действующей странице, всего страниц
        return [$books,$total];
    }

    public function getBooksByCategoryAndPages(int $id, int $page): array
    {
        $settings = $this->settingsRepository->findOneBy(['nameSelector' => 'settings']);
        $pageLimit = 5;

        if ($settings)
        {
            $pageLimit = $settings->getAmountBookPagination() ?? 5;
        }

        $offset = max($page - 1,0) * $pageLimit;

        $paginator = $this->bookEntityRepository->booksByCategoryAndPages($id,$offset,$pageLimit);

        $books = [];

        foreach ($paginator as $item)
        {
            $books[] = $item;
        }

        $totalPages = ceil(count($paginator) / $pageLimit);

        return [$books,$totalPages];
    }





}