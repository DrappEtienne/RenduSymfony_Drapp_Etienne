<?php

namespace App\Controller;

use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    /**
     * @Route("/menu", name="menu")
     */
    public function _menu()
    {
        $repository=$this->getDoctrine()->getRepository(Produit::class);
        $produit=$repository->findAll();

        return $this->render('menu/_menu.html.twig', [
            "produit"=>$produit,
        ]);
    }
}
