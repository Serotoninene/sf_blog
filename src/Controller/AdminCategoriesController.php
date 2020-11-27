<?php

namespace App\Controller;


use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminCategoriesController extends AbstractController{

    /**
     * @Route("/admin/categories", name="admin_list_categories")
     */
    public function listCategories(CategoryRepository $categoryRepository){

        $categories = $categoryRepository->findAll();

        return $this->render('admin/categoriesAdmin.html.twig',[
            'categories' => $categories
        ]);

    }

    /**
     * @Route ("/admin/categories/show/{id}", name="admin_show_category")
     */
    public function showCategory($id, CategoryRepository $categoryRepository){

        $category = $categoryRepository->find($id);

        return $this->render("admin/categoryAdmin.html.twig",
            [
                'category' => $category
            ]);

    }


    /**
     * @Route("/admin/categories/insert", name ="admin_insert_category")
     */
    public function insertCategory(Request $request, EntityManagerInterface $entityManager){

        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('admin_list_categories');
        }

        $formview = $form-> createView();

        return $this->render('admin/form.html.twig',[
           "formview" => $formview
        ]);

    }


    /**
     * @Route("/admin/categories/update/{id}", name = "admin_update_category")
     */
    public function updateCategory(CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $entityManager, $id){
        $category = $categoryRepository->find($id);

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('admin_list_categories');
        }

        $formview = $form->createView();

        return $this->render('admin/form.html.twig',[
           "formview" => $formview
        ]);
    }

    /**
     * @Route("/admin/categories/delete/{id}", name = "admin_delete_category")
     */
    public function deleteCategory(CategoryRepository $categoryRepository, EntityManagerInterface $entityManager,$id){
        $category = $categoryRepository->find($id);

        if(isset($category)){
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_list_categories');
    }

}