<?php

namespace App\Controller;

use App\Entity\FeedbackEntity;
use App\Form\FeedbackEntityType;
use App\Repository\FeedbackEntityRepository;
use App\Service\FeedbackEntityService;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeedbackController extends AbstractController
{
    public function __construct(private FeedbackEntityRepository $feedbackRepository,
                                private FeedbackEntityService    $feedbackService)
    {
    }

    #[Route('/admin/feedbacks', name: 'app_feedbacks')]
    public function index(): Response
    {
        return $this->render('feedback/get-all-feedbacks.html.twig', [
            'feedbacks' => $this->feedbackRepository->findBy([],['createdDate' => Criteria::DESC])
        ]);
    }

    #[Route('/create-feedback', name: 'app_create_feedback')]
    public function create(Request $request): Response
    {
        $form = $this->createForm(FeedbackEntityType::class, new FeedbackEntity());

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid())
        {
            $feedback = $this->feedbackService->addFeedback($form->getData());

            $this->addFlash($feedback[0],$feedback[1]);

            return $this->redirectToRoute($feedback[2]);
        }

            return $this->render('feedback/create-feedback.html.twig',[
            'form' => $form->createView()
        ]);
    }

    #[Route('/delete-feedback/{feedback}', name: 'app_delete_feedback')]
    public function delete(FeedbackEntity $feedback): Response
    {
        $this->feedbackRepository->remove($feedback,true);

        $this->addFlash('success','Отзыв Успешно Удален');

        return $this->redirectToRoute('app_feedbacks');
    }




}
