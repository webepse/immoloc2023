<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * Permet d'afficher les annonces pour l'administration
     *
     * @param AdRepository $repo
     * @param int $page
     * @return Response
     */
    #[Route('/admin/ads/{page?1}', name: 'admin_ads_index', requirements:['page'=>"\d+"])]
    public function index(AdRepository $repo, $page): Response
    {
        // $ad = $repo->find(531);
        // $ad = $repo->findBy([
        //     "title" => "annonce"
        // ]);
        // findBy = 1) critère 2) orders 3) limit 4) offset
        // $ads = $repo->findBy([],[], 5, 0)

        $limit = 10;
        $start = $page * $limit - $limit;
        // page 1 * 10 = 10 -10 = 0
        // page 2 * 10 = 20 - 10 = 10
        $total = count($repo->findAll());
        // 3.1 = 4
        $pages = ceil($total / $limit);


        return $this->render('admin/ad/index.html.twig', [
            'ads' => $repo->findBy([],[],$limit, $start),
            'pages' => $pages,
            'page' => $page
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition
     * @param Ad $ad
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/admin/ads/{id}/edit", name: "admin_ads_edit")]
    public function edit(Ad $ad, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(AnnonceType::class, $ad);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été modifiée"
            );
        }

        return $this->render("admin/ad/edit.html.twig",[
            "ad" => $ad,
            "myForm" => $form->createView()
        ]);

    }

    /**
     * Permet de supprimer une annonce
     *
     * @param Ad $as
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/admin/ads/{id}/delete", name: "admin_ads_delete")]
    public function delete(Ad $ad, EntityManagerInterface $manager): Response
    {
        // one ne peut pas supprimer une annonce qui possède des réservations
        if(count($ad->getBookings()) > 0)
        {
            // afficher qu'on ne peut pas supprimer 
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas supprier l'annonce <strong>id: {$ad->getId()} - {$ad->getTitle()}</strong> car elle possède des réservations"
            );
        }else{
            // supprimer + flash
            $this->addFlash(
                "success",
                "L'annonce <strong>id: {$ad->getId()} - {$ad->getTitle()}</strong> a bien été supprimée"
            );
            $manager->remove($ad);
            $manager->flush();
        }

        return $this->redirectToRoute('admin_ads_index');
    }
}
