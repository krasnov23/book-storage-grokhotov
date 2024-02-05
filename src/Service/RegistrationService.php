<?php

namespace App\Service;

use App\Entity\UserEntity;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationService
{

    public function __construct(private EmailVerifier $emailVerifier,
                                private EntityManagerInterface $entityManager,
                                private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function createNewUser(UserEntity $user,string $password)
    {
        // encode the plain password
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $password
            )
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('grokhotov@studio.com', 'Grokhotov studio'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
        // do anything else you need here, like send an email
    }



}