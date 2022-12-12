<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard_index')]
    public function index(EntityManagerInterface $manager): Response
    {
        $users = $manager->createQuery("SELECT COUNT(u) FROM App\Entity\User u")->getSingleScalarResult(); // permet de récup 1 valeur et pas un array
        $ads = $manager->createQuery("SELECT COUNT(a) FROM App\Entity\Ad a")->getSingleScalarResult();
        $bookings = $manager->createQuery("SELECT COUNT(b) FROM App\Entity\Booking b")->getSingleScalarResult();
        $comments = $manager->createQuery("SELECT COUNT(c) FROM App\Entity\Comment c")->getSingleScalarResult();

        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => [
                'users' => $users,
                'ads' => $ads,
                'bookings' => $bookings,
                'comments' => $comments
            ],
        ]);
    }
}
