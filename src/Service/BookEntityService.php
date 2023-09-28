<?php

namespace App\Service;

use App\Repository\BookEntityRepository;
use App\Repository\SettingsRepository;
use Symfony\Component\String\Slugger\SluggerInterface;

class BookEntityService
{

    public function __construct(private BookEntityRepository $bookEntityRepository,
                                private SettingsRepository $settingsRepository)
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

        $paginatorArray = [];

        foreach ($paginator as $item)
        {
            $paginatorArray[] = $item;
        }

        $total = ceil(count($paginatorArray) / $pageLimit);

        return [$paginatorArray,$total];
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

        $paginatorArray = [];

        foreach ($paginator as $item)
        {
            $paginatorArray[] = $item;
        }

        $totalPages = ceil(count($paginatorArray) / $pageLimit);

        return [$paginatorArray,$totalPages];
    }



}