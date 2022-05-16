<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UserPersonalType;
use App\Form\UserPasswordType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PersonalSpaceController extends AbstractController
{
    /**
     * @Route("/utilisateur/espace/personnel", name="personal_space")
     */
    public function index()
    {
        $user = $this->getUser();
        $formUser = $this->createForm(UserPersonalType::class, $user, ['action' => $this->generateUrl('modify_personal_informations',['id' => $user->getId()])]);
        $formUserPassword = $this->createForm(UserPasswordType::class, $user, ['action' => $this->generateUrl('modify_password',['id' => $user->getId()])]);

        return $this->render('personal_space/index.html.twig', [
            'controller_name' => 'PersonalSpaceController',
            'user' => $user,
            'formUser' => $formUser->createView(),
            'formUserPassword' => $formUserPassword->createView(),
        ]);
    }

    /**
     * @Route("/utilisateur/espace/personnel/informations/modifier/{id}", name="modify_personal_informations")
     */
    public function modifyPersonalInformations(Request $request, $id)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['id' => $id]);

        $form = $this->createForm(UserPersonalType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Informations modifiées');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Une erreur est survenue');
        }

        return $this->redirectToRoute('personal_space');
    }

    /**
     * @Route("/utilisateur/espace/personnel/mdp/modifier/{id}", name="modify_password")
     */
    public function modifyPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, $id)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['id' => $id]);

        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Mot de passe modifié');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Une erreur est survenue');
        }

        return $this->redirectToRoute('personal_space');
    }
}
