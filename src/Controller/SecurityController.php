<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserResetPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $formResetPassword = $this->createForm(UserResetPasswordType::class, null,['action' => $this->generateUrl('email_forgot_password')]);

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        //return $this->render('maintenance/index.html.twig', ['controller_name' => 'SecurityController']); MAINTENANCE
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'controller_name' => 'SecurityController', 'formResetPassword' => $formResetPassword->createView()]);
    }

    /**
     * @Route("/deconnexion", name="app_logout", methods={"GET"})
     */
    public function logout()
    { }

    /**
     * @Route("/reinitialiser/mdp/{id}/{token}", name="app_reset_password", methods={"POST","GET"})
     */
    public function resetPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, $id, $token)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['id' => $id]);

        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            if($user->getTokenReset()==$token){
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                $user->setTokenReset(null);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                $this->get('session')->getFlashBag()->add('success', "Mot de passe modifié");
            }
        }

        return $this->render('security/reset_password.html.twig', [
            'controller_name' => 'EmailController',
            'form' => $form->createView(),
        ]);
    }
}
