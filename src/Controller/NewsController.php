<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use DateTime;
use App\Entity\News;
use App\Form\NewsType;


class NewsController extends AbstractController
{
    /**
     * @Route("/actualites", name="news")
     */
    public function index()
    {
        $allnews = $this->getDoctrine()
            ->getRepository(News::class)
            ->findAll();

        $frenchMonths = [
            "01" => "Janvier",
            "02" => "Février",
            "03" => "Mars",
            "04" => "Avril",
            "05" => "Mai",
            "06" => "Juin",
            "07" => "Juillet",
            "08" => "Août",
            "09" => "Septembre",
            "10" => "Octobre",
            "11" => "Novembre",
            "12" => "Décembre"
        ];

        $news = new News();

        $form = $this->createForm(
            NewsType::class,
            $news,
            ['action' => $this->generateUrl('add-news')]
        );

        $formModifyAllNews = null;
        foreach ($allnews as $key => $news) {
            $formModifyAllNews[$key] = $this->createForm(NewsType::class, $news, ['action' => $this->generateUrl('modify-news', ['id' => $news->getId()])])->createView();
        }

        return $this->render('news/index.html.twig', [
            'controller_name' => 'NewsController',
            'form' => $form->createView(),
            'allnews' => $allnews,
            'frenchMonths' => $frenchMonths,
            'formModifyAllNews' => $formModifyAllNews,
        ]);
    }

    /**
     * @Route("/administration/actualites/ajouter", name="add-news")
     */
    public function add(Request $request)
    {

        $news = new News();
        $form = $this->createForm(NewsType::class, $news);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $news = $form->getData();
            $news->setDate((new DateTime()));
            $entityManager->persist($news);
            $entityManager->flush();
            $this->get('session')->getFlashBag()->add('success', 'Actualité ajoutée');
        }

        return $this->redirectToRoute('news');
    }

    /**
     * @Route("/administration/actualites/modifier/{id}", name="modify-news")
     */
    public function modify(Request $request, $id)
    {
        $news = $this->getDoctrine()
            ->getRepository(News::class)
            ->find($id);

        $form = $this->createForm(NewsType::class, $news);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($news);
            $entityManager->flush();
            $this->get('session')->getFlashBag()->add('success', 'Actualité modifiée');
        }

        return $this->redirectToRoute('news');
    }

    /**
     * @Route("/administration/actualites/supprimer/{id}", name="delete-news")
     */
    public function delete($id)
    {

        $news = $this->getDoctrine()
            ->getRepository(News::class)
            ->find($id);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($news);

        $entityManager->flush();

        $this->get('session')->getFlashBag()->add('success', 'Actualité supprimée');

        return $this->redirectToRoute('news');
    }
}
