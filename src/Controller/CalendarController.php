<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use App\Entity\Activity;
use App\Entity\Session;

class CalendarController extends AbstractController
{
    /**
     * @Route("/calendrier/{month}/{year}", name="calendar")
     */
    public function index($month = -1, $year = -1)
    {
        $activitiesPonctual = array();

        $activitiesPonctual = $this->getDoctrine()
            ->getRepository(Activity::class)
            ->findAllPonctualCalendar();


        $thisDate = new DateTime();

        if (-1 == $year) {
            $year = (new DateTime())->format('Y');
        }

        if (-1 == $month) {
            $month = (new DateTime())->format('m');
        } else {
            $dateObj = DateTime::createFromFormat('!m', $month);
            $month = $dateObj->format('m');
        }

        $arrayActivitiesPonctual = array(array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array());
        foreach ($activitiesPonctual as $activityPonctual) {
            if ($activityPonctual->getFromDateTime()->format('m') == $month) {
                for( $i= $activityPonctual->getFromDateTime()->format('d') - 1 ; $i < $activityPonctual->getToDateTime()->format('d') ; $i++ ){
                    array_push($arrayActivitiesPonctual[$i], $activityPonctual);
                }
            }
        }

        $numberDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        if ($month != 1) {
            $numberDaysLast = cal_days_in_month(CAL_GREGORIAN, $month - 1, $year) + 1;
        } else {
            $numberDaysLast = cal_days_in_month(CAL_GREGORIAN, 12, $year - 1) + 1;
        }

        $firstDay = date('N', mktime(0, 0, 0, $month, 1, $year));
        $currentDay = $thisDate->format('d');

        $monthName = DateTime::createFromFormat('!m', $month)->format('F');

        return $this->render('calendar/index.html.twig', [
            'controller_name' => 'CalendarController',
            'arrayActivitiesPonctual' => $arrayActivitiesPonctual,
            'monthName' => $monthName,
            'month' => $month,
            'thisDate' => $thisDate,
            'numberDays' => $numberDays,
            'firstDay' => $firstDay,
            'currentDay' => $currentDay,
            'numberDaysLast' => $numberDaysLast,
            'year' => $year,
        ]);
    }

    /**
     * @Route("/calendrier/criteres/{btnName}/{month}", name="calendar_criteria")
     */
    public function setCriteria($month = 0, $btnName)
    {
        if ($btnName == 'hebdo') {
            if ($this->get('session')->get('calendar-display-hebdo')) {
                $this->get('session')->set('calendar-display-hebdo', false);
            } else {
                $this->get('session')->set('calendar-display-hebdo', true);
            }
        }

        if ($btnName == 'ponctual') {
            if ($this->get('session')->get('calendar-display-ponctual')) {
                $this->get('session')->set('calendar-display-ponctual', false);
            } else {
                $this->get('session')->set('calendar-display-ponctual', true);
            }
        }

        return $this->redirectToRoute('calendar', ['month' => $month]);
    }
}
