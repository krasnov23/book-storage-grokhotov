<?php

namespace App\Tests\Service;

use App\Entity\BookEntity;
use App\Entity\Settings;
use App\Repository\BookEntityRepository;
use App\Repository\SettingsRepository;
use App\Service\BookEntityService;
use App\Service\BookImageService;
use App\Tests\AbstractTestClass;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class BookEntityServiceTest extends AbstractTestClass
{
    private BookImageService $bookImageService;
    private BookEntityRepository $bookEntityRepository;
    private SettingsRepository $settingsRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookImageService = $this->createMock(BookImageService::class);
        $this->bookEntityRepository = $this->createMock(BookEntityRepository::class);
        $this->settingsRepository = $this->createMock(SettingsRepository::class);
    }

    public function testAddBookWithoutBookImage(): void
    {
        $book = New BookEntity();

        $this->bookImageService->expects($this->never())
            ->method('addNewBookImage');

        $this->bookEntityRepository->expects($this->once())
            ->method('save')
            ->with($book,true);

        $bookService = $this->createBookEntityService();

        $bookService->addBook($book);
    }

    public function testAddBookWithBookImage(): void
    {
        $book = New BookEntity();
        $newBookImage = (new UploadedFile('path','field',null,UPLOAD_ERR_NO_FILE,true));

        $this->bookImageService->expects($this->once())
            ->method('addNewBookImage')
            ->with($newBookImage)
            ->willReturn('testName');

        $this->bookEntityRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (BookEntity $bookEntity){
                if ($bookEntity->getImage() !== 'testName'){
                    return false;
                }
                return true;
            }),true);

        $bookService = $this->createBookEntityService();

        $bookService->addBook($book,$newBookImage);
    }

    public function testEditBookWithOutImage(): void
    {
        $book = New BookEntity();

        $this->bookImageService->expects($this->never())
            ->method('editBookImage');

        $this->bookEntityRepository->expects($this->once())
            ->method('save')
            ->with($book,true);

        $bookService = $this->createBookEntityService();

        $bookService->editBook($book);
    }

    public function testEditBookWithUploadedImage():void
    {
        $book = New BookEntity();
        $newBookImage = (new UploadedFile('path','field',null,UPLOAD_ERR_NO_FILE,true));

        $this->bookImageService->expects($this->once())
            ->method('editBookImage')
            ->with($newBookImage)
            ->willReturn('testName');

        $this->bookEntityRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (BookEntity $bookEntity){
                if ($bookEntity->getImage() !== 'testName'){
                    return false;
                }
                return true;
            }),true);

        $bookService = $this->createBookEntityService();

        $bookService->editBook($book,'someName',$newBookImage);
    }

    public function testGetBooksByPageWithSettings(): void
    {
        $book = (new BookEntity());

        $this->settingsRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['nameSelector' => 'settings'])
            ->willReturn((new Settings())->setAmountBookPagination(1));
        
        $this->bookEntityRepository->expects($this->once())
            ->method('bookPagination')
            ->with(0,1)
            ->willReturn(new \ArrayIterator([$book]));
        
        $bookService = $this->createBookEntityService();

        $this->assertEquals([[$book],1],$bookService->getBooksByPage(1));
    }

    public function testGetBooksByPageWithoutSettings(): void
    {
        $books = [new BookEntity(),new BookEntity(),new BookEntity(),new BookEntity(),new BookEntity()];

        $this->settingsRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['nameSelector' => 'settings'])
            ->willReturn(null);

        $this->bookEntityRepository->expects($this->once())
            ->method('bookPagination')
            ->with(0,5)
            ->willReturn(new \ArrayIterator([$books]));

        $bookService = $this->createBookEntityService();

        $this->assertEquals([[$books],1],$bookService->getBooksByPage(1));
    }

    public function testGetBooksByCategoryAndPagesWithSettings(): void
    {
        $book = new BookEntity();

        $this->settingsRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['nameSelector' => 'settings'])
            ->willReturn((new Settings())->setAmountBookPagination(1));

        $this->bookEntityRepository->expects($this->once())
            ->method('booksByCategoryAndPages')
            ->with(1,0,1)
            ->willReturn(new \ArrayIterator([$book]));

        $bookService = $this->createBookEntityService();

        $this->assertEquals([[$book],1],$bookService->getBooksByCategoryAndPages(1,1));
    }

    public function testGetBooksByCategoryAndPagesWithoutSettings(): void
    {
        $books = [new BookEntity(),new BookEntity(),new BookEntity(),new BookEntity(),new BookEntity()];

        $this->settingsRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['nameSelector' => 'settings'])
            ->willReturn(null);

        $this->bookEntityRepository->expects($this->once())
            ->method('booksByCategoryAndPages')
            ->with(1,0,5)
            ->willReturn(new \ArrayIterator([$books]));

        $bookService = $this->createBookEntityService();

        $this->assertEquals([[$books],1],$bookService->getBooksByCategoryAndPages(1,1));
    }
    

    private function createBookEntityService(): BookEntityService
    {
        return new BookEntityService($this->bookEntityRepository,
            $this->settingsRepository,$this->bookImageService);
    }






}