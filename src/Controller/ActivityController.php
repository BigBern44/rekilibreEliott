<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Activity;
use App\Entity\CloseSeason;
use App\Entity\User;
use App\Form\ActivityDescriptionType;
use Symfony\Component\HttpFoundation\Request;

class ActivityController extends AbstractController
{
    /**
     * @Route("/activites", name="activities")
     */
    public function index(Request $request)
    {
        $type = $request->query->get('type', 'ponctual');
        $closeSeasons = $this->getDoctrine()
        ->getRepository(CloseSeason::class)
        ->findLastSeason()[0];

        if ($type == 'hebdo') {
            $activities = $this->getDoctrine()
                ->getRepository(Activity::class)
                ->findAllHebdo($closeSeasons->getCloseDate());
        } else {
            $activities = $this->getDoctrine()
                ->getRepository(Activity::class)
                ->findAllPonctual();
        }
        
        return $this->render('activity/index.html.twig', [
            'controller_name' => 'ActivitiesController',
            'activities' => $activities,
            'activityType' => $type,
        ]);
    }

    /**
     * @Route("/activites/detail/{id}", name="detail_activity")
     */
    public function display($id)
    {
        $activity = $this->getDoctrine()
            ->getRepository(Activity::class)
            ->findOneBy(array('id' => $id));

        $formDescription = $this->createForm(ActivityDescriptionType::class, $activity, ['action' => $this->generateUrl('modify_description_activity', ['id' => $activity->getId()])]);

        $page = 'activity/detailPonctual.html.twig';
        if ($activity->getType() == 'hebdo') {
            $page = 'activity/detailHebdo.html.twig';
        }

        return $this->render('activity/detail.html.twig', [
            'controller_name' => 'ActivitiesController',
            'activity' => $activity,
            'formDescription' => $formDescription->createView(),
            'page' => $page,
        ]);
    }

    /**
     * @Route("/activites/modifier/description/{id}", name="modify_description_activity")
     */
    public function modfifyDescription(Request $request, $id)
    {
        $activity = $this->getDoctrine()
            ->getRepository(Activity::class)
            ->findOneBy(array('id' => $id));

        $formDescription = $this->createForm(ActivityDescriptionType::class, $activity);
        $formDescription->handleRequest($request);

        if ($formDescription->isValid() && $formDescription->isSubmitted()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($activity);
            $entityManager->flush();
        }

        return $this->redirectToRoute('detail_activity', ['id' => $activity->getId()]);
    }

    /**
     * @Route("/adherent/activites/inscription/{idActivity}", name="user_register_activity")
     */
    public function userRegister($idActivity)
    {
        echo $idActivity;
        $activity = $this->getDoctrine()
            ->getRepository(Activity::class)
            ->findOneBy(array('id' => $idActivity));

        $user = $this->getUser();

        $activity->addUser($user);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($activity);

        $entityManager->flush();

        $this->get('session')->getFlashBag()->add('success', 'Inscription à l\'activité');

        return $this->redirectToRoute('detail_activity', array('id' => $idActivity));
    }

    /**
     * @Route("/adherent/activites/desinscription/{idActivity}", name="user_unregister_activity")
     */
    public function userUnregister($idActivity)
    {
        $activity = $this->getDoctrine()
            ->getRepository(Activity::class)
            ->findOneBy(array('id' => $idActivity));

        $user = $this->getUser();

        $activity->removeUser($user);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($activity);

        $entityManager->flush();

        $this->get('session')->getFlashBag()->add('success', 'Désinscription de l\'activité');

        return $this->redirectToRoute('detail_activity', array('id' => $idActivity));
    }

    
      
}
