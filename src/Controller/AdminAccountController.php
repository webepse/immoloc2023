<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminAccountController extends AbstractController
{
    /**
     * Permet de se connecter à l'admin
     *
     * @param AuthenticationUtils $utils
     * @return Response
     */
    #[Route('/admin/login', name: 'admin_account_login')]
    public function index(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('admin/account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);

    }

    /**
     * Permet de se déconnecter
     *
     * @return void
     */
    #[Route("/admin/logout", name:"admin_account_logout")]
    public function logout(): void
    {
        //
    }
}
