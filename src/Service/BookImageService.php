<?php

namespace App\Service;

use App\Entity\BookEntity;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class BookImageService
{
    public function __construct(private string $booksImageDirectory,
                                private Filesystem $filesystem)
    {
    }

    public function addNewBookImage(UploadedFile $bookImage): string
    {

        // составление нового уникального имени картинки
        $newFileName = Uuid::v4()->toRfc4122() . '.' . $bookImage->guessExtension();

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

    public function editBookImage(UploadedFile $bookImage,?string $bookOldImage = null): string
    {

        $newFileName = Uuid::v4()->toRfc4122() . '.' . $bookImage->guessExtension();

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