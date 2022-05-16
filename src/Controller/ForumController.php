<?php

namespace App\Controller;

use App\Entity\User;
use Knp\Component\Pager\PaginatorInterface;
use PhpParser\Node\Expr\Array_;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Discussion;
use App\Entity\Post;
use App\Entity\CategorieDiscussion;
use App\Form\AddDiscussionType;
use App\Form\AddCategorieDiscussion;
use App\Form\SearchDiscussionType;
use App\Form\AddPostType;
use Symfony\Component\HttpFoundation\Session\Session;


Source: https://prograide.com/pregunta/48203/comment-verifier-si-un-utilisateur-est-connecte--symfony2-dans-un-contrleur

class ForumController extends AbstractController
{


    /**
     * @Route("/forum", name="forum")
     */
    public function index(Session $session, PaginatorInterface $paginator, Request $request)
    {

        // récuperation des roles de l'utilisateur sur l'application
        $user = $this->getUser();

        if (($user != null) && ( $this->getUser()->getRoles() != null)) {
            $roleUser = $this->getUser()->getRoles();
            array_push($roleUser, "ROLE_VISITEUR");
        }else{
            $roleUser = array("ROLE_VISITEUR");
        }
        // récuperation des discussion filtré par la function search

        $filteredDiscussion = $session->get('filteredDiscussion');
        if (empty($filteredDiscussion)) {

            $alldiscussion = $this->getDoctrine()
                ->getRepository(Discussion::class)
                ->findAll();
        }else{
            if (in_array("toutes les catégories", $filteredDiscussion)){

                $alldiscussion = $this->getDoctrine()
                    ->getRepository(Discussion::class)
                    ->findAll();
            }else{
                $alldiscussion = $filteredDiscussion;
            }


        }


        // recuperation des discussions visible par l'utilisateur connecté ou non

        $discussionAutorise = array();


        foreach ($alldiscussion as $discussion ) {
            // recuperation de toutes les categories de chaque discussion
            $categorieDiscussion = $discussion->getCategorieDiscussion();
            $i=0;
            $discussionValide = false;

            while(($i<count($categorieDiscussion, COUNT_NORMAL)) && ($discussionValide == false)){
                // verifie si les roles de chaque categorie récupérée correspond à ceux de l'utilisateur
                if ( !empty(array_intersect($categorieDiscussion[$i]->getRoleRequis(), $roleUser )) ){

                    $discussionValide = true;
                    array_push($discussionAutorise,$discussion);
                }
                $i++;
            }
        }
        // récuperation de toutes les categories présente dans la bdd
        $allCategories = $this->getDoctrine()
            ->getRepository(CategorieDiscussion::class)
            ->findAll();


        // Verifie les catégorie de discussion sont accessible par l'utilisateur
        $categorieAutorise = array();

        foreach ($allCategories as $categorie ) {

            if (!empty(array_intersect($categorie->getRoleRequis(), $roleUser ))){

                array_push($categorieAutorise,$categorie );

            }

        }



        // Création du formulaire pour la recherche par catégorie

        $formSearch = $this->createFormBuilder()

            ->setAction($this->generateUrl('search_discussion'))
            ->add('query', EntityType::class,
                [
                    'class' => CategorieDiscussion::class,

                    'choices'           => $categorieAutorise,
                    'choice_label'      => 'title',

                    'attr' => array(
                        'class' => 'form-select form-select-sm'
                    )

                ])

            ->add('recherche', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
            ->getForm();

        // Suppression de la catégorie "toutes les catégorie pour l'ajout de discussion

        $categorieAutoriseChoixDiscussion = array();

        foreach ($categorieAutorise as $categorie){

            if ($categorie->getId() != 1){


                array_push($categorieAutoriseChoixDiscussion,$categorie );

            }

        }


        $discussion = new Discussion();

        $discussion->setCategorieAdmise($categorieAutoriseChoixDiscussion);
        // formulaire pour ajouter une discussion

        $formdiscu = $this->createForm(
            AddDiscussionType::class,
            $discussion,
            ['action' => $this->generateUrl('add_discussion')], ['data' => $discussion]
        );


        // formulaire pour ajouter une catégorie

        $formCate = $this->createForm(
            AddCategorieDiscussion::class,
            $categorie,
            ['action' => $this->generateUrl('add_categorie')]
        );

        $alldiscussionPagine = $paginator->paginate(
            $discussionAutorise,
            $request->query->getInt('page', 1),
            6

        );


        return $this->render('forum/index.html.twig', [
            'allCategories' => $allCategories,
            'controller_name' => 'ForumController',
            'form' => $formdiscu->createView(),
            'formCate' => $formCate->createView(),
            'formSearch' => $formSearch->createView() ,
            'alldiscussion' => $alldiscussionPagine,
            'userCurrent' => $user,



        ]);
    }


    /**
     * @Route("/forum/search", name="search_discussion")
     */
    public function SearchDiscussion(Request $request, Session  $session)
    {

        $query = $request->request->get('form')['query'];
        $alldiscussion = $this->getDoctrine()
        ->getRepository(Discussion::class)
        ->findAll();

        $filteredDiscussion =  array();

        if($query != null) {

            foreach ($alldiscussion as $discussion ) {

                $discussionCategorie = $discussion->getCategorieDiscussion()->toArray();

                $discussionCategorieId = array ();


                foreach ($discussionCategorie as $categorie) {
                    array_push($discussionCategorieId, $categorie->getId());
                }
                if (in_array($query,$discussionCategorieId)){

                    array_push($filteredDiscussion,$discussion );

                }

            }
        }

        $session->set('filteredDiscussion', $filteredDiscussion);

        return $this->redirectToRoute('forum');
    }
   
    

     /**
     * @Route("/forum/ajouter", name="add_discussion")
     */
    public function add(Request $request)
    {

        $user = $this->getUser();

        if (($user != null) && ( $this->getUser()->getRoles() != null)) {
            $roleUser = $this->getUser()->getRoles();
            array_push($roleUser, "ROLE_VISITEUR");
        }else{
            $roleUser = array("ROLE_VISITEUR");
        }

        $allCategories = $this->getDoctrine()
            ->getRepository(CategorieDiscussion::class)
            ->findAll();

        $categorieAutorise = array();

        foreach ($allCategories as $categorie ) {

           if (!empty(array_intersect($categorie->getRoleRequis(), $roleUser ))){

               array_push($categorieAutorise,$categorie );

           }

        }


        $discussion = new Discussion();

        $discussion->setCategorieAdmise($categorieAutorise);


        $form = $this->createForm(AddDiscussionType::class, $discussion, ['data' => $discussion]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($user)){
                $entityManager = $this->getDoctrine()->getManager();
                $discussion = $form->getData();
    
                $discussion->setCreatedAt(new \DateTime('now+1 hour'));
                $discussion->setAuthorId($user);

                
                $entityManager->persist($discussion);
                $entityManager->flush();
                $this->get('session')->getFlashBag()->add('success', 'Discussion ajoutée vous pouvez écrire votre premier post');

                $goTo = $this->redirectToRoute('show_discussion',['id' => $discussion->getId()]);

            }else{

                $this->get('session')->getFlashBag()->add('notice', 'Veuillez vous connecter pour créer une nouvelle discussion');
                $goTo = $this->redirectToRoute('forum');
            }

                       
            
        }

        return $goTo;
        
    }

