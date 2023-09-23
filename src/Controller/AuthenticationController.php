<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthenticationController extends AbstractController
{
    #[Route('/login', name: 'app_authentication')]
    public function index(AuthenticationUtils $utils): Response
    {
        $lastUsername = $utils->getLastUsername();
        $error = $utils->getLastAuthenticationError();

        if ($error)
        {
            $this->addFlash('warning','Введены некорректные данные');
        }

        return $this->render('authentication/login.html.twig', [
            'lastUsername' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): Response
    {

    }




}
