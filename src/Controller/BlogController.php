<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;



use App\Entity\Article;
use App\Repository\ArticleRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('blog/index.html.twig', [
            'title' => "Bienvenue dans ce blog"
        ]);
    }

    /**
     * @Route("/articles", name="articles")
     */
    public function getArticles(ArticleRepository $repositotyArticles) { //systeme d'injestion de dependance
        //$repositotyArticles = $this->getDoctrine()->getRepository(Article::class);

        $articles = $repositotyArticles->findAll();

        return $this->render('blog/articles.html.twig', [
            'title' => "Voici la listes des articles !",
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/article/create", name = "article_create")
     */

    public function article_create(Request $request, EntityManagerInterface $manager){ //ici bien penser a utiliser EntityManagerInterface que ObjectManager
        $article = new Article();

        $form = $this->createFormBuilder($article)
                    ->add('title', TextType::class)
                    ->add('content', TextareaType::class)
                    ->add('image', TextType::class)
                    ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $article->setCreatedAt(new DateTime());

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('article_details', ['id'=> $article->getId() ]);

        }
                

        return $this->render('blog/create.html.twig', [
            'articleForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/article/{id}", name="article_details")
     */
    public function showArticle(Article $article){ // params converteur
        return $this->render('blog/article_show.html.twig', [
            'article' => $article
        ]);
    }
    
}
