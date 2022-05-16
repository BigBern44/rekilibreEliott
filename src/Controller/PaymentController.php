<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AddPaymentsType;
use App\Form\PaymentsType;
use App\Form\ModifyPaymentType;
use App\Form\ParametrageDateType;
use App\Entity\Payment;
use App\Entity\User;
use App\Entity\Year;
use App\Repository\YearRepository; //.php


class PaymentController extends AbstractController
{
    /**
     * @Route("/administration/paiements/ajouter", name="add_payments")
     */
    public function add(Request $request)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);
        $fromDate = $request->query->get('fromDate', '');
        $toDate = $request->query->get('toDate', '');

        for ($i = 0; $i < count($request->get('add_payments')['payments']); $i++) {
            $payments['payments'][$i] = new Payment();
        }
        

        $form = $this->createForm(AddPaymentsType::class, $payments);
        $form->handleRequest($request);
        $dates = "";
        $paiements = array();
        $ligne = array();
        $i = 0;
        if ($form->isSubmitted() && $form->isValid()) {
            $userid = $form->get('user')->getData()->getId();
            $payments = $form->get('payments')->getData();
            foreach($payments as $info){
                $ligne[0] = $info->getDate()->format('Y-m-d');
                $ligne[1] = $info->getValue();
                $ligne[2] = $info->getType();
                $paiements[$i] = $ligne;
                $i++;
            }
            try{
                $repository = $this->getDoctrine()->getRepository(Payment::class);
            
                foreach($paiements as $ligne){
                    if ($ligne[0] != null){
                        $date = $ligne[0];
                        $value = $ligne[1];
                        $type = $ligne[2];
                        $repository->ajouterPaiement($userid, $type, $date, $value);

                    }
                }

            }
            catch (Exception $e) {
                $this->get('session')->getFlashBag()->add('error', 'Impossible d ajouter le paiement');
            }

               
            
            
        }
        

        /*if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            foreach ($payments['payments'] as $payment) {
                $payment->setUser($user);
                $payment->setDate(new \DateTime($date));
                $entityManager->persist($payment);
            }
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Paiement(s) ajouté(s)');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Une erreur est survenue: valeur manquante');
        } */

        return $this->redirectToRoute('payments',['search' => $search, 'page' => $page, 'fromDate'=>$fromDate, 'toDate'=>$toDate]);
    }

    /**
     * @Route("/administration/paiements/modifier/{id}", name="modify_payment")
     */
    public function modify(Request $request, $id)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);
        $fromDate = $request->query->get('fromDate', '');
        $toDate = $request->query->get('toDate', '');

        $payment = $this->getDoctrine()
            ->getRepository(Payment::class)
            ->findOneBy(['id' => $id]);

        $form = $this->createForm(ModifyPaymentType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($payment);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Paiement modifié');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Une erreur est survenue');
        }

        return $this->redirectToRoute('payments',['search' => $search, 'page' => $page, 'fromDate'=>$fromDate, 'toDate'=>$toDate]);
    }

    /**
     * @Route("/administration/paiements/supprimer/{id}", name="delete_payment")
     */
    public function delete(Request $request, $id)
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);
        $fromDate = $request->query->get('fromDate', '');
        $toDate = $request->query->get('toDate', '');

        $payment = $this->getDoctrine()
            ->getRepository(Payment::class)
            ->findOneBy(['id' => $id]);

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($payment);
            $entityManager->flush();
            $this->get('session')->getFlashBag()->add('success', 'Paiement supprimé');
        } catch (Exception $e) {
            $this->get('session')->getFlashBag()->add('error', 'Impossible de supprimer le paiement');
        }

        return $this->redirectToRoute('payments',['search' => $search, 'page' => $page, 'fromDate'=>$fromDate, 'toDate'=>$toDate]);
    }

    /**
     * @Route("/administration/paiements/parametrage", name="parameters_payment")
     */

    public function parameters(Request $request)
    {
        /*$year = $this->getDoctrine()
            ->getRepository(Year::class)
            ->findAll();*/


        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        $repository = $this->getDoctrine()->getRepository(Year::class);
        $form = $this->createForm(ParametrageDateType::class, null);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $test_missing = $form->get('annee')->getData();
            if ($test_missing == null){
                $fromDate01 = $form->get('firstDate')->getData()->format('y-m-d');
                $fromDate02 = $form->get('secondDate')->getData()->format('y-m-d');
                $fromDate03 = $form->get('thirdDate')->getData()->format('y-m-d');
                $repository->updateYearMissing($fromDate01, $fromDate02, $fromDate03);
            }
            
            else {
                $year = $form->get('annee')->getData()->getYear();
                $fromDate01 = $form->get('firstDate')->getData()->format('y-m-d');
                $fromDate02 = $form->get('secondDate')->getData()->format('y-m-d');
                $fromDate03 = $form->get('thirdDate')->getData()->format('y-m-d');
                $repository->updateYear($year, $fromDate01, $fromDate02, $fromDate03);
            }
            
        }

        return $this->redirectToRoute('payments',['search' => $search, 'page' => $page]);

    }
}
