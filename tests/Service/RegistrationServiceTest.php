<?php

namespace App\Tests\Service;

use App\Entity\UserEntity;
use App\Security\EmailVerifier;
use App\Service\RegistrationService;
use App\Tests\AbstractTestClass;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class RegistrationServiceTest extends AbstractTestClass
{
    private EmailVerifier $emailVerifier;
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $passwordHasher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->emailVerifier = $this->createMock(EmailVerifier::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
    }

    public function testUserRegistration(): void
    {
        $userWithOutPass = (new UserEntity())
            ->setEmail('test@test.com')
            ->setRoles(['ROLE_USER']);

        $expectedUser = clone $userWithOutPass;
        $expectedUser->setPassword('hashed_password');

        $this->passwordHasher->expects($this->once())
            ->method('hashPassword')
            ->with($userWithOutPass,'testtest')
            ->willReturn("hashed_password");

        $this->em->expects($this->once())
            ->method('persist')
            ->with($expectedUser);

        $this->em->expects($this->once())
            ->method('flush');

        $this->emailVerifier->expects($this->once())
            ->method('sendEmailConfirmation')
            ->with('app_verify_email', $expectedUser,(new TemplatedEmail())
                ->from(new Address('grokhotov@studio.com', 'Grokhotov studio'))
                ->to($expectedUser->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig'));

        $registrationService = new RegistrationService($this->emailVerifier,$this->em,$this->passwordHasher);

        $registrationService->createNewUser($userWithOutPass,'testtest');
    }


}