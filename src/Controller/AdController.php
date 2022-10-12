<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $ad = new Ad();

        // $image1 = new Image();
        // $image1->setUrl('https://picsum.photos/400/200')
        //     ->setCaption('Titre 1');
        
        // $ad->addImage($image1);   

        // $image2 = new Image();
        // $image2->setUrl('https://picsum.photos/400/200')
        //     ->setCaption('Titre 2');
        
        // $ad->addImage($image2);    


        $form = $this->createForm(AnnonceType::class, $ad);
        // permet de récupèrer la requête et l'état du formulaire
        $form->handleRequest($request);

        // Es-ce que mon formulaire à été soumis?
        if($form->isSubmitted() && $form->isValid())
        {
            // gestion des images 
            foreach($ad->getImages() as $image)
            {
                $image->setAd($ad);
                $manager->persist($image);
            }

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée!"
            );
          
            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }

        return $this->render("ad/new.html.twig",[
            'myform' => $form->createView()
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
