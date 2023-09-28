<?php

namespace App\Form;

use App\Entity\BookCategoryEntity;
use App\Entity\BookEntity;
use App\Repository\BookCategoryEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class BookEntityType extends AbstractType
{
    public function __construct(private BookCategoryEntityRepository $categoryRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('name')
            ->add('amountOfPages',IntegerType::class)
            ->add('isbn')
            ->add('status',ChoiceType::class,[
                'choices' => [
                    'MEAP' => 'MEAP',
                    'PUBLISH' => 'PUBLISH']
            ])
            ->add('image',FileType::class,
            ['label' => 'Загрузите Фото книги (Оно не должно превышать 1024кб и быть в формате jpeg или png)',
                'data_class' => null,
                    'required' => false,
                    'mapped' => true,
                    'constraints' =>
                        [new File(['maxSize' => '1024k','mimeTypes' => ['image/jpeg','image/png'],
                            'mimeTypesMessage' => 'Please upload a valid PNG/JPEG image']) ]])
            ->add('categories',EntityType::class,[
                'class' => BookCategoryEntity::class,
                'choice_label' => 'title',
                'by_reference' => false,
                'expanded' => true,
                'multiple' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BookEntity::class,
        ]);
    }
}