        /**
          * @Route("/forum/ajouterCatégorie", name="add_categorie")
          */
         public function addCategorie(Request $request)
         {
             $user = $this->getUser();

             $categorie = new CategorieDiscussion();

             $form = $this->createForm(AddCategorieDiscussion::class, $categorie);

             $form->handleRequest($request);

             if ($form->isSubmitted() && $form->isValid()) {

                 if (in_array('ROLE_ADMIN', $user->getRoles())){

                     $entityManager = $this->getDoctrine()->getManager();
                     $categorie = $form->getData();
                     $categorie->setColor(CategorieDiscussion::rand_color());


                     $entityManager->persist($categorie);
                     $entityManager->flush();
                     $this->get('session')->getFlashBag()->add('success', 'Categorie ajoutée vous pouvez écrire votre premier post');








                }
             }

             return $this->redirectToRoute('forum');

         }


     /**
     * @Route("/forum/supprimer/{id}", name="delete_discussion")
     */
    public function deleteDiscussion(Request $request, $id)
    {
        $user = $this->getUser();

        $discussion = $this->getDoctrine()
            ->getRepository(Discussion::class)
            ->findOneBy(['id' => $id]);

        if  (($discussion->getAuthorId() == $user) || (in_array('ROLE_ADMIN', $user->getRoles()))){

            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($discussion);
                $entityManager->flush();
                $this->get('session')->getFlashBag()->add('success', 'Discussion supprimé');
            } catch (Exception $e) {
                $this->get('session')->getFlashBag()->add('error', 'Impossible de supprimer la discussion');
            }

            return $this->redirectToRoute('forum');

        }else{

            $this->get('session')->getFlashBag()->add('notice', 'Vous ne pouvez pas supprimer cette discussion');
        }
        
        
    }



    /**
     * @Route("/forum/discussion/{id}", name="show_discussion")
     */
    public function show($id)
    {
        $user = $this->getUser();
        $discussion = $this->getDoctrine()
            ->getRepository(Discussion::class)
            ->findOneBy(array('id' => $id));

            $post = new Post();
            

           $form = $this->createForm(AddPostType::class,  $post, ['action' => $this->generateUrl('add_post', array('idDiscussion' => $discussion->getId()))]);

            return $this->render('forum/show.html.twig', [
                'controller_name' => 'ForumController',
                'discussion' => $discussion,
                'formaddPost' =>   $form->createView(),
                'idDiscussion' => $discussion->getId(),
                'allPosts' => $discussion->getPosts(),
                'userCurrent' => $user
            ]);

    }

     /**
     * @Route("/forum/post/ajouter/{idDiscussion}", name="add_post")
     */
    public function addPost(Request $request, $idDiscussion)
    {
        $discussion = $this->getDoctrine()
        ->getRepository(Discussion::class)
        ->findOneBy(array('id' => $idDiscussion));

        $user = $this->getUser();

        

        $post = new Post();

        $form = $this->createForm(AddPostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($user)){
            // encode the plain password
            

            $post->setAuthor($user);
            $post->setDiscussion($discussion);
            $post->setCreatedAt(new \DateTime('now+1 hour'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Post ajouté');
        } else {

            $this->get('session')->getFlashBag()->add('notice', 'Veuillez vous connecter pour ajouter un post');

            }
        
        }

        return $this->redirectToRoute('show_discussion',['id' => $discussion->getId()]);
    }

     /**
     * @Route("/forum/show/supprimer/{id}", name="delete_post")
     */
    public function deletePost(Request $request, $id)
    {
        $user = $this->getUser();

        $post = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findOneBy(['id' => $id]);

        if  (($post->getAuthor() == $user) || (in_array('ROLE_ADMIN', $user->getRoles()))){

            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($post);
                $entityManager->flush();
                $this->get('session')->getFlashBag()->add('success', 'Post supprimé');
            } catch (Exception $e) {
                $this->get('session')->getFlashBag()->add('error', 'Impossible de supprimer le Post');
            }


        }else{

            $this->get('session')->getFlashBag()->add('notice', 'Vous ne pouvez pas supprimer ce Post');
        }
        
        return $this->redirectToRoute('show_discussion',['id' => $post->getDiscussion()->getId()]);
    }


     /**
     * @Route("/forum/load/", name="load_more")
     * 
     */
    public function LoadMore()
    {
        global $limitDiscussion;

        $limitDiscussion = $limitDiscussion + 4;
        return  $this->redirectToRoute('forum', array(
            'limitDiscussion' => $limitDiscussion,
   
           ));




    }






}





    
