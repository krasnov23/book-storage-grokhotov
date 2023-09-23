<?php

namespace App\Service;

use App\Repository\BookEntityRepository;
use Symfony\Component\String\Slugger\SluggerInterface;

class BookEntityService
{
    private const PAGE_LIMIT = 5;

    public function __construct(private BookEntityRepository $bookEntityRepository)
    {
    }

    public function getBooksByPage(int $page): array
    {
        $offset = max($page - 1,0) * self::PAGE_LIMIT;

        $paginator = $this->bookEntityRepository->bookPagination($offset,self::PAGE_LIMIT);

        $paginatorArray = [];

        foreach ($paginator as $item)
        {
            $paginatorArray[] = $item;
        }

        $total = ceil(count($paginatorArray) / self::PAGE_LIMIT);

        return [$paginatorArray,$total];
    }


}