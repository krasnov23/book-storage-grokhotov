<?php

namespace App\Controller;

use App\Entity\Settings;
use App\Form\BookEntityType;
use App\Form\SettingsType;
use App\Repository\SettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    public function __construct(private SettingsRepository $settingsRepository)
    {
    }

    #[Route('/admin/settings', name: 'app_settings')]
    public function index(Request $request): Response
    {
        $settings = $this->settingsRepository->findOneBy(['nameSelector' => 'settings']);
        $newSettings = new Settings();

        if ($settings)
        {
            $form = $this->createForm(SettingsType::class,$settings);
        }else{
            $form = $this->createForm(SettingsType::class,$newSettings);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid())
        {
            /** @var Settings $configureSettings */
            $configureSettings = $form->getData();

            if (!$settings)
            {
                $configureSettings->setNameSelector('settings');
            }

            $this->settingsRepository->save($configureSettings,true);
            $this->addFlash('success','Настройки успешно изменены');
            $this->redirectToRoute('app_settings');
        }

        return $this->render('settings/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
