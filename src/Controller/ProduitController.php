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

        //On va essayer de verifier l'ean


        if($formulaire->isSubmitted() && $formulaire->isValid()){

            $donneeForm=$formulaire->getData();         //on recupere le form
            //var_dump($donneeForm);

            $ean=$donneeForm->getEAN();    //on recupere seulement le EAN du form qui vient detre submit
            $cestok = 0;
            //verif pour GTIN13
            if(strlen($ean) === 13){
                $totalEAN=($ean[11]*3+$ean[10]*1+$ean[9]*3+$ean[8]*1+$ean[7]*3+$ean[6]*1+$ean[5]*3+$ean[4]*1+$ean[3]*3+$ean[2]*1+$ean[1]*3+$ean[0]*1);

                $MultipeDe10=(round($totalEAN)%10 === 0) ? round($totalEAN) : round(($totalEAN+10/2)/10)*10;

                $checkDigit=($MultipeDe10-$totalEAN);

                if($checkDigit === $ean[12]){
                    $cestok++;
                }
            }
            //verif pour GTIN14
            else if(strlen($ean) === 14){
                $totalEAN=($ean[12]*3+$ean[11]*1+$ean[10]*3+$ean[9]*1+$ean[8]*3+$ean[7]*1+$ean[6]*3+$ean[5]*1+$ean[4]*3+$ean[3]*1+$ean[2]*3+$ean[1]*1+$ean[0]*3);

                $MultipeDe10=((round($totalEAN)%10 === 0) ? round($totalEAN) : round(($totalEAN+10/2)/10)*10);

                $checkDigit=($MultipeDe10-$totalEAN);

                if($checkDigit === $ean[13]){
                    $cestok++;
                }
            }
            else{
                //si le GTIN ne fais pas la bonne longueur, il sort
                return $this->redirectToRoute("produit");
            }
            //if($cestok === 1){       //si le calcul montre que le GTIN est bon
                //On va recuperer l'entity manager
                $em=$this->getDoctrine()->getManager();

                //je dis au manager de garder cet objet en BDD
                $em->persist($produit);
                //execute l'insert
                $em->flush();

                //je m'en vais
                return $this->redirectToRoute("produit");
            //}
            /*else if($cestok === 0){
                $this->addFlash("ERREUR", "Le GTIN n'est pas bon");
            }*/

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
            ,"h1"=>"Modifier la produit".$produit->getReference()
        ]);
    }

    /**
     * @Route("/produit/supprimer/{id}",name="produit_supprimer")
     */
    public function supprimer(Request $request, $id)
    {
        //je vais chercher l'objet a supprimer
        $repository=$this->getDoctrine()->getRepository(Produit::class);
        $produit=$repository->find($id);

        //creer le formulaire
        $formulaire = $this->createForm(ProduitType::class, $produit);  //va creer un formulaire vide car $produit est vide

        //recuperer les données du POST
        $formulaire->handleRequest($request);    //recupere les données du post et rempli $produit

        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            //On va recuperer l'entity manager
            $em = $this->getDoctrine()->getManager();
            $em->remove($produit);
            $em->flush();
            $this->addFlash("Succes","Le produit a bien été supprimé");


            //je m'en vais
            return $this->redirectToRoute("produit");
        }
        return $this->render('produit/formulaire.html.twig',[
            "formulaire"=>$formulaire->createView()
            ,"h1"=>"Supprimer le produit".$produit->getReference()
        ]);
    }
}
