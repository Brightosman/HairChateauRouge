<?php
namespace App\Service;

use App\Entity\Product;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Basket{
    public $session;
    public $productRepository;
    public $manager;

    public function _construct(SessionInterface $session, ProductRepository $productRepository, EntityManagerInterface $manager){
        $this->session = $session;
        $this->productRepository = $productRepository;
        $this->manager = $manager;
    }

    public function createBasket()
    {
        $basket = [
            'titre' => [],
            // "id" => [],
            // "quantity" => [],
            "prix" => []
        ];

        return $basket;
    }

    public function add($titre, $id_product, $quantity, $prix){
        $basketSession = $this->session->get('basket');

        if(empty($basketSession)){
            $newBasket = $this->createBasket();
            $this->session->set('basket', $newBasket);
            $basketSession = $this->session->get('basket');
        }
        $position_product = array_search($id_product, $basketSession["id_product"]);

        if (is_int($position_product)) 
        {
            $basketSession["quantity"][$position_product] += $quantity;
            $this->session->set('basket', $basketSession);
        } else 
        {
            $basketSession["titre"][] = $titre;
            // $basketSession["id_product"][] = $id_product;
            // $basketSession["quantity"][] = $quantity;
            $basketSession["prix"][] = $prix;
            $this->session->set('basket', $basketSession);
        }
    }

    public function empty()
    {
       $this->session->remove("basket");
    }

    public function remove($id_product_delete)
    {
        $basketSession = $this->session->get('basket');

        $position_product = array_search($id_product_delete, $basketSession['id_product']);

        if (is_int($position_product)) {
            array_splice($basketSession['titre'], $position_product, 1);
            array_splice($basketSession['id_product'], $position_product, 1);
            array_splice($basketSession['quantity'], $position_product, 1);
            array_splice($basketSession['prix'], $position_product, 1);

            $this->session->set('basket', $basketSession);
        }
    }

    public function totalSum()
    {
        $basketSession = $this->session->get('basket');

        $total = 0;

        for ($i = 0; $i < count($basketSession['id_product']); $i++) {
            $total += $basketSession['prix'][$i] * $basketSession['quantity'][$i];
        }

        return round($total, 2);
    }
}
