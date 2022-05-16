<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\AddUserType;
use App\Form\AddIntervenerType;
use App\Entity\Activity;
use App\Entity\CloseSeason;
use App\Form\AddHebdoActivityType;
use App\Form\AddPonctualActivityType;
use App\Entity\Location;
use App\Form\LocationType;
use App\Entity\Payment;
use App\Form\ModifyPaymentType;
use App\Form\AddPaymentsType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Picture;
use App\Form\PictureType;
use App\Form\ChoixDateListeType;
use App\Form\ModifyPictureType;
use App\Entity\Registration;
use App\Form\RegistrationValidType;
use DateTime;
use App\Form\ParametrageDateType;
use App\Entity\Year;

class AdministrationController extends AbstractController
{
    /**
     * @Route("/administration", name="administration")
     */
    public function index()
    {
        return $this->redirectToRoute('hebdo_activities');
    }

    /**
     * @Route("/administration/activites/hebdomadaires", name="hebdo_activities")
     */
    public function hebdoActivities(Request $request, PaginatorInterface $paginator)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        $closeSeasons = $this->getDoctrine()
        ->getRepository(CloseSeason::class)
        ->findLastSeason()[0];
        
        $thisYear= $closeSeasons->getCloseDate()->format('Y');
        $season = $request->query->getInt('season', $thisYear);

        $queryHebdoActivities = $this->getDoctrine()
            ->getRepository(Activity::class)
            ->findAllHebdoPagination($search, $season);


        $idCurrentYear = $this->getDoctrine()
        ->getRepository(Year::class)
        ->fectchCurrent();

        $closeSeason = $this->getDoctrine()
        ->getRepository(CloseSeason::class)
        ->findThisYear();

        $hebdoActivity= new Activity();
        $formHebdoActivity = $this->createForm(AddHebdoActivityType::class, $hebdoActivity, ['action' => $this->generateUrl('add_hebdo_activity',['search' => $search, 'page' => $page, 'season' => $season]) ]);

        $hebdoActivities = $paginator->paginate(
            $queryHebdoActivities,
            $page,
            10
        );

        $formModifyHebdoActivities = null;
        foreach ($hebdoActivities as $key => $hebdoActivity) {
            $formModifyHebdoActivities[$key] = $this->createForm(AddHebdoActivityType::class, $hebdoActivity, ['action' => $this->generateUrl('modify_hebdo_activity', ['id' => $hebdoActivity->getId(), 'search' => $search, 'page' => $page, 'season' => $season])])->createView();
        }

