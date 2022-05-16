<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\AddIntervenerType;
use App\Form\ChoixDateListeType;
use App\Entity\User;
use App\Entity\Activity;
use App\Form\IntervenerDescriptionType;
use Symfony\Component\Translation\TranslatorInterface;

class IntervenerController extends AbstractController
{
    /**
     * @Route("/intervenants", name="interveners")
     */
    public function index()
    {
        $interveners = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAllInterveners();

        return $this->render('intervener/index.html.twig', [
            'controller_name' => 'IntervenerController',
            'interveners' => $interveners,
        ]);
    }

    /**
     * @Route("/interveners/detail/{id}", name="detail_intervener")
     */
    public function display($id)
    {
        $intervener = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(array('id' => $id));

        $formDescription = $this->createForm(IntervenerDescriptionType::class, $intervener, ['action' => $this->generateUrl('modify_description_intervener',['id' => $intervener->getId()])]);

        return $this->render('intervener/detail.html.twig', [
            'controller_name' => 'IntervenerController',
            'intervener' => $intervener,
            'formDescription' => $formDescription->createView(),
        ]);
    }

    /**
     * @Route("/interveners/modifier/description/{id}", name="modify_description_intervener")
     */
    public function modfifyDescription(Request $request, $id)
    {
        $intervener = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(array('id' => $id));

        $formDescription = $this->createForm(IntervenerDescriptionType::class, $intervener);
        $formDescription->handleRequest($request);

        if ($formDescription->isValid() && $formDescription->isSubmitted()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($intervener);
            $entityManager->flush();
        }

        return $this->redirectToRoute('detail_intervener',['id' => $intervener->getId()]);
    }

    /**
     * @Route("/administration/intervenants/ajouter", name="add_intervener")
     */
    public function add(Request $request, UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $translator)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        $user = new User();
        $form = $this->createForm(AddIntervenerType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setIntervener(true);
            $user->setAnonymous(true);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Intervenant ajouté');
        } else {
            foreach($form->getErrors(true) as $error){
                $this->get('session')->getFlashBag()->add('error',$error->getOrigin()->getConfig()->getOption("label") . ': ' . $translator->trans($error->getMessage(), array(), 'validators'));
            }
        }

