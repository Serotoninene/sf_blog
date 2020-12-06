<?php

namespace App\Controller;

use App\Form;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminArticlesController extends AbstractController
{

    /**
     * @Route("/admin/articles", name="admin_list_articles")
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

        return $this->render('admin/article/articlesAdmin.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     *
     * @Route ("/admin/articles/show/{id}", name="admin_show_article")
     */
    public function showArticle($id, ArticleRepository $articleRepository)
    {

        $article = $articleRepository->find($id);
        return $this->render("admin/article/articleAdmin.html.twig",
            [
                'article' => $article
            ]);

    }


    /**
     * Pour créer un formulaire, il suffit d'abord d'en créer un gabarit (ie. un template) qui va s'appuyer sur l'entitée
     * que l'on a déjà créé pour faire la table Article de la base de donnée
     * => on utilise la command ine "bin/console make:form" et cela crée automatiquement le gabarit dans un dossier src/Form
     *
     * @Route("/admin/articles/insert" , name = "admin_insert_article")
     */
    public function insertArticle(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger){

        // On crée une nouvelle instance dans l'entitée(php)/la table(mysql) Article
        $article = new Article();


        /* On insère le gabarit créé dans une variable, mais elle est encore illisible par du twig, c'est encore du code php trop brut
        On y insère également la variable $article pour lier le form à la variable */
        $form = $this->createForm(Form\ArticleType::class, $article);

        /* avec handleRequest, on appelle tout le contenu POST rentré et enregistré dans la variable $request (entrée en AUTOWIRE en amont) */
        $form->handleRequest($request);

        // Vérification que le form est rempli + valide
        if($form-> isSubmitted() && $form->isValid()){

            // POUR ENVOYER UN FICHIER IMAGE : on récupère le fichier image avec la fonction get (qui fait référence à 'image'
            //le nom Sde la propriété du form builder dans ArticleType
            $imageFile = $form->get('image')->getData();

            if ($imageFile){
                //je récupère le nom du fichier image
                $originalImageName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                // avec la methode slug de la class slugger (ne pas oublier de l'instancier en autowire) on chope le nom
                //en enlevant tous les caractères spéciaux et accents (/"-][ )
                $safeImageName = $slugger->slug($originalImageName);

                // on recrée un nom clean (la ligne ci-dessous ajoute juste l'extension après le nom
                $newImageName = $safeImageName.'-'.uniqid().'.'.$imageFile->guessExtension();


                // Move the file to the directory where images are stored
                try {
                    $imageFile->move(
                        // /!\ HYPER IMPORTANT : modifier le fichier service.yaml pour déterminer le paramètre du fichier
                        // cible, faire attention à l'indentation chez les YAML !!!
                        $this->getParameter('uploads_directory'),
                        $newImageName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $article->setImageFileName($newImageName);
            }


            //  Si oui on persist et on flush pour ajouter à la BDD
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('notice', 'Article créé :)');

            return $this->redirectToRoute('admin_list_articles');
        }

        // Du coup via la fonction createView(), on la rend décriptable dans du twig
        $formView = $form->createView();

        // Il ne reste plus qu'à passer la variable au fichier twig, pour qu'il puisse la traiter.
        return $this->render("admin/article/articleForm.html.twig",[
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
     * @Route("/admin/articles/update/{id}", name= "admin_update_article")
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
            return $this->redirectToRoute("admin_list_articles");

        }

        $formView = $form->createView();

        return $this->render('admin/article/articleForm.html.twig',[
            'formview' => $formView
        ]);

    }

    /**
     * Pour delete un article, le fonctionnement est globalement le même que pour l'update, je trouve d'abord mon article a supp
     * via articleRepository et ma wildcard que je sors du lien cliqué dans la page twig "articles"
     *
     * @Route("/admin/articles/remove/{id}", name="admin_remove_article")
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

        return $this->redirectToRoute("admin_list_articles");

    }
}