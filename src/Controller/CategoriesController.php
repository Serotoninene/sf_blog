<?php

namespace App\Controller;



use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class CategoriesController extends AbstractController{

/**
         * @Route("/categories", name="list_categories")
         */
        public function listCategories(CategoryRepository $categoryRepository){

            $categories = $categoryRepository->findAll();

            return $this->render('front/categories.html.twig',[
                'categories' => $categories
            ]);

        }

        /**
        * @Route ("/categories/{id}", name="show_category")
        */
        public function showCategory($id, CategoryRepository $categoryRepository){

            $category = $categoryRepository->find($id);
            return $this->render("front/category.html.twig",
                [
                    'category' => $category
                ]);

        }


    }

?>