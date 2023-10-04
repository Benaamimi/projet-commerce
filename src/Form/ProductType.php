<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => [
                    'placeholder' => 'Tapez le nom du prduit'
                ],
                'required' => false, //? annule les messages d'erreur par default du navigateur, pour les champ vide ne pas oublier de faire le ? dans le setName dans l'Entity de la classe produit
                // 'constraints' => new NotBlank(['message' => "Validation du formulaire : le nom du produit ne  peut pas être vide !"])
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Description courte',
                'attr' => [
                    'placeholder' => 'Tapez une courte description pour le visiteur'
                ],
                'required' => false
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix du produit',
                'attr' => [
                    'placeholder' => 'Tapez le prix du produit en euro',
                ],
                'divisor' => 100,
                'required' => false,
                // 'constraints' => new NotBlank(['message' => "Le prix est obligatoire"])

            ])
            ->add('mainPicture', UrlType::class, [
                'label' => 'Image du produit',
                'attr' => ['placeholder' => 'Url de l\'image'],
                'required' => false
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégories',
                'placeholder' => '-- Choisir une catégorie --',
                'class' => Category::class,
                'choice_label' => function (Category $category) {
                    return strtoupper($category->getName());
                },

            ]);



        //permet de transforment les valeur avant et aprés l'evenement
        //  $builder->get('price')->addModelTransformer(new CentimesTransformer);        

    }



    //cette methode d'evenement post_submit aprés l'evenement(la submission) permet de multiplier le prix 
    // $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event){
    //     $product = $event->getData();

    //     if($product->getPrice() !== null){
    //         $product->setPrice($product->getPrice() * 100);
    //     }
    // });


    //?cette methode d'evenement pre_set_data avant l'evenement permet d'afficher les champs du formulaire ou on veut sur le même builder
    //! $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

    //! if($product->getId() === null){

    //!    $form->add('category', EntityType::class, [
    // !        'label' => 'Catégories',
    //  !       'placeholder' => '-- Choisir une catégorie --',
    //   !      'class' => Category::class,
    //    !     'choice_label' => function(Category $category){
    //     !        return strtoupper($category->getName());
    //    !     }
    //     ! ]);
    //! }


    //     $form = $event->getForm();

    //     /** @var Product */
    //     $product = $event->getData();

    //     if($product->getPrice() !== null){
    //         $product->setPrice($product->getPrice() / 100);
    //     }


    // });



    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