        return $this->redirectToRoute('admin_interveners',['search' => $search, 'page' => $page]);
    }


    /**
     * @Route("/administration/intervenants/modifier/{id}", name="modify_intervener")
     */
    public function modify(Request $request, UserPasswordEncoderInterface $passwordEncoder, $id)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        $intervener = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['id' => $id]);

        $intervenerPassword = $intervener->getPassword();

        $form = $this->createForm(AddIntervenerType::class, $intervener);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if(count($form->get('plainPassword')->getData())){
                $intervener->setPassword(
                    $passwordEncoder->encodePassword(
                        $intervener,
                        $form->get('plainPassword')->getData()
                    )
                );
            }
            else{
                $intervener->setPassword($intervenerPassword);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($intervener);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Intervenant modifié');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Une erreur est survenue');
        }

        return $this->redirectToRoute('admin_interveners',['search' => $search, 'page' => $page]);
    }

    /**
     * @Route("/administration/intervenants/supprimer/{id}", name="delete_intervener")
     */
    public function delete(Request $request, $id)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        $intervener = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['id' => $id]);

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($intervener);
            $entityManager->flush();
            $this->get('session')->getFlashBag()->add('success', 'Intervenant supprimé');
        } catch (Exception $e) {
            $this->get('session')->getFlashBag()->add('error', 'Impossible de supprimer l\'intervenant');
        }

        return $this->redirectToRoute('admin_interveners',['search' => $search, 'page' => $page]);
    }
    /**
     * @Route("/administration/intervenants/liste/{id}", name="liste_intervener")
     */
    public function list(Request $request, $id)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        $intervener = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['id' => $id]);

        $form = $this->createForm(ChoixDateListeType::class, ['id' => $intervener->getId()]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $retourLigne = "\n";
            $fromDate = $form->get('FromDate')->getData(); 
            $fromDate= $fromDate->format('y-m-d');
            $toDate = $form->get('ToDate')->getData();
            $toDate = $toDate->format('y-m-d');
            $id = $form->get('Id')->getData();
            try {
                $repository = $this->getDoctrine()->getRepository(Activity::class);
                $contenu = $repository->findPaginationByDate($fromDate, $toDate, $id);
                //
                $premParcours = true; //vraie si c'est le premier parcours de la collection $chaine
                $titre = ''; //titre de l'activité 
                $dateDebBrut = "";
                $dateFinBrut = "";
                $tpsDeb = "";
                $tpsFin = "";
                $result = array(); //collection multidimensionnelle renvoyée
                $subArray= array(); //collection de chaînes contenant les détails d'une activité (titre et participants)
                $i = 1; //variable de parcours de la collection
                $j = 0;
                $resultat = ""; 
                //
                foreach($contenu as $ligne)
                {
                    if($ligne['name'] == $titre && $ligne['from_date_time'] == $dateDebBrut && $ligne['from_time'] == $tpsDeb)
                    {
                        $subArray[$i] = $ligne['firstname'].' '.$ligne['surname'];
                        //die(var_dump($subArray));
                        $i= $i+1;
                    }
                    else
                    {
                        $result[$j] = $subArray;
                        $j++;
                        $subArray = array();
                        $titre = $ligne['name'];
                        $dateDebBrut = $ligne['from_date_time'];
                        $dateDeb ='';
                        for ($i = 0; $i <= 9; $i++) {
                            $dateDeb = $dateDeb.$dateDebBrut[$i];
                        }
                        $dateFin ='';
                        $dateFinBrut = $ligne['to_date_time'];
                        for ($i = 0; $i <= 9; $i++) {
                            $dateFin = $dateFin.$dateFinBrut[$i];
                        }
                        $tpsDeb = $ligne['from_time'];
                        $tpsFin = $ligne['to_time'];
                        $day = $ligne['day'];
                        $jour = "";
                        $journee = "";
                        switch($day)
                        {
                            case 1:
                                $jour=" Le lundi ";
                                break;
                            case 2:
                                $jour=" Le mardi ";
                                break;
                            case 3:
                                $jour=" Le mercredi ";
                                break;
                            case 4:
                                $jour=" Le jeudi ";
                                break;
                            case 5:
                                $jour=" Le vendredi ";
                                break;
                            case 6:
                                $jour=" Le samedi ";
                                break;
                            case 7:
                                $jour=" Le dimanche ";
                                break;
                        }
                        if($day == null){
                            $journee = $dateDeb;
                        }
                        else{
                            $journee = $dateDeb.' au '.$dateFin;
                        }
                        $entete = '<center><h1>'.$titre.' du '.$journee.$retourLigne.$jour.'de '.$tpsDeb.' à '.$tpsFin.'</h1>'.'</center>';
                        $subArray[0] = $entete;
                        $subArray[1] = '<h2>Inscrits : </h2>'.$ligne['firstname'].' '.$ligne['surname'];
                        $i= $i+1;
                        //die(var_dump($subArray));
                    }
                    
                    //die(var_dump($result));
                }
                $result[$j] = $subArray;
                //
                foreach($result as $activity)
                {
                    $i=1;
                    foreach($activity as $element)
                    {
                        $resultat = $resultat.$element.$retourLigne;
                    }
                    //$resultat = $resultat.'\n';
                }
                /*foreach ($contenu as $requete ) {
                    
                    $resultat = $resultat.$requete['name'].",".$requete['from_date_time'].",".$requete['to_date_time'].",".$requete['surname'].",".$requete['firstname'];                   
                    $resultat = $resultat."\n";
                }*/
                $resultat = nl2br($resultat);
                $intervener->exportIntervenerToPDF($resultat);
                
            }
            catch (Exception $e) {
                $this->get('session')->getFlashBag()->add('error', 'Impossible de supprimer l\'intervenant');
            }
              
        }
        
        die();
       
       return $this->redirectToRoute('admin_interveners',['search' => $search, 'page' => $page]);
    }

    public function resultatToString(Array $contenu): ?String {
        $resultat = "";
        foreach ($contenu as $requete ) {
            foreach ($requete as $ligne){
                $resultat = $resultat.ligne[0].",".ligne[1];
            }
            $resultat = $resultat."\n";
        }
        $resultat = nl2br($resultat);
        return($resultat);
    }
}
