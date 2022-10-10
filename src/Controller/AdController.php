<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Repository\AdRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AdController extends AbstractController
{
    /**
     * Permet d'afficher l'ensemble des annonces
     *
     * @param AdRepository $repo
     * @return Response
     */
    #[Route('/ads', name: 'ads_index')]
    public function index(AdRepository $repo): Response
    {
        $ads = $repo->findAll();


        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }

    #[Route("/ads/new", name:"ads_create")]
    public function create(): Response
    {
        $ad = new Ad();

        // $form = $this->createFormBuilder($ad)
        //             ->add('title')
        //             ->add('introduction')
        //             ->add('content')
        //             ->add('rooms')
        //             ->add('price')
        //             ->add('save', SubmitType::class, [
        //                 'label' => "créer la nouvelle annonce",
        //                 "attr" => [
        //                     'class' => 'btn btn-primary'
        //                 ]
        //             ])
        //             ->getForm();
        
        $form = $this->createFormBuilder($ad)
                    ->add('title')
                    ->add('introduction')
                    ->add('content')
                    ->add('rooms')
                    ->add('price')
                    ->getForm();

        return $this->render("ad/new.html.twig",[
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher une annonce via son slug
     */
    #[Route('/ads/{slug}', name:'ads_show')]
    public function show(string $slug, Ad $ad):Response
    {
        dump($ad);

        return $this->render('ad/show.html.twig',[
            'ad' => $ad
        ]);
    }


}
