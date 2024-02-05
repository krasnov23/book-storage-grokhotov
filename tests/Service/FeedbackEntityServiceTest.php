<?php

namespace App\Tests\Service;

use App\Entity\FeedbackEntity;
use App\Entity\Settings;
use App\Repository\FeedbackEntityRepository;
use App\Repository\SettingsRepository;
use App\Service\FeedbackEntityService;
use App\Tests\AbstractTestClass;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class FeedbackEntityServiceTest extends AbstractTestClass
{
    private FeedbackEntityRepository $feedbackRepository;
    private MailerInterface $mailer;
    private SettingsRepository $settings;

    protected function setUp(): void
    {
        parent::setUp();

        $this->feedbackRepository = $this->createMock(FeedbackEntityRepository::class);
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->settings = $this->createMock(SettingsRepository::class);
    }

    public function testAddFeedbackTestWithSpecifiedAdminEmail(): void
    {
        $email = (new Email())->from('user@email.com')
        ->to('admin@email.com')->subject("Новый отзыв")
            ->text("user@email.com с номером 89297169752 написал вам: Hello");

        $feedback = (new FeedbackEntity())->setEmail('user@email.com')
            ->setPhoneNumber('89297169752')->setMessage('Hello');

        $this->settings->expects($this->once())
            ->method('findOneBy')
            ->with(['nameSelector' => 'settings'])
            ->willReturn((new Settings())->setNameSelector('settings')
                ->setAdminEmail('admin@email.com'));

        $this->mailer->expects($this->once())
            ->method('send')
            ->with($email);

        $this->feedbackRepository->expects($this->once())
            ->method('save')
            ->with($feedback,true);

        $service = new FeedbackEntityService($this->feedbackRepository,$this->mailer,$this->settings);

        $this->assertEquals(['success','Ваш отзыв отправлен','app_main'],$service->addFeedback($feedback));
    }

    public function testAddFeedbackTestBaseEmail(): void
    {
        $email = (new Email())->from('user@email.com')
            ->to('grokhotov@studio.com')->subject("Новый отзыв")
            ->text("user@email.com с номером 89297169752 написал вам: Hello");

        $feedback = (new FeedbackEntity())->setEmail('user@email.com')
            ->setPhoneNumber('89297169752')->setMessage('Hello');

        $this->settings->expects($this->once())
            ->method('findOneBy')
            ->with(['nameSelector' => 'settings'])
            ->willReturn(null);

        $this->mailer->expects($this->once())
            ->method('send')
            ->with($email);

        $this->feedbackRepository->expects($this->once())
            ->method('save')
            ->with($feedback,true);

        $service = new FeedbackEntityService($this->feedbackRepository,$this->mailer,$this->settings);

        $this->assertEquals(['success','Ваш отзыв отправлен','app_main'],$service->addFeedback($feedback));
    }






}