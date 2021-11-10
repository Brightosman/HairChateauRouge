<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Media;
use App\Form\ProductType;
use App\Controller\BasketController;

use App\Form\CategorieType;

use App\Data\SearchData;
use App\Form\SearchForm;

use App\Repository\ProductRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchForm::class, $data);
        $form->handleRequest($request);
        $products = $productRepository->findSearch($data);
        return $this->render('product/index.html.twig', [
            // 'products' => $productRepository->findAll(),
            'products'=> $products,
            'form'=> $form->createView()
        ]);
    }

    #[Route('/new', name: 'product_new', methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $product->setDateEnregistrement(new \DateTimeImmutable('now'));
            $user=$this->getUser();
            $product->setUser($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            $mediaFile = $form->get('media')->getData();

            if($mediaFile){
                for ($i=0; $i < count($mediaFile); $i++){
                    $media = new Media;

                    $nomMedia = date("TmdHis") . "-" . uniqid() . "-" . $mediaFile[$i]->getClientOriginalName();

                    $mediaFile[$i]->move(
                        $this->getParameter("media_product"),
                        $nomMedia
                    );

                    $media->setNom($nomMedia);
                    $media->setProduct($product);
                    $type = ["image"];
                    $media->setType($type);

                    $entityManager->persist($media);
                    $entityManager->flush();
                }
            }
            // $this->addFlash("success", "L'annonce n°  a bien été ajoutée");

            return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'product_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
    }
}
