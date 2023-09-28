<?php

namespace App\Command;

use App\Entity\AuthorEntity;
use App\Entity\BookCategoryEntity;
use App\Entity\BookEntity;
use App\Entity\Settings;
use App\Repository\AuthorEntityRepository;
use App\Repository\BookCategoryEntityRepository;
use App\Repository\BookEntityRepository;
use App\Repository\SettingsRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:parse-json',
    description: 'Parse from data from json file',
)]
class ParseJsonCommand extends Command
{
    public function __construct(private BookEntityRepository         $bookRepository,
                                private BookCategoryEntityRepository $categoryRepository,
                                private EntityManagerInterface       $em,
                                private SettingsRepository $settings,
                                private AuthorEntityRepository $authorsRepository)
    {
        parent::__construct();
    }


    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sourceOfJson = $this->settings->findOneBy(['nameSelector' => 'settings']);

        if ($sourceOfJson)
        {
            $sourceOfJson = $sourceOfJson->getSourceJson();
        }

        $JsonFile = file_get_contents($sourceOfJson ?? 'books.json');

        $jsonToArray = json_decode($JsonFile, true);

        foreach ($jsonToArray as $oneBook)
        {
            $allEntityBooksInDB = $this->bookRepository->findAll();
            $allEntityCategoriesInDB = $this->categoryRepository->findAll();
            $allEntityAuthorsInDB = $this->authorsRepository->findAll();

            $booksInDatabase = array_map(fn(BookEntity $bookEntity) => $bookEntity->getName(), $allEntityBooksInDB);
            $categoriesInDatabase = array_map(fn(BookCategoryEntity $categoryEntity) => $categoryEntity->getTitle()
                ,$allEntityCategoriesInDB);
            $authorsInDatabase = array_map(fn(AuthorEntity $author) => $author->getName(), $allEntityAuthorsInDB );

            $newBook = null;
            $newCategory = null;

            if (in_array($oneBook['title'],$booksInDatabase))
            {
                continue;

            }else{
                $newBook = new BookEntity();

                $newBook->setName($oneBook['title']);

                if (array_key_exists('thumbnailUrl',$oneBook))
                {
                    $newBook->setImage($oneBook['thumbnailUrl']);
                }

                $newBook->setAmountOfPages($oneBook['pageCount']);

                if (array_key_exists('isbn',$oneBook))
                {
                    $newBook->setIsbn($oneBook['isbn']);
                }

                if (array_key_exists('shortDescription',$oneBook))
                {
                    $newBook->setShortDescription($oneBook['shortDescription']);
                }

                if (array_key_exists('longDescription',$oneBook))
                {
                    $newBook->setLongDescription($oneBook['longDescription']);
                }

                if (array_key_exists('status',$oneBook))
                {
                    $newBook->setStatus($oneBook['status']);
                }

                if (array_key_exists('publishedDate',$oneBook))
                {
                    $datetime = $oneBook['publishedDate']['$date'];
                    $newBook->setPublishedDate(new DateTimeImmutable($datetime));
                }
            }

            if ($oneBook['categories'] === [])
            {

                $categoryWithNameNew = $this->categoryRepository->findOneBy(['title' => 'Новинки']);

                if ($categoryWithNameNew)
                {
                    $newBook->addCategory($categoryWithNameNew);
                }else{
                    $categoryWithNameNew = new BookCategoryEntity();
                    $categoryWithNameNew->setLevel(1);
                    $categoryWithNameNew->setTitle('Новинки');
                    $newBook->addCategory($categoryWithNameNew);
                }
            }else{
                foreach ($oneBook['categories'] as $categoryInJson)
                {
                    $categoryInJson = ucfirst($categoryInJson);

                    if (in_array($categoryInJson,$categoriesInDatabase))
                    {
                        foreach ($allEntityCategoriesInDB as $categoryEntity)
                        {
                            if ($categoryEntity->getTitle() === $categoryInJson)
                            {
                                $newBook->addCategory($categoryEntity);
                            }
                        }
                    }else{
                        $newCategory = new BookCategoryEntity();
                        $newCategory->setTitle($categoryInJson);
                        $newCategory->setLevel(1);
                        $newBook->addCategory($newCategory);
                    }
                }
            }

            foreach ($oneBook['authors'] as $authorInJson)
            {
                if (in_array($authorInJson,$authorsInDatabase))
                {
                    foreach ($allEntityAuthorsInDB as $authorEntity)
                    {
                        if ($authorEntity->getName() === $authorInJson)
                        {
                            $newBook->addAuthor($authorEntity);
                        }
                    }
                }else{
                    $author = new AuthorEntity();
                    $author->setName($authorInJson);
                    $newBook->addAuthor($author);
                }

            }

            $this->em->persist($newBook);
            $this->em->flush();

        }

        return Command::SUCCESS;
    }
}
