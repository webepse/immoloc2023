<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Entity\UserImgModify;
use App\Form\ImgModifyType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountController extends AbstractController
{
    /**
     * Permet à l'utilisateur de se connecter
     *
     * @param AuthenticationUtils $utils
     * @return Response
     */
    #[Route('/login', name: 'account_login')]
    public function index(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('account/index.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }

    /**
     * Permet à l'utilisateur de se déconnecter
     *
     * @return void
     */
    #[Route("/logout", name:"account_logout")]
    public function logout(): void
    {
        // ..
    }

    /**
     * Permet d'afficher le formulaire d'inscription d'un utilisateur et de l'ajouter à la base de données
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Route("/register", name:"account_register")]
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // gestion de mon image
            $file = $form['picture']->getData();
            if(!empty($file))
            {
                $originalFilename = pathinfo($file->getClientOriginalName(),PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin;Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename."-".uniqid().".".$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                }catch(FileException $e)
                {
                    return $e->getMessage();
                }
                $user->setPicture($newFilename);
            }

            $hash = $hasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre compte a bien été créé"
            );

            return $this->redirectToRoute('account_login');

        }

        return $this->render("account/registration.html.twig",[
            'myform' => $form->createView()
        ]);


    }

    /**
     * Permet d'éditer les informations d'un utilisateur connecté
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/account/profile", name:"account_profile")]
    public function profile(Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser(); // récup l'utilisateur connecté
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les données ont été enregistrées avec succès"
            );
        }

        return $this->render("account/profile.html.twig",[
            'myform' => $form->createView()
        ]);
    }

    #[Route("/account/password-update", name:'account_password')]
    public function updatePassword(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $passwordUpdate = new PasswordUpdate();
        $user = $this->getUser();
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // vérifier que le mot de passe correspond à l'ancien
            if(!password_verify($passwordUpdate->getOldPassword(),$user->getPassword()))
            {
                // gérer l'erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel"));
            }else{
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $hasher->hashPassword($user, $newPassword);

                $user->setPassword($hash);
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié"
                );

                return $this->redirectToRoute('homepage');
            }
        }


        return $this->render("account/password.html.twig",[
            'myform' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier l'image de l'utilisateur
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/account/imgmodify", name:"account_modifimg")]
    public function imgModify(Request $request, EntityManagerInterface $manager): Response
    {
        $imgModify = new UserImgModify();
        $user = $this->getUser(); 
        $form = $this->createForm(ImgModifyType::class, $imgModify);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // supprimer l'image dans le dossier
            if(!empty($user->getPicture()))
            {
                unlink($this->getParameter('uploads_directory').'/'.$user->getPicture());
            }

            $file = $form['newPicture']->getData();
            if(!empty($file))
            {
                $originalFilename = pathinfo($file->getClientOriginalName(),PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin;Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename."-".uniqid().".".$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                }catch(FileException $e)
                {
                    return $e->getMessage();
                }
                $user->setPicture($newFilename);
            }

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre avatar a bien été modifié'
            );

            return $this->redirectToRoute('homepage');

        }

        return $this->render("account/imgModify.html.twig",[
            'myform' => $form->createView()
        ]);
    }

    #[Route("/account/delimg", name:'account_delimg')]
    public function removeImg(EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        if(!empty($user->getPicture()))
        {
            unlink($this->getParameter('uploads_directory').'/'.$user->getPicture());
            $user->setPicture('');
            $manager->persist($user);
            $manager->flush();

            $this->addflash(
                'success',
                'Votre avatar a bien été supprimé'
            );
        }

        return $this->redirectToRoute('homepage');
    }
}
