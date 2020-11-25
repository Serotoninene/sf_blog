<?php

namespace App\Controller;


use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\True_;
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
     * AUTOWIRE EntityManagerInterface
     *
     * @Route ("/articles/insert", name = "insert_static")
     */
    public function insertStaticArticle(EntityManagerInterface $entityManager){

        /**
         * je fais appel à mon entity Article (pas oublier de creer un nouveau use pour elle) et j'en crée une nouvelle
         * instance
         */
        $article = new Article();

        /**
         * Je construis chaque propriétés de l'entitée en utilisant ses setters
         */
        $article->setTitle('Le bon roi Dagobert');
        $article->setContent('a mis sa culotte à l\'envers');
        $article->setImage("https://static.teteamodeler.com/media/cache/thumb_400/le-bon-roi-dagobert-la-chanson-en-video.png");
        $article->setCreationDate(new \DateTime());
        $article->setPublicationDate(new \DateTime());
        $article->setIsPublished(true);

        /**
         * C'est via EntityManager que je fais le transfert vers la BDD, d'abord j'enregistre mes ajouts avec sa méthode
         * persist() et ensuite j'envoie tout avec flush() - fonctionnement similaire à git
         */
        $entityManager->persist($article);
        $entityManager->flush();

        return $this->redirectToRoute("list_articles");

    }


    /**
     * Je pose une WILDCARD dans ma route, c'est elle qui va déterminer l'article que l'on va modifier
     * + j'AUTOWIRE ArticleRepository pour faire appel à ma table Article dans ma BDD (et ainsi la modifier - partiellement -)
     * + j'AUTOWIRE EntityManager pour envoyer ces même changements à la BDD
     * + je mets également en PARAMETRE l'id que je récupère du lien pour pouvoir la réutiliser dans le code php :)
     *
     * @Route ("/articles/update/{id}", name="update_article_static")
     */
    public function articleStaticUpdate(ArticleRepository $articleRepository, EntityManagerInterface $entityManager, $id){

        $article = $articleRepository->find($id);

        $article->setTitle("OMG IL A CLIQUÉ LE CON");
        $entityManager->persist($article);
        $entityManager->flush();

        return $this->redirectToRoute("list_articles");

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
             * et pas besoin de la fonction persist() dans ce cas
             */
            $entityManager->flush();
        }

        return $this->redirectToRoute("list_articles");

    }


}