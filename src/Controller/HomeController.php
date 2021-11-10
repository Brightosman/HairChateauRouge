<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Media;
use App\Repository\ProductRepository;
use App\Service\DateFr;

use App\Data\SearchData;
use App\Form\SearchForm;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: '')]
    #[Route('/home', name: 'home')]
    public function index(ProductRepository $repoProduct): Response
    {
        $data = new SearchData();
        $form = $this->createForm(SearchForm::class, $data);
        $productsObjectArray = $repoProduct->findAll();
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'products' => $productsObjectArray,
            'form' => $form->createView()
            
        ]);
    }


     #[Route('/catalogue', name: 'catalogue')]
    public function catalogue(ProductRepository $repoProduct): Response
    {
        $productsObjectArray = $repoProduct->findAll();
        return $this->render('./product/catalogue.html.twig', [
            'controller_name' => 'HomeController',
            'products' => $productsObjectArray
        ]);
    }

    #[Route('/imageslider', name: 'imageslider')]
    public function imageslider(): Response
    {
        return $this->render('home/imageslider.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
