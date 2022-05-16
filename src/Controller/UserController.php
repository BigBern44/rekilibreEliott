<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Form\AddUserType;
use Symfony\Component\Translation\TranslatorInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/utilisateurs/supprimer/notification/{id}", name="delete_notification")
     */
    public function deleteNotification(Request $request, $id){
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['id' => $this->getUser()->getId()]);

        return $this->redirectToRoute('users',['search' => $search, 'page' => $page]);
    }

    /**
     * @Route("/administration/utilisateurs/ajouter", name="add_user")
     */
    public function add(Request $request, UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $translator)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        $user = new User();
        $form = $this->createForm(AddUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setIntervener(false);
            $user->setAnonymous(true);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Utilisateur ajouté');
        } else {
            foreach($form->getErrors(true) as $error){
                $this->get('session')->getFlashBag()->add('error',$error->getOrigin()->getConfig()->getOption("label") . ': ' . $translator->trans($error->getMessage(), array(), 'validators'));
            }
        }

        return $this->redirectToRoute('users',['search' => $search, 'page' => $page]);
    }

    /**
     * @Route("/administration/utilisateurs/modifier/{id}", name="modify_user")
     */
    public function modify(Request $request, UserPasswordEncoderInterface $passwordEncoder, $id)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['id' => $id]);

        $userPassword = $user->getPassword();

        $form = $this->createForm(AddUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('plainPassword')->getData()){
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }
            else{
                $user->setPassword($userPassword);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Utilisateur modifié');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Une erreur est survenue');
        }

        return $this->redirectToRoute('users',['search' => $search, 'page' => $page]);
    }

    /**
     * @Route("/administration/utilisateurs/supprimer/{id}", name="delete_user")
     */
    public function delete(Request $request, $id)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['id' => $id]);

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
            $this->get('session')->getFlashBag()->add('success', 'Utilisateur supprimé');
        } catch (Exception $e) {
            $this->get('session')->getFlashBag()->add('error', 'Impossible de supprimer l\'utilisateur');
        }

        return $this->redirectToRoute('users',['search' => $search, 'page' => $page]);
    }
}
