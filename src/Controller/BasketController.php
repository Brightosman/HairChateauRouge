<?php

namespace App\Controller;

use App\Service\Basket;
use App\Entity\Product;

use App\Repository\ProductRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BasketController extends AbstractController
{
    #[Route('/basket', name: 'basket')]
    public function index(SessionInterface $session, ProductRepository $productRepository): Response
    {
         $basket = $session->get("basket", []);

         $basketData = [];
         $total = 0;

         foreach($basket as $id => $quantity){
             $product = $productRepository->find($id);
             $basketData[] = [
                 "product" => $product,
                 "quantity" => $quantity,
                
             ];
             $total += $product->getPrix() * $quantity;
         }

        return $this->render('basket/index.html.twig', compact("basketData", "total"), 
        );
    }

    #[Route('/basket/add{id}  ', name:'add_to_basket')]
     public function add_to_basket(Product $product, SessionInterface $session /*Request $request, ProductRepository $repoProduct, Basket $basket*/){
        // $quantity = $request->request->get('quantity');
        // $id_product = $request->request->get('id');

        // $product = $repoProduct->find($id_product);
        // $basket->add($product->getTitre(), $id_product, $quantity, $product->getPrix());
        $basket = $session->get("basket", []);
        $id = $product->getId();

        if(!empty($basket[$id])){
            $basket[$id]++;
        }else{
            $basket[$id] = 1;
        }

        $session->set("basket", $basket);

        return $this->redirectToRoute('basket');
    }

    #[Route('/basket/empty', name:'basket_empty')]
    public function basket_empty(Basket $basket){
        $basket->empty();
        return $this->redirectToRoute('basket');
    }

    #[Route('/basket/remove/{id}', name:'basket_remove')]
    public function remove(Product $product, SessionInterface $session)
    {
        
        $basket = $session->get("basket", []);
        $id = $product->getId();

        if (!empty($basket[$id])) {
            if ($basket[$id] > 1) {
                $basket[$id]--;
            } else {
                unset($basket[$id]);
            }
        }

        $session->set("basket", $basket);

        return $this->redirectToRoute("basket");
    }

    #[Route('/basket/delete/{id}', name: 'basket_delete')]
    public function delete(Product $product, SessionInterface $session)
    {
        
        $basket = $session->get("basket", []);
        $id = $product->getId();

        if (!empty($basket[$id])) {
            unset($basket[$id]);
        }

        $session->set("basket", $basket);

        return $this->redirectToRoute("basket");
    }

    #[Route('/basket/delete', name: 'basket_delete_all')]
    public function deleteAll(SessionInterface $session)
    {
        $session->remove("basket");

        return $this->redirectToRoute("basket");
    }
}
