<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Location;
use App\Form\LocationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

class LocationController extends AbstractController
{
    /**
     * @Route("/administration/salles/ajouter", name="add_location")
     */
    public function add(Request $request, TranslatorInterface $translator)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($location);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Salle ajoutée');
        } else {
            foreach($form->getErrors(true) as $error){
                $this->get('session')->getFlashBag()->add('error',$error->getOrigin()->getConfig()->getOption("label") . ': ' . $translator->trans($error->getMessage(), array(), 'validators'));
            }
        }

        return $this->redirectToRoute('locations',['search' => $search, 'page' => $page]);
    }

    /**
     * @Route("/administration/salles/modifier/{id}", name="modify_location")
     */
    public function modify(Request $request, $id)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        $location = $this->getDoctrine()
            ->getRepository(Location::class)
            ->findOneBy(['id' => $id]);

        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($location);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Salle modifiée');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Une erreur est survenue: valeur manquante');
        }

        return $this->redirectToRoute('locations',['search' => $search, 'page' => $page]);
    }

    /**
     * @Route("/administration/salles/supprimer/{id}", name="delete_location")
     */
    public function delete(Request $request, $id)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        $location = $this->getDoctrine()
            ->getRepository(Location::class)
            ->findOneBy(['id' => $id]);

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($location);
            $entityManager->flush();
            $this->get('session')->getFlashBag()->add('success', 'Salle supprimée');
        } catch (Exception $e) {
            $this->get('session')->getFlashBag()->add('error', 'Impossible de supprimer la salle');
        }

        return $this->redirectToRoute('locations',['search' => $search, 'page' => $page]);
    }
}
