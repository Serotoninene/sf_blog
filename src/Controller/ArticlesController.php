<?php

namespace App\Controller;

use App\Form;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route ("/articles/show/{id}", name="show_article")
     */
    public function showArticle($id, ArticleRepository $articleRepository)
    {

        $article = $articleRepository->find($id);
        return $this->render("article.html.twig",
            [
                'article' => $article
            ]);

    }

    /**
     * Attention aux COLLISIONS, si on ajoute une nouvelle route type "/articles/insert", insert étant au même niveau
     * que la wildcard de la route précédente, php va croire que "insert" est l'id
     *
     * Pour parer à ça, on peut soit forcer id à être un integer via une autre condition "requirements" précisée dans la Route
     * soit rajouter un niveau à la route contenant le wildcard
     */


    /**
     * Pour créer un formulaire, il suffit d'abord d'en créer un gabarit (ie. un template) qui va s'appuyer sur l'entitée
     * que l'on a déjà créé pour faire la table Article de la base de donnée
     * => on utilise la command ine "bin/console make:form" et cela crée automatiquement le gabarit dans un dossier src/Form
     *
     * @Route("/articles/insert" , name = "insert_article")
     */
    public function insertArticle(Request $request, EntityManagerInterface $entityManager){

        // On crée une nouvelle instance dans l'entitée(php)/la table(mysql) Article
        $article = new Article();
        /* On insère le gabarit créé dans une variable, mais elle est encore illisible par du twig, c'est encore du code php trop brut
        On y insère également la variable $article pour lier le form à la variable */
        $form = $this->createForm(Form\ArticleType::class, $article);

        /* avec handleRequest, on appelle tout le contenu POST rentré et enregistré dans la variable $request (entrée en AUTOWIRE en amont) */
        $form->handleRequest($request);

        // Vérification que le form est rempli + valide
        if($form-> isSubmitted() && $form->isValid()){
        //  Si oui on persist et on flush pour ajouter à la BDD
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('notice', 'Article créé :)');

            return $this->redirectToRoute('list_articles');
        }

        // Du coup via la fonction createView(), on la rend décriptable dans du twig
        $formView = $form->createView();

        // Il ne reste plus qu'à passer la variable au fichier twig, pour qu'il puisse la traiter.
        return $this->render("form.html.twig",[
            "formview" => $formView
        ]);


    }

    /**
     *  * Je pose une WILDCARD dans ma route, c'est elle qui va déterminer l'article que l'on va modifier
     * + j'AUTOWIRE ArticleRepository pour faire appel à ma table Article dans ma BDD (et ainsi la modifier - partiellement -)
     * + j'AUTOWIRE EntityManager pour envoyer ces même changements à la BDD
     * + je mets également en PARAMETRE l'id que je récupère du lien pour pouvoir la réutiliser dans le code php :)
     *
     *
     * @Route("/articles/update/{id}", name= "update_article")
     */
    public function updateArticle (ArticleRepository $articleRepository, EntityManagerInterface $entityManager, Request $request, $id){
        //Je vais chercher l'article que je veux modifier dans la BDD
       $article = $articleRepository->find($id);
        //J'insère ses données dans de le form
       $form = $this->createForm(Form\ArticleType::class, $article);

        // avec handleRequest, on appelle tout le contenu POST rentré et enregistré dans la variable $request (entrée en AUTOWIRE en amont)
        $form->handleRequest($request);

       if($form->isSubmitted() && $form->isValid()){
        $entityManager->persist($article);
        $entityManager->flush();

        $this->addFlash('notice', 'Article mis à jour :)');
        return $this->redirectToRoute("list_articles");

       }

        $formView = $form->createView();

        return $this->render('form.html.twig',[
            'formview' => $formView
        ]);



    }

    /**
     * Pour delete un article, le fonctionnement est globalement le même que pour l'update, je trouve d'abord mon article a supp
     * via articleRepository et ma wildcard que je sors du lien cliqué dans la page twig "articles"
     *
     * @Route("/articles/remove/{id}", name="remove_article")
     */
    public function articleDelete(ArticleRepository $articleRepository, EntityManagerInterface $entityManager, $id){

        $article = $articleRepository->find($id);

        /**
         * je dois ouvrir une boucle if pour éviter un message d'erreur si j'essaie d'effacer un article qui existe déjà
         * (donc je vérifie just que l'article n'est pas null)
         */
        if (isset($article)){
            $entityManager->remove($article);

            /**
             * la méthode addFlash d'AbstractController permet de générer des messages temporaires, on précise le type
             * et le contenu du message afin de pouvoir trier plus tard lesdits messages en fonction de leur type.
             *
             * Il faut ensuite les générer dans le code twig de la page vers laquelle on va rediriger ou mieux, dans
             * celui de la base.html.twig
             */
            $this->addFlash("notice", "Article supprimé !");
            /**
             * et pas besoin de la fonction persist() dans ce cas
             */
            $entityManager->flush();
        }

        return $this->redirectToRoute("list_articles");

    }


}