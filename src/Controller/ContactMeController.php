<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactMeController extends AbstractController
{
    #[Route('/contactme', name: 'contact_me')]
    public function index(): Response
    {
        return $this->render('contact_me/index.html.twig', [
            'controller_name' => 'ContactMeController',
        ]);
    }
}
