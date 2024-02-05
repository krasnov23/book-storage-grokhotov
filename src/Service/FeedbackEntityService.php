<?php

namespace App\Service;

use App\Entity\FeedbackEntity;
use App\Entity\Settings;
use App\Repository\FeedbackEntityRepository;
use App\Repository\SettingsRepository;
use DateTime;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class FeedbackEntityService
{
    public function __construct(private FeedbackEntityRepository $feedbackRepository,
                                private MailerInterface $mailer,
                                private SettingsRepository $settings)
    {
    }

    public function addFeedback(FeedbackEntity $feedback): array
    {
        $feedback->setCreatedDate(new DateTime());

        $senderEmailSettings = $this->settings->findOneBy(['nameSelector' => 'settings']);

        if ($senderEmailSettings)
        {
            $senderEmailSettings = $senderEmailSettings->getAdminEmail();
        }


        $email = (new Email())
            ->from($feedback->getEmail())
            ->to($senderEmailSettings  ?? 'grokhotov@studio.com')
            ->subject("Новый отзыв")
            ->text("{$feedback->getEmail()} с номером {$feedback->getPhoneNumber()} написал вам: {$feedback->getMessage()}");

        try{
            $this->mailer->send($email);
        }catch (TransportException)
        {
        }

        $this->feedbackRepository->save($feedback,true);

        return ['success','Ваш отзыв отправлен','app_main'];
    }

}