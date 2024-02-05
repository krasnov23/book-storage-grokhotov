<?php

namespace App\Tests\Service;

use App\Entity\BookEntity;
use App\Repository\SettingsRepository;
use App\Service\BookEntityService;
use App\Service\BookImageService;
use App\Tests\AbstractTestClass;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class BookImageServiceTest extends AbstractTestClass
{
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filesystem = $this->createMock(Filesystem::class);
    }

    public function testAddNewBookImage(): void
    {
        $uploadedFileMock = $this->createMock(UploadedFile::class);

        $uploadedFileMock->expects($this->once())
            ->method('guessExtension')->willReturn('jpg');

        $uploadedFileMock->expects($this->once())
            ->method('move')
            ->with('testDirectory/images', $this->callback(function (string $arg){
                if (!str_ends_with($arg,'.jpg')){
                    return false;
                }
                return Uuid::isValid(basename($arg,'.jpg'));
            }));

        $uploadedImageNameInfo = pathinfo($this->createBookEntityService()->addNewBookImage($uploadedFileMock));

        $this->assertEquals('jpg',$uploadedImageNameInfo['extension']);
        $this->assertTrue(Uuid::isValid($uploadedImageNameInfo['filename']));

    }

    public function testEditBookImageWithOldBookImage(): void
    {
        $uploadedFileMock = $this->createMock(UploadedFile::class);

        $uploadedFileMock->expects($this->once())
            ->method('guessExtension')->willReturn('jpg');

        $uploadedFileMock->expects($this->once())
            ->method('move')
            ->with('testDirectory/images', $this->callback(function (string $arg){
                if (!str_ends_with($arg,'.jpg')){
                    return false;
                }
                return Uuid::isValid(basename($arg,'.jpg'));
            }));

        $fileSystem = $this->createMock(Filesystem::class);

        $fileSystem->expects($this->once())
            ->method('remove')
            ->with("testDirectory/images" . DIRECTORY_SEPARATOR . "testImage.jpg");

        $uploadedImageNameInfo = pathinfo((new BookImageService('testDirectory/images',$fileSystem))
            ->editBookImage($uploadedFileMock, 'testImage.jpg'));

        $this->assertEquals('jpg',$uploadedImageNameInfo['extension']);
        $this->assertTrue(Uuid::isValid($uploadedImageNameInfo['filename']));

    }

    public function testEditBookImageWithoutImage(): void
    {
        $uploadedFileMock = $this->createMock(UploadedFile::class);

        $uploadedFileMock->expects($this->once())
            ->method('guessExtension')->willReturn('jpg');

        $uploadedFileMock->expects($this->once())
            ->method('move')
            ->with('testDirectory/images', $this->callback(function (string $arg){
                if (!str_ends_with($arg,'.jpg')){
                    return false;
                }
                return Uuid::isValid(basename($arg,'.jpg'));
            }));

        $fileSystem = $this->createMock(Filesystem::class);

        $fileSystem->expects($this->never())
            ->method('remove');

        $uploadedImageNameInfo = pathinfo((new BookImageService('testDirectory/images',$fileSystem))
            ->editBookImage($uploadedFileMock));

        $this->assertEquals('jpg',$uploadedImageNameInfo['extension']);
        $this->assertTrue(Uuid::isValid($uploadedImageNameInfo['filename']));
    }

    public function testDeleteBookImage()
    {
        $book = new BookEntity();
        $book->setImage('testImage.jpg');

        $fileSystem = $this->createMock(Filesystem::class);

        $fileSystem->expects($this->once())
            ->method('remove')
            ->with("testDirectory/images" . DIRECTORY_SEPARATOR . "testImage.jpg");

        $deleteBookImage = (new BookImageService('testDirectory/images',$fileSystem))->deleteBookImage($book);

        $this->assertEquals(new BookEntity(),$deleteBookImage);
    }

    private function createBookEntityService(): BookImageService
    {
        return new BookImageService('testDirectory/images',$this->filesystem);
    }

}