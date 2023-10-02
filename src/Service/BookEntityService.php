<?php

namespace App\Service;

use App\Entity\BookEntity;
use App\Repository\BookEntityRepository;
use App\Repository\SettingsRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use TypeError;

class BookEntityService
{

    public function __construct(private string $booksImageDirectory,
                                private BookEntityRepository $bookEntityRepository,
                                private SettingsRepository $settingsRepository,
                                private SluggerInterface $slugger,
                                private Filesystem $filesystem,

    )
    {
    }

    public function getBooksByPage(int $page): array
    {
        $settings = $this->settingsRepository->findOneBy(['nameSelector' => 'settings']);
        $pageLimit = 5;

        if ($settings)
        {
            $pageLimit = $settings->getAmountBookPagination() ?? 5;
        }

        $offset = max($page - 1,0) * $pageLimit;

        $paginator = $this->bookEntityRepository->bookPagination($offset,$pageLimit);

        $totalAmountOfBooks = count($paginator);

        $total = ceil($totalAmountOfBooks / $pageLimit);

        return [$paginator,$total];
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

        $totalPages = ceil(count($paginator) / $pageLimit);

        return [$paginator,$totalPages];
    }

    public function addNewBookImage(UploadedFile $bookImage): string
    {
        $originalFileName = pathinfo($bookImage->getClientOriginalName(),PATHINFO_FILENAME);

        $sluggerFileName = $this->slugger->slug($originalFileName);

        $newFileName = $sluggerFileName . '-' . uniqid() . '.' . $bookImage->guessExtension();

        try{
            $bookImage->move(
                $this->booksImageDirectory,
                $newFileName
            );
        } catch (FileException $exception)
        {
        }

        return $newFileName;
    }

    public function editBookImage(UploadedFile $bookImage,?string $bookOldImage): string
    {
        $originalFileName = pathinfo($bookImage->getClientOriginalName(),PATHINFO_FILENAME);

        $sluggerFileName = $this->slugger->slug($originalFileName);

        $newFileName = $sluggerFileName . '-' . uniqid() . '.' . $bookImage->guessExtension();

        try{
            $bookImage->move(
                $this->booksImageDirectory,
                $newFileName
            );
        } catch (FileException $exception)
        {
        }

        if ($bookOldImage)
        {
            $this->filesystem->remove($this->booksImageDirectory . DIRECTORY_SEPARATOR . $bookOldImage);
        }


        return $newFileName;
    }

    public function deleteBookImage(BookEntity $book): BookEntity
    {
        $bookImage = $book->getImage();

        if ($bookImage)
        {
            $this->filesystem->remove($this->booksImageDirectory . DIRECTORY_SEPARATOR . $bookImage);
        }

        $book->setImage(null);

        return $book;
    }



}