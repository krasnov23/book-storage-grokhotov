<?php

namespace App\Controller;

use App\Entity\FeedbackEntity;
use App\Form\FeedbackEntityType;
use App\Repository\FeedbackEntityRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeedbackController extends AbstractController
{
    public function __construct(private FeedbackEntityRepository $feedbackRepository)
    {
    }

    #[Route('/admin/feedbacks', name: 'app_feedbacks')]
    public function index(): Response
    {
        return $this->render('feedback/get-all-feedbacks.html.twig', [
            'controller_name' => 'FeedbackController',
        ]);
    }

    #[Route('/create-feedback', name: 'app_create_feedback')]
    public function create(Request $request): Response
    {
        $form = $this->createForm(FeedbackEntityType::class, new FeedbackEntity());

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid())
        {
            $feedback = $form->getData();

            $feedback->setCreatedDate(new DateTime());

            $this->feedbackRepository->save($feedback,true);

            $this->addFlash('success','Ваш отзыв отправлен');

            return $this->redirectToRoute('app_main');
        }

            return  $this->render('feedback/create-feedback.html.twig',[
            'form' => $form->createView()
        ]);
    }



}
