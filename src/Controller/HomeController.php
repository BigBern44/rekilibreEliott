<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\HomeArticleType;
use App\Entity\HomeArticle;
use Symfony\Component\Form\Forms;


class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $this->get('session')->set('calendar-display-hebdo', true);
        $this->get('session')->set('calendar-display-ponctual', true);

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/accueil", name="home")
     */
    public function home(Request $request)
    {
        $homeArticles = $this->getDoctrine()
            ->getRepository(HomeArticle::class)
            ->findAll();

        foreach($homeArticles as $key => $homeArticle){
            $formHomeArticles[$key] = $this->createForm(
                HomeArticleType::class, 
                $homeArticles[$key], 
                ['action' => $this->generateUrl('update-article',
                    ['id' => $homeArticle->getId()]
                )])->createView();
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'formHomeArticles' => $formHomeArticles,
            'homeArticles' => $homeArticles
        ]);
    }

    /**
     * @Route("/administration/article/modifier", name="update-article")
     */
    public function update(Request $request)
    {
        $homeArticles = $this->getDoctrine()
            ->getRepository(HomeArticle::class)
            ->find($request->query->get('id'));

        $formHomeArticle = $this->createForm(HomeArticleType::class, $homeArticles, ['action' => $this->generateUrl('update-article')]);

        $formHomeArticle->handleRequest($request);

        if ($formHomeArticle->isSubmitted() && $formHomeArticle->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $homeArticle = $formHomeArticle->getData();

            $entityManager->persist($homeArticle);

            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Article modifié');
        }

        return $this->redirectToRoute('home');
    }
}
