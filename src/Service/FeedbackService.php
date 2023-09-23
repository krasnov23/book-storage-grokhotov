<?php

namespace App\Service;

use App\Entity\FeedbackEntity;
use App\Repository\FeedbackEntityRepository;
use DateTime;

class FeedbackService
{
    public function __construct(private FeedbackEntityRepository $feedbackRepository)
    {
    }

    public function addFeedback(FeedbackEntity $feedback): array
    {
        $feedback->setCreatedDate(new DateTime());

        $this->feedbackRepository->save($feedback,true);

        return ['success','Ваш отзыв отправлен','app_main'];
    }

}