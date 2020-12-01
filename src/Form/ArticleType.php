<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('image', FileType::class, [
                'label' => 'Image',
                'mapped'=> false,
                'required'=>false,
            ])



            /*Pour inclure les catégories dans la création d'un article, il faut mettre un menu déroulant
            dans le form, mais comme à chaque fois que l'on veut modifier le form du CRUD, il faut passer par le dossier Type
            (cf. ajout du bouton submit)

            là on fait bien référence à la propriété "category" de l'entitée Article, et on précise qu'elle (la propriété)
            fait référence à une autre entitée

            /!\ Ne pas oublier de rajouter la ligne use pour le EntityType et le Category::class !! dans les deux cas il faut préciser le chemin
            vers les Entitées Article et Category
            */
            ->add('category', EntityType::class, [

//                On précise le chemin vers la-dite entitée -> Category (d'où la majuscule ici et pas au-dessus - important)
                'class' => Category::class,
//                On ne veut pas renvoyer un objet entier au form, il faut donc choisir une des propriété de l'entitée Category - ici le title est le plus sensé
//                choice_label permet de créer un menu déroulant dans le form avec toutes les catégories
                'choice_label' => 'title'
            ])
            ->add('publicationDate')
            ->add('creationDate')
            ->add('isPublished')
//            RAJOUT D'UN BOUTON SUBMIT (INDIQUÉ PAR SA CLASSE A PRÉCISER CAR ON LE CREE MANUELLEMENT)
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
