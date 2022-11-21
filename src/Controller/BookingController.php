<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    #[Route('/ads/{slug}/book', name: 'booking_create')]
    #[IsGranted("ROLE_USER")]
    public function book(Ad $ad,Request $request, EntityManagerInterface $manager): Response
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // traitement 
            $user = $this->getUser();
            $booking->setBooker($user)
                ->setAd($ad);

            // vérifier si les dates ne sont pas disponible, dans le cas où elles ne le sont pas -> message erreur
            if(!$booking->isBookableDates())
            {
                $this->addFlash(
                    'warning',
                    'Les dates que vous avez choisies ne peuvent être réservées: elles sont déjà prises!'
                );
            }else{
                $manager->persist($booking);
                $manager->flush();

              
                return $this->redirectToRoute('booking_show',['id' => $booking->getId(), 'withAlert' => true]);
            }
        }

        return $this->render('booking/book.html.twig', [
            'ad' => $ad,
            'myForm' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher la page d'une réservation
     */
    #[Route("/booking/{id}", name:"booking_show")]
    #[IsGranted("ROLE_USER")]
    public function show(Booking $booking, Request $request, EntityManagerInterface $manager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $comment->setAd($booking->getAd())
                ->setAuthor($this->getUser());
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre commentaire a bien été pris en compte'
            );
        }

        return $this->render("booking/show.html.twig",[
            'booking' => $booking,
            'myForm' => $form->createView()
        ]);
    }
}
