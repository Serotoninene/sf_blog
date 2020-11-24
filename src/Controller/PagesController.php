<?php

    namespace App\Controller;



    use App\Repository\ArticleRepository;
    use App\Repository\CategoryRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Routing\Annotation\Route;

    class PagesController extends AbstractController{

        /**
         * @Route("/articles", name="list_articles")
         */
        public function listArticles(ArticleRepository $articleRepository){

            $articles = $articleRepository->findAll();

            return $this->render('articles.html.twig',[
            'articles' => $articles
            ]);
        }

        /**
         * @Route("/categories", name="list_categories")
         */
        public function listCategories(CategoryRepository $categoryRepository){

            $categories = $categoryRepository->findAll();

            return $this->render('categories.html.twig',[
                'categories' => $categories
            ]);

        }

    }

?>