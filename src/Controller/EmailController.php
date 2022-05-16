<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserResetPasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;

class EmailController extends AbstractController
{
    /**
     * @Route("/email/oublier/mdp", name="email_forgot_password")
     */
    public function forgotPassword(\Swift_Mailer $mailer, Request $request, TranslatorInterface $translator)
    {
        $email = $request->request->get('user_reset_password')['email'];

        $token = '';

        for ($i = 0; $i < 16; $i++) {
            $token = $token . random_int(1, 10);
        }

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if ($user == null) {
            $this->get('session')->getFlashBag()->add('error', "L'adresse email : " . $user['email'] . " n'est associée à aucun compte");
            return $this->redirectToRoute('app_login');
        }

        $user->setTokenReset($token);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $message = (new \Swift_Message('Réinitialisation du mot de passe'))
            ->setFrom('contact@rekilibre.fr')
            ->setTo($email)
            ->setBody(
                $this->renderView(
                    'email/template/forgot_password.html.twig',
                    ['link' => $this->generateUrl('app_reset_password', ['id' => $user->getId(), 'token' => $token], UrlGeneratorInterface::ABSOLUTE_URL)]
                ),
                'text/html'
            );

        $mailer->send($message);

        return $this->render('email/forgot_password_info.html.twig', [
            'controller_name' => 'EmailController',
            'email' => $user->getEmail(),
        ]);
    }
}
