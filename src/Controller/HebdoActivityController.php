<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Activity;
use App\Form\AddHebdoActivityType;
use Symfony\Component\Translation\TranslatorInterface;

class HebdoActivityController extends AbstractController
{
    /**
     * @Route("/administration/activites/hebdomadaires/ajouter", name="add_hebdo_activity")
     */
    public function add(Request $request, TranslatorInterface $translator)
    {
        $search = $request->query->get('search', '');
        $season = $request->query->get('season', '');
        $page = $request->query->getInt('page', 1);

        $hebdoActivity = new Activity();

        $form = $this->createForm(AddHebdoActivityType::class, $hebdoActivity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $hebdoActivity->setType('hebdo');

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($hebdoActivity);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Activité hebdomadaire ajoutée');
        } else {
            foreach($form->getErrors(true) as $error){
                $this->get('session')->getFlashBag()->add('error',$error->getOrigin()->getConfig()->getOption("label") . ': ' . $translator->trans($error->getMessage(), array(), 'validators'));
            }
        }

        return $this->redirectToRoute('hebdo_activities',['page' => $page, 'search' => $search, 'season' => $season]);
    }

    
    /**
     * @Route("/administration/activites/hebdomadaires/modifier/{id}", name="modify_hebdo_activity")
     */
    public function modify(Request $request, $id)
    {
        $search = $request->query->get('search', '');
        $season = $request->query->get('season', '');
        $page = $request->query->getInt('page', 1);

        $hebdoActivity = $this->getDoctrine()
            ->getRepository(Activity::class)
            ->findOneBy(['id' => $id]);

        $form = $this->createForm(AddHebdoActivityType::class, $hebdoActivity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($hebdoActivity);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Activité hebdomadaire modifiée');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Une erreur est survenue');
        }

        return $this->redirectToRoute('hebdo_activities',['page' => $page, 'search' => $search, 'season' => $season]);
    }

    /**
     * @Route("/administration/activites/hebdomadaires/dupliquer/{id}", name="duplicate_hebdo_activity")
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
        $this->get('session')->getFlashBag()->add('success', 'Activité hebdomadaire dupliquée');

        return $this->redirectToRoute('hebdo_activities',['page' => $page, 'search' => $search, 'season' => $season]);
    }

    /**
     * @Route("/administration/activites/hebdomadaires/supprimer/{id}", name="delete_hebdo_activity")
     */
    public function delete(Request $request, $id)
    {
        $search = $request->query->get('search', '');
        $season = $request->query->get('season', '');
        $page = $request->query->getInt('page', 1);

        $hebdoActivity = $this->getDoctrine()
            ->getRepository(Activity::class)
            ->findOneBy(['id' => $id]);

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($hebdoActivity);
            $entityManager->flush();
            $this->get('session')->getFlashBag()->add('success', 'Activité hebdomadaire supprimée');
        } catch (Exception $e) {
            $this->get('session')->getFlashBag()->add('error', 'Impossible de supprimer l\'activité');
        }

        return $this->redirectToRoute('hebdo_activities',['page' => $page, 'search' => $search, 'season' => $season]);
    }

    /**
     * @Route("/administration/activites/hebdomadaires/exporter/{id}", name="exporter_hebdo_activity")
     */

    public function exporter(Request $request, $id)
    {
        $search = $request->query->get('search', '');
        
        $season = $request->query->get('season', '');
        $page = $request->query->getInt('page', 1);

    

        $hebdoActivity = $this->getDoctrine()
        ->getRepository(Activity::class)
        ->findOneBy(['id' => $id]);
        
        try {
            $hebdoActivity->exportToPDF();
            
        } catch (Exception $e) {
            $this->get('session')->getFlashBag()->add('error', 'Impossible de télécharger le PDF');
        }
        die();
        return $this->redirectToRoute('hebdo_activities',['page' => $page, 'search' => $search, 'season' => $season]);
    }
}
