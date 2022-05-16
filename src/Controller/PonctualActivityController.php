<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Activity;
use App\Form\AddPonctualActivityType;
use Symfony\Component\HttpFoundation\Request;

class PonctualActivityController extends AbstractController
{
    /**
     * @Route("/administration/activites/ponctuelles/ajouter", name="add_ponctual_activity")
     */
    public function add(Request $request)
    {
        $search = $request->query->get('search', '');
        $season = $request->query->get('season', '');
        $page = $request->query->getInt('page', 1);

        $ponctualActivity = new Activity();

        $form = $this->createForm(AddPonctualActivityType::class, $ponctualActivity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $ponctualActivity->setType('ponctual');

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ponctualActivity);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Activité ponctuelle ajoutée');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Une erreur est survenue: valeur manquante');
        }

        return $this->redirectToRoute('ponctual_activities',['search' => $search, 'page' => $page, 'season' => $season]);
    }

    
    /**
     * @Route("/administration/activites/ponctuelles/modifier/{id}", name="modify_ponctual_activity")
     */
    public function modify(Request $request, $id)
    {
        $search = $request->query->get('search', '');
        $season = $request->query->get('season', '');
        $page = $request->query->getInt('page', 1);

        $ponctualActivity = $this->getDoctrine()
            ->getRepository(Activity::class)
            ->findOneBy(['id' => $id]);

        $form = $this->createForm(AddPonctualActivityType::class, $ponctualActivity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ponctualActivity);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Activité poncutelle modifiée');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Une erreur est survenue');
        }

        return $this->redirectToRoute('ponctual_activities',['search' => $search, 'page' => $page, 'season' => $season]);
    }

    /**
     * @Route("/administration/activites/ponctuelles/dupliquer/{id}", name="duplicate_ponctual_activity")
     */
    public function duplicate(Request $request, $id)
    {
        $search = $request->query->get('search', '');
        $season = $request->query->get('season', '');
        $page = $request->query->getInt('page', 1);

        $activity = $this->getDoctrine()
            ->getRepository(Activity::class)
            ->findOneBy(array('id' => $id));

        $copyActivity = new Activity();
        $copyActivity->setName($activity->getName().'[Dupliquée]');
        $copyActivity->setLocation($activity->getLocation());
        $copyActivity->setMaxRegistrations($activity->getMaxRegistrations());
        $copyActivity->setType($activity->getType());
        $copyActivity->setPrice($activity->getPrice());
        foreach($activity->getInterveners() as $intervener){
            $copyActivity->addIntervener($intervener);
        }
        $copyActivity->setDay($activity->getDay());
        $copyActivity->setFromDateTime($activity->getFromDateTime());
        $copyActivity->setToDateTime($activity->getToDateTime());
        $copyActivity->setDescription($activity->getDescription());
        $copyActivity->setFromTime($activity->getFromTime());
        $copyActivity->setToTime($activity->getToTime());
        $copyActivity->setPicture($activity->getPicture());
        $copyActivity->setReiki($activity->getReiki());

        $em = $this->getDoctrine()->getManager();
        $em->persist($copyActivity);
        $em->flush();
        $this->get('session')->getFlashBag()->add('success', 'Activité ponctuelle dupliquée');

        return $this->redirectToRoute('ponctual_activities',['page' => $page, 'search' => $search, 'season' => $season]);
    }

    /**
     * @Route("/administration/activites/ponctuelles/supprimer/{id}", name="delete_ponctual_activity")
     */
    public function delete(Request $request, $id)
    {
        $search = $request->query->get('search', '');
        $season = $request->query->get('season', '');
        $page = $request->query->getInt('page', 1);

        $ponctualActivity = $this->getDoctrine()
            ->getRepository(Activity::class)
            ->findOneBy(['id' => $id]);

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ponctualActivity);
            $entityManager->flush();
            $this->get('session')->getFlashBag()->add('success', 'Activité poncutelle supprimée');
        } catch (Exception $e) {
            $this->get('session')->getFlashBag()->add('error', 'Impossible de supprimer l\'activité');
        }

        return $this->redirectToRoute('ponctual_activities',['search' => $search, 'page' => $page, 'season' => $season]);
    }

    /**
     * @Route("/administration/activites/ponctuelles/exporter/{id}", name="exporter_ponctual_activity")
     */

    public function exporter(Request $request, $id)
    {
        $search = $request->query->get('search', '');
        
        $season = $request->query->get('season', '');
        $page = $request->query->getInt('page', 1);

    

        $activity = $this->getDoctrine()
        ->getRepository(Activity::class)
        ->findOneBy(['id' => $id]);
        $html = $this->renderView('pdf/test.html.twig');
        try {
            $activity->exportToPDF();
            
        } catch (Exception $e) {
            $this->get('session')->getFlashBag()->add('error', 'Impossible de télécharger le PDF');
        }
        die();
        return $this->redirectToRoute('ponctual_activities',['page' => $page, 'search' => $search, 'season' => $season]);
    }
}
