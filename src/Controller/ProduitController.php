<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    /**
     * @Route("/produit", name="produit")
     */
    public function index()
    {
        $repository=$this->getDoctrine()->getRepository(Produit::class);

        //je fais un select *
        $produit=$repository->findAll();

        return $this->render('produit/index.html.twig', [
            "produit"=>$produit
        ]);
    }

    /**
     * @Route("/produit/ajouter",name="produit_ajouter")
     */
    //on a donné un URL a la route, puis un nom
    public function ajouter(Request $request){
        //je crée un objet categorie vide
        $produit=new Produit();

        //creer le formulaire
        $formulaire=$this->createForm(ProduitType::class, $produit);  //va creer un formulaire vide car $categorie est vide

        //recuperer les données du POST
        $formulaire->handleRequest($request);    //recupere les données du post et rempli $categorie

        if($formulaire->isSubmitted() && $formulaire->isValid()){
            //On va recuperer l'entity manager
            $em=$this->getDoctrine()->getManager();

            //je dis au manager de garder cet objet en BDD
            $em->persist($produit);
            //execute l'insert
            $em->flush();

            //je m'en vais
            return $this->redirectToRoute("produit");
        }


        return $this->render('produit/formulaire.html.twig',[
            "formulaire"=>$formulaire->createView()
            ,"h1"=>"Ajouter un produit"
        ]);
    }

    /**
     * @Route("/produit/modifier/{id}",name="produit_modifier")
     */
    public function modifier(Request $request, $id)
    {
        //je vais chercher l'objet a modifier
        $repository=$this->getDoctrine()->getRepository(Produit::class);
        $produit=$repository->find($id);

        //creer le formulaire
        $formulaire = $this->createForm(ProduitType::class, $produit);  //va creer un formulaire vide car $categorie est vide

        //recuperer les données du POST
        $formulaire->handleRequest($request);    //recupere les données du post et rempli $categorie

        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            //On va recuperer l'entity manager
            $em = $this->getDoctrine()->getManager();

            //je dis au manager de garder cet objet en BDD
            $em->persist($produit);
            //execute l'insert
            $em->flush();

            //je m'en vais
            return $this->redirectToRoute("produit");
        }
        return $this->render('produit/formulaire.html.twig',[
            "formulaire"=>$formulaire->createView()
            ,"h1"=>"Modifier la produit".$produit->getTitre()
        ]);
    }
}
