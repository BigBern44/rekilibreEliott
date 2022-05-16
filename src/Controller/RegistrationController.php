<?php
namespace App\Controller;

use App\Entity\CloseSeason;
use App\Entity\Payment;
use App\Entity\User;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use App\Entity\Registration;
use App\Form\RegistrationStepOneType;
use App\Form\RegistrationStepTwoType;
use App\Form\RegistrationStepThreeType;
use App\Form\RegistrationStepFourType;
use App\Form\RegistrationValidType;
use App\Form\ReRegistrationType;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/inscription", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        return $this->redirectToRoute('app_register_step_one');
    }

    /**
     * @Route("/inscription/etape/1/{id}", name="app_register_step_one")
     */
    public function registerStepOne($id = 0): Response
    {
        $registration = $this->getDoctrine()
            ->getRepository(Registration::class)
            ->findOneBy([
                'id' => $id,
            ]);

        if (is_null($registration)) {
            $registration = new Registration();
        }

        $form = $this->createForm(RegistrationStepOneType::class, $registration, ['action' => $this->generateUrl('app_register_step_two')]);

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'controller_name' => 'RegistrationController',
            'step' => 1,
            'idRegistration' => $registration->getId(),
        ]);
    }

    /**
     * @Route("/inscription/etape/2/{id}", name="app_register_step_two")
     */
    public function registerStepTwo(Request $request, $id = 0): Response
    {
        $registration = $this->getDoctrine()
            ->getRepository(Registration::class)
            ->findOneBy([
                'id' => $id,
            ]);

        if (is_null($registration)) {
            $registration = new Registration();
        }

        $form = $this->createForm(RegistrationStepOneType::class, $registration);
        $form->handleRequest($request);

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy([
                'email' => $registration->getEmailAddress(),
            ]);

        if (!is_null($user)) {
            $this->get('session')->getFlashBag()->add('error', "Cette adresse email est déjà utilisée par un utilisateur");
            return $this->redirectToRoute('app_register_step_one');
        }

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $oldRegistration = $this->getDoctrine()
                    ->getRepository(Registration::class)
                    ->findOneBy([
                        'emailAddress' => $registration->getEmailAddress(),
                        'phone' => $registration->getPhone()
                    ]);

                if (!is_null($oldRegistration)) {
                    $registration = $oldRegistration;
                    $this->get('session')->getFlashBag()->add('success', "Reprise de l'inscription");
                } else {
                    $entityManager = $this->getDoctrine()->getManager();
                    $registration->setSubscriber(false);
                    $entityManager->persist($registration);
                    $entityManager->flush();
                    $this->get('session')->getFlashBag()->add('success', "Création de l'inscription");
                }
            } else {
                $this->get('session')->getFlashBag()->add('error', "Une erreur est survenue");
                return $this->redirectToRoute('app_register_step_one');
            }
        }

        $form = $this->createForm(RegistrationStepTwoType::class, $registration, ['action' => $this->generateUrl('app_register_step_three', ['id' => $registration->getId()])]);

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'controller_name' => 'RegistrationController',
            'step' => 2,
            'idRegistration' => $registration->getId(),
            'previous' => $this->generateUrl('app_register_step_one', ['id' => $registration->getId()]),
        ]);
    }

    /**
     * @Route("/inscription/etape/3/{id}", name="app_register_step_three")
     */
    public function registerStepThree(Request $request, $id = 0): Response
    {
        $registration = $this->getDoctrine()
            ->getRepository(Registration::class)
            ->findOneBy([
                'id' => $id,
            ]);

        $form = $this->createForm(RegistrationStepTwoType::class, $registration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($registration);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success', "Informations sauvegardées");
        }


        $data_closen_date = $this->getDoctrine()
        ->getRepository(CloseSeason::class)
        ->findLastSeason()[0]->getCloseDate();
        
        $registration->setDateCloseSeason($data_closen_date);


        $form = $this->createForm(RegistrationStepThreeType::class, $registration,  ['action' => $this->generateUrl('app_register_step_four', ['id' => $registration->getId() ]), 'data' => $registration]   );

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'controller_name' => 'RegistrationController',
            'step' => 3,
            'idRegistration' => $registration->getId(),
            'previous' => $this->generateUrl('app_register_step_two', ['id' => $registration->getId()]),
        ]);
    }

    /**
     * @Route("/inscription/etape/4/{id}", name="app_register_step_four")
     */
    public function registerStepFour(Request $request, $id): Response
    {
        $registration = $this->getDoctrine()
            ->getRepository(Registration::class)
            ->findOneBy([
                'id' => $id,
            ]);

        $form = $this->createForm(RegistrationStepThreeType::class, $registration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($registration);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success', "Activités sauvegardées");
        }

        $form = $this->createForm(RegistrationStepFourType::class, $registration, ['action' => $this->generateUrl('app_register_validate', ['id' => $registration->getId()])]);

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'controller_name' => 'RegistrationController',
            'step' => 4,
            'idRegistration' => $registration->getId(),
            'previous' => $this->generateUrl('app_register_step_three', ['id' => $registration->getId()]),
        ]);
    }

    /**
     * @Route("/inscription/valide/{id}", name="app_register_validate")
     */
    public function registerValidate(Request $request, UserPasswordEncoderInterface $passwordEncoder, $id): Response
    {
        $registration = $this->getDoctrine()
            ->getRepository(Registration::class)
            ->findOneBy([
                'id' => $id,
            ]);

        $form = $this->createForm(RegistrationStepFourType::class, $registration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy([
                    'email' => $registration->getEmailAddress(),
                ]);

            if (is_null($user)) {
                $user = new User();
                $user->fillWithRegistration($registration);

                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $registration->setDateCreate(new \DateTime());
                $registration->setUser($user);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($registration);
                $entityManager->persist($user);
                $entityManager->flush();

                $this->get('session')->getFlashBag()->add('success', "Votre inscription est enregistrée");
            }
        } else {
            $this->get('session')->getFlashBag()->add('error', "Une erreur est survenue");

            return $this->redirectToRoute('app_register_step_four', ['id' => $registration->getId()]);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'controller_name' => 'RegistrationController',
            'step' => 5,
            'idRegistration' => $registration->getId(),
        ]);
    }

    /**
     * @Route("/inscription/generer/pdf/{id}", name="app_register_generate_pdf")
     */
    public function generatePdf($id)
    {
        $registration = $this->getDoctrine()
            ->getRepository(Registration::class)
            ->findOneBy([
                'id' => $id,
            ]);

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('pdf/register.html.twig', [
            'registration' => $registration
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("Bulletin_adhesion_" . $registration->getFirstName() . '_' . $registration->getLastName() . ".pdf", [
            "Attachment" => true
        ]);

        return $this->redirectToRoute('app_register_validate', ['id' => $registration->getId()]);
    }

    /**
     * @Route("/inscription/valider/{id}", name="registration_valid")
     */
    public function valid(Request $request, TranslatorInterface $translator, $id)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        $registration = $this->getDoctrine()
            ->getRepository(Registration::class)
            ->findOneBy([
                'id' => $id,
            ]);

        for ($i = 0; $i < count($request->get('registration_valid')['payments']); $i++) {
            $payments['payments'][$i] = new Payment();
        }

        $payments['date'] = new \DateTime();

        $form = $this->createForm(RegistrationValidType::class, $payments);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $registration->getUser();

            $entityManager = $this->getDoctrine()->getManager();
            $repository = $this->getDoctrine()->getRepository(Registration::class);
            try {
                $status = $repository->checkStatus($user->getId());
                if ($status == null){
                    foreach ($payments['payments'] as $payment) {
                        $payment->setDate($payments['date']);
                        $payment->setUser($user);
                        $entityManager->persist($payment);
                    }

                    $registration->setDateValidate(new \DateTime());
                    $entityManager->persist($registration);

                    $roles = $user->getRoles();
                    array_push($roles,"ROLE_ADHERENT");
                    $user->setRoles($roles);
                    $user->setStatus(true);
                    foreach ($registration->getActivities() as $activity) {
                        $user->addActivity($activity);
                    }
                    $entityManager->persist($user);

                    $entityManager->flush();

                    $this->get('session')->getFlashBag()->add('success', 'Adhésion validée');
                }
                else {
            
                    echo '<script> alert(\"cet utilisateur est déja un adhérent\"); </script>';
                    
                }
            }  catch (Exception $e) {
                $this->get('session')->getFlashBag()->add('error', 'Impossible de télécharger le PDF');
            }
        } else {
            foreach ($form->getErrors(true) as $error) {
                $this->get('session')->getFlashBag()->add('error', $error->getOrigin()->getConfig()->getOption("label") . ': ' . $translator->trans($error->getMessage(), array(), 'validators'));
            }
        }

        return $this->redirectToRoute('registrations', ['search' => $search, 'page' => $page]);
    }

    /**
     * @Route("/administration/adhesion/refuser/{id}", name="refuse_registration")
     */
    public function refuse(Request $request, $id)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        $registration = $this->getDoctrine()
            ->getRepository(Registration::class)
            ->findOneBy(['id' => $id]);

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($registration);
            $entityManager->flush();
            $this->get('session')->getFlashBag()->add('success', 'Demande refusée');
        } catch (Exception $e) {
            $this->get('session')->getFlashBag()->add('error', 'Impossible de refuser la demande');
        }

        return $this->redirectToRoute('registrations', ['search' => $search, 'page' => $page]);
    }

    /**
     * @Route("/re/inscription/{id}", name="re_register")
     */
    public function reRegister(Request $request, $id): Response
    {
        $closeSeasons = $this->getDoctrine()
        ->getRepository(CloseSeason::class)
        ->findLastSeason()[0];

        $registration = $this->getDoctrine()->getRepository(Registration::class)->findSeason($closeSeasons->getCloseDate(),$this->getUser());
        $doneRegistration = false;

        if (is_null($registration)) {
            $registration = new Registration();
        }
        else{
            $doneRegistration = true;
        }

        $form = $this->createForm(ReRegistrationType::class, $registration, ['action' => $this->generateUrl('re_register', ['id' => $this->getUser()->getId()])]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $registration->setAlreadyMember(true);
            $registration->setLastname($this->getUser()->getSurname());
            $registration->setFirstname($this->getUser()->getFirstName());
            $registration->setBirthdate($this->getUser()->getBirthDate());
            $registration->setEmailAddress($this->getUser()->getEmailAddress());
            $registration->setPostAddress($this->getUser()->getPostAddress());
            $registration->setZipCode($this->getUser()->getZipCode());
            $registration->setCity($this->getUser()->getCity());
            $registration->setPhone($this->getUser()->getPhone());
            $registration->setUser($this->getUser());
            $registration->setDateCreate(new \DateTime());
            $registration->setGender($this->getUser()->getGender());
            $registration->setSubscriber(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($registration);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Réinscription effectuée');
            $doneRegistration = true;
        }

        return $this->render('registration/re_register.html.twig', [
            'controller_name' => 'RegistrationController',
            'form' => $form->createView(),
            'doneRegistration' => $doneRegistration,
            'registration' => $registration,
        ]);
    }
}
