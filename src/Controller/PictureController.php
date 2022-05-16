<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use App\Form\PictureType;
use App\Form\ModifyPictureType;
use App\Entity\Picture;

class PictureController extends AbstractController
{
    /**
     * @Route("/administration/images/ajouter", name="add_pictures")
     */
    public function add(Request $request)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        $picture = new Picture();

        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $file = $picture->getFile();

            $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

            try {
                $file->move(
                    $this->getParameter('pictures_directory'),
                    $fileName
                );
            } catch (FileException $e) {
                $this->get('session')->getFlashBag()->add('error', 'Impossible d\'envoyer l\'image');
            }

            $picture->setUrl($fileName);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($picture);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Image ajoutée');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Une erreur est survenue: valeur manquante');
        }

        return $this->redirectToRoute('pictures',['search' => $search, 'page' => $page]);
    }

    /**
     * @Route("/administration/images/modifier/{id}", name="modify_picture")
     */
    public function modify(Request $request, $id)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        $picture = $this->getDoctrine()
            ->getRepository(Picture::class)
            ->findOneBy(['id' => $id]);

        $pictureUrl = $picture->getUrl();

        $form = $this->createForm(ModifyPictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $picture->getFile();

            if ($file != null){
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
                if ($fileName != $pictureUrl) {
                    try {
                        unlink(realpath($this->getParameter('pictures_directory').$pictureUrl));
    
                        $file->move(
                            $this->getParameter('pictures_directory'),
                            $pictureUrl
                        );
                    } catch (FileException $e) {
                        $this->get('session')->getFlashBag()->add('error', 'Impossible d\'envoyer l\'image');
                    }
                }
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($picture);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Image modifiée');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Une erreur est survenue');
        }

        return $this->redirectToRoute('pictures',['search' => $search, 'page' => $page]);
    }

    /**
     * @Route("/administration/images/supprimer/{id}", name="delete_picture")
     */
    public function delete(Request $request, $id)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        $picture = $this->getDoctrine()
            ->getRepository(Picture::class)
            ->findOneBy(['id' => $id]);

        try {
            unlink(realpath($this->getParameter('pictures_directory').$picture->getUrl()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($picture);
            $entityManager->flush();
            $this->get('session')->getFlashBag()->add('success', 'Image supprimée');
        } catch (Exception $e) {
            $this->get('session')->getFlashBag()->add('error', 'Impossible de supprimer l\'image)');
        }

        return $this->redirectToRoute('pictures',['search' => $search, 'page' => $page]);
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }
}