        return $this->render('administration/index.html.twig', [
            'controller_name' => 'AdministrationController',
            'template' => 'administration/hebdoActivities.html.twig',
            'hebdoActivities' => $hebdoActivities,
            'formHebdoActivity' => $formHebdoActivity->createView(),
            'formModifyHebdoActivities' => $formModifyHebdoActivities,
            'search' => $search,
            'page' => $page,
            'season' => $season,
            'thisYear' => $thisYear,
            'closeSeason' => $closeSeason,
        ]);
    }

    /**
     * @Route("/administration/activites/ponctuelles", name="ponctual_activities")
     */
    public function ponctualActivities(Request $request, PaginatorInterface $paginator)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        $closeSeasons = $this->getDoctrine()
        ->getRepository(CloseSeason::class)
        ->findLastSeason()[0];

        $thisYear= $closeSeasons->getCloseDate()->format('Y');
        $season = $request->query->getInt('season', $thisYear);

        $queryPonctualActivities = $this->getDoctrine()
            ->getRepository(Activity::class)
            ->findAllPonctualPagination($search, $season);

        $ponctualActivity= new Activity();
        $formPonctualActivity = $this->createForm(AddPonctualActivityType::class, $ponctualActivity, ['action' => $this->generateUrl('add_ponctual_activity',['search' => $search, 'page' => $page, 'season' => $season])]);

        $ponctualActivities = $paginator->paginate(
            $queryPonctualActivities,
            $page,
            10
        );

        $formModifyPonctualActivities = null;
        foreach ($ponctualActivities as $key => $ponctualActivity) {
            $formModifyPonctualActivities[$key] = $this->createForm(AddPonctualActivityType::class, $ponctualActivity, ['action' => $this->generateUrl('modify_ponctual_activity', ['id' => $ponctualActivity->getId(), 'search' => $search, 'page' => $page, 'season' => $season])])->createView();
        }

        return $this->render('administration/index.html.twig', [
            'controller_name' => 'AdministrationController',
            'template' => 'administration/ponctualActivities.html.twig',
            'ponctualActivities' => $ponctualActivities,
            'formPonctualActivity' => $formPonctualActivity->createView(),
            'formModifyPonctualActivities' => $formModifyPonctualActivities,
            'search' => $search,
            'page' => $page,
            'season' => $season,
            'thisYear' => $thisYear,
        ]);
    }

    /**
     * @Route("/administration/utilisateurs", name="users")
     */
    public function users(Request $request, PaginatorInterface $paginator)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        $queryUsers = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAllUsersPagination($search);

        $user= new User();
        $formUser = $this->createForm(AddUserType::class, $user, ['action' => $this->generateUrl('add_user',['search' => $search, 'page' => $page])]);

        $users = $paginator->paginate(
            $queryUsers,
            $page,
            10
        );

        $formModifyUsers = null;
        foreach ($users as $key => $user) {
            $formModifyUsers[$key] = $this->createForm(AddUserType::class, $user, ['action' => $this->generateUrl('modify_user', ['id' => $user->getId(), 'search' => $search, 'page' => $page])])->createView();
        }

        return $this->render('administration/index.html.twig', [
            'controller_name' => 'AdministrationController',
            'template' => 'administration/users.html.twig',
            'users' => $users,
            'formUser' => $formUser->createView(),
            'formModifyUsers' => $formModifyUsers,
            'search' => $search,
            'page' => $page,
        ]);
    }

    /**
     * @Route("/administration/intervenants", name="admin_interveners")
     */
    public function interveners(Request $request, PaginatorInterface $paginator)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        $queryInterveners = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAllIntervenersPagination($search);

        $intervener= new User();
        $formIntervener = $this->createForm(AddIntervenerType::class, $intervener, ['action' => $this->generateUrl('add_intervener',['search' => $search, 'page' => $page])]);

        $interveners = $paginator->paginate(
            $queryInterveners,
            $page,
            10
        );

        $formModifyInterveners = null;
        $formListeInterveners = null;
        foreach ($interveners as $key => $intervener) {
            $formListeInterveners[$key] = $this->createForm(ChoixDateListeType::class, ['id' => $intervener->getId()], ['action' => $this->generateUrl('liste_intervener', ['id' => $intervener->getId()])])->createView();
            $formModifyInterveners[$key] = $this->createForm(AddIntervenerType::class, $intervener, ['action' => $this->generateUrl('modify_intervener', ['id' => $intervener->getId(), 'search' => $search, 'page' => $page])])->createView();
        }


        
        

        return $this->render('administration/index.html.twig', [
            'controller_name' => 'AdministrationController',
            'template' => 'administration/interveners.html.twig',
            'interveners' => $interveners,
            'formIntervener' => $formIntervener->createView(),
            'formModifyInterveners' => $formModifyInterveners,
            'formListeInterveners' => $formListeInterveners,
            'search' => $search,
            'page' => $page,
        ]);
    }

    /**
     * @Route("/administration/salles", name="locations")
     */
    public function locations(Request $request, PaginatorInterface $paginator)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        $queryLocations = $this->getDoctrine()
            ->getRepository(Location::class)
            ->findAllPagination($search);

        $location= new Location();
        $formLocation = $this->createForm(LocationType::class, $location, ['action' => $this->generateUrl('add_location',['search' => $search, 'page' => $page])]);

        $locations = $paginator->paginate(
            $queryLocations,
            $page,
            10
        );

        $formModifyLocations = null;
        foreach ($locations as $key => $location) {
            $formModifyLocations[$key] = $this->createForm(LocationType::class, $location, ['action' => $this->generateUrl('modify_location', ['id' => $location->getId(), 'search' => $search, 'page' => $page])])->createView();
        }

        return $this->render('administration/index.html.twig', [
            'controller_name' => 'AdministrationController',
            'template' => 'administration/locations.html.twig',
            'locations' => $locations,
            'formLocation' => $formLocation->createView(),
            'formModifyLocations' => $formModifyLocations,
            'search' => $search,
            'page' => $page,
        ]);
    }

    /**
     * @Route("/administration/paiements", name="payments")
     */
    public function payments(Request $request, PaginatorInterface $paginator)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);
        $fromDate = $request->query->get('fromDate', '');
        $toDate = $request->query->get('toDate', '');
        $btnSubmit = $request->query->get('submit', '');

        if($btnSubmit=='export'){
            return $this->redirectToRoute('export_payments',[
                'search' => $search,
                'fromDate' => $fromDate,
                'toDate' => $toDate,
            ]);
        }

        $closeSeasons = $this->getDoctrine()
        ->getRepository(CloseSeason::class)
        ->findLastSeason()[0];

        if($fromDate==''){
            $fromDate = $closeSeasons->getCloseDate()->format('d/m/Y');
        }
        if($toDate==''){
            $toDate = $closeSeasons->getCloseDate()->format('d/m/').($closeSeasons->getCloseDate()->format('Y')+1);
        }

        $fromDate = \DateTime::createFromFormat('d/m/Y',$fromDate);
        $toDate = \DateTime::createFromFormat('d/m/Y',$toDate);

        $queryPayments = $this->getDoctrine()
            ->getRepository(Payment::class)
            ->findAllPagination($search,$fromDate,$toDate);

        $repository = $this->getDoctrine()->getRepository(Year::class);


        $dates =  $repository->fetchDate();

        $date = $dates[0];

        


        $date1 = $date->getFirstDate();
        $date2 = $date->getSecondDate();
        $date3 = $date->getThirdDate();

        $payments['payments'][0] = new Payment();
        $payments['payments'][1] = new Payment();
        $payments['payments'][2] = new Payment();
        $payments['payments'][3] = new Payment();
        $payments['payments'][1]->setDate($date1);
        $payments['payments'][2]->setDate($date2);
        $payments['payments'][3]->setDate($date3);
        $formPayments = $this->createForm(AddPaymentsType::class, $payments, ['action' => $this->generateUrl('add_payments',['search' => $search, 'page' => $page, 'fromDate' => $fromDate->format('d/m/Y'), 'toDate' => $toDate->format('d/m/Y')])]);
        $payments = $paginator->paginate(
            $queryPayments,
            $page,
            10
        );
        
        
        $formParameters = $this->createForm(ParametragedateType::class, null, ['action' => $this->generateUrl('parameters_payment')])->createView();
        $formModifyPayments = null;
        foreach ($payments as $key => $payment) {
            $formModifyPayments[$key] = $this->createForm(ModifyPaymentType::class, $payment, ['action' => $this->generateUrl('modify_payment', ['id' => $payment->getId(), 'search' => $search, 'page' => $page, 'fromDate' => $fromDate->format('d/m/Y'), 'toDate' => $toDate->format('d/m/Y')])])->createView();
        }

        return $this->render('administration/index.html.twig', [
            'controller_name' => 'AdministrationController',
            'template' => 'administration/payments.html.twig',
            'payments' => $payments,
            'formPayments' => $formPayments->createView(),
            'formModifyPayments' => $formModifyPayments,
            'formParameters' => $formParameters,
            'search' => $search,
            'page' => $page,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
        ]);
    }

    /**
     * @Route("/administration/images", name="pictures")
     */
    public function pictures(Request $request, PaginatorInterface $paginator)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        $queryPictures = $this->getDoctrine()
            ->getRepository(Picture::class)
            ->findAllPagination($search);

        $picture = new Picture();
        $formPicture = $this->createForm(PictureType::class, $picture, ['action' => $this->generateUrl('add_pictures',['search' => $search, 'page' => $page])]);

        $pictures = $paginator->paginate(
            $queryPictures,
            $page,
            10
        );

        $formModifyPictures = null;
        foreach ($pictures as $key => $picture) {
            $formModifyPictures[$key] = $this->createForm(ModifyPictureType::class, $picture, ['action' => $this->generateUrl('modify_picture', ['id' => $picture->getId(), 'search' => $search, 'page' => $page])])->createView();
        }

        return $this->render('administration/index.html.twig', [
            'controller_name' => 'AdministrationController',
            'template' => 'administration/pictures.html.twig',
            'pictures' => $pictures,
            'formPicture' => $formPicture->createView(),
            'formModifyPictures' => $formModifyPictures,
            'search' => $search,
            'page' => $page,
        ]);
    }

    /**
     * @Route("/administration/adhesions", name="registrations")
     */
    public function registrations(Request $request, PaginatorInterface $paginator)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        $closeSeasons = $this->getDoctrine()
        ->getRepository(CloseSeason::class)
        ->findLastSeason()[0];

        $queryRegistrations = $this->getDoctrine()
            ->getRepository(Registration::class)
            ->findPaginationNonAdherent($search, $closeSeasons->getCloseDate()->format('y-m-d'));


        $registrations = $paginator->paginate(
            $queryRegistrations,
            $page,
            10
        );

        $formValidRegistrations = null;
        $payments['payments'][0] = new Payment();
        foreach ($registrations as $registration) {
            $formValidRegistrations[$registration->getId()] = $this->createForm(RegistrationValidType::class, $payments, ['action' => $this->generateUrl('registration_valid', ['id' => $registration->getId(), 'search' => $search, 'page' => $page])])->createView();
        }

        return $this->render('administration/index.html.twig', [
            'controller_name' => 'AdministrationController',
            'template' => 'administration/registrations.html.twig',
            'registrations' => $registrations,
            'formValidRegistrations' => $formValidRegistrations,
            'search' => $search,
            'page' => $page,
        ]);
    }

    /**
     * @Route("super/administration/saison/cloturer", name="close_season")
     */
    public function closeSeason(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();

        $closeSeason = new CloseSeason();
        $closeSeason->setCloseDate(new \DateTime());
        $em->persist($closeSeason);

        $users = $em->getRepository(User::class)->findAll();

        foreach($users as $user){
            $user->removeRoleMember();
            $user->setStatus(false);
            $em->persist($user);
        }

        $em->flush();
        $this->get('session')->getFlashBag()->add('success', 'Saison clôturée');

        return $this->redirectToRoute('administration');
    }
}
