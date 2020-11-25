<?php

namespace App\Controller;



use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticlesController extends AbstractController
{

    /**
     * @Route("/articles", name="list_articles")
     *
     * On rajoute la classe ArticleRepository directement dans la ligne de la fonction et on la sauvegarde dans une
     * variable --> AUTOWIRE
     */
    public function listArticles(ArticleRepository $articleRepository)
    {

        /**
         * Grace à ArticleRepository je peux faire des requêtes SELECT SQL et retrouver les éléments désirés dans
         *ma BDD, en l'occurence je les cherche tous
         */
        $articles = $articleRepository->findAll();

        return $this->render('articles.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     *
     * id est ci-dessous une WILDCARD, ie c'est une variable, dans ce cas modifiée en fonction du lien sur lequel on a cliqué
     *
     * @Route ("/articles/{id}", name="show_article")
     */
    public function showArticle($id, ArticleRepository $articleRepository)
    {

        $article = $articleRepository->find($id);
        return $this->render("article.html.twig",
            [
                'article' => $article
            ]);

    }

}