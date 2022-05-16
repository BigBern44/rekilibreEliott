<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Payment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Service\Constants\DayString;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Options;
use Dompdf\Dompdf;
use Symfony\Component\HttpFoundation\Request;

class ExportController extends AbstractController
{
    /**
     * @Route("/administration/export/users", name="export_users")
     */
    public function users()
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        $spreadsheet = new Spreadsheet();

        $titleColumns = array('Nom', 'Prénom', 'Email', 'Genre', 'Adresse', 'Code postal', 'Ville', 'Date de naissance', 'Téléphone', 'Adhérent', 'Intervenant', 'Association');
        $letterColumns = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L');

        $sheet = $spreadsheet->getActiveSheet();

        foreach ($titleColumns as $key => $tilte) {
            $sheet->setCellValue($letterColumns[$key] . '1', $tilte);
        }

        foreach ($users as $numberKey => $user) {
            $userInformations = array($user->getSurname(), $user->getFirstName(), $user->getEmail(), $user->getGender(), $user->getPostAddress(), $user->getZipCode(), $user->getCity(), $user->getBirthDate()->format('d/m/Y'), $user->getPhone(), $user->getStatus() ? 'Oui' : 'Non', $user->getIntervener() ? 'Oui' : 'Non', $user->getPartner());
            foreach ($userInformations as $letterKey => $tilte) {
                $sheet->setCellValue($letterColumns[$letterKey] . ($numberKey + 2), $tilte);
            }
        }

        $sheet->setTitle("Export utilisateurs");

        $writer = new Xlsx($spreadsheet);
        $fileName = 'utilisateurs_export.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/administration/export/payments", name="export_payments")
     */
    public function payments(Request $request)
    {
        $search = $request->query->get('search', '');
        $fromDate = $request->query->get('fromDate', '');
        $toDate = $request->query->get('toDate', '');

        $fromDate = \DateTime::createFromFormat('d/m/Y',$fromDate);
        $toDate = \DateTime::createFromFormat('d/m/Y',$toDate);

        $payments = $this->getDoctrine()
            ->getRepository(Payment::class)
            ->findAllExport($search,$fromDate,$toDate);

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('pdf/payments.html.twig', [
            'payments' => $payments
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("export_paiements" . ".pdf", [
            "Attachment" => true
        ]);

        return $this->redirectToRoute('payments');
    }

    /**
     * @Route("/administration/export/activites/{type}/{season}", name="export_activities")
     */
    public function activities($type,$season)
    {
        if($type == 'hebdo'){
            $activities = $this->getDoctrine()
            ->getRepository(Activity::class)
            ->findAllHebdoPagination('', $season)
            ->getResult();
        }
        else{
            $activities = $this->getDoctrine()
            ->getRepository(Activity::class)
            ->findAllPonctualPagination('', $season)
            ->getResult();
        }
        
        $spreadsheet = new Spreadsheet();

        $titleColumns = array('Nom', 'Période', 'Jour', 'Horaires', 'Participants', 'Intervenants', 'Salle', 'Prix');
        $letterColumns = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');

        $sheet = $spreadsheet->getActiveSheet();

        foreach ($titleColumns as $key => $tilte) {
            $sheet->setCellValue($letterColumns[$key] . '1', $tilte);
        }

        foreach ($activities as $numberKey => $activity) {
            if($type == 'hebdo'){
                $jour = DayString::DAYSTRING[$activity->getDay()];
            }
            else{
                $jour = 'X';
            }
            $userInformations = [
                $activity->getName(),
                $activity->getFromDateTime()->format('d/m/Y') . " au " . $activity->getToDateTime()->format('d/m/Y'),
                $jour,
                $activity->getFromTime()->format('H:i') . ' à ' . $activity->getToTime()->format('H:i'),
                count($activity->getUsers()),
                count($activity->getInterveners()),
                $activity->getLocation()->getName(),
                $activity->getPrice(),
            ];
            foreach ($userInformations as $letterKey => $tilte) {
                $sheet->setCellValue($letterColumns[$letterKey] . ($numberKey + 2), $tilte);
            }
        }

        $sheet->setTitle("Export activités");

        $writer = new Xlsx($spreadsheet);
        $fileName = 'activites_export.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
