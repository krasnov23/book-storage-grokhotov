<?php

namespace App\Form;

use App\Entity\BookEntity;
use App\Repository\BookCategoryEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class BookEntityType extends AbstractType
{
    public function __construct(private BookCategoryEntityRepository $categoryEntityRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $categories = $this->categoryEntityRepository->findBy([],['level' => Criteria::DESC]);
        $arrayCategories = [];

        foreach ($categories as $category)
        {
            $arrayCategories[$category->getTitle()] = $category;
        }

        $builder
            ->add('name')
            ->add('amountOfPages',IntegerType::class)
            ->add('isbn')
            ->add('image',FileType::class,
            ['label' => 'Загрузите Фото книги (Оно не должно превышать 1024кб и быть в формате jpeg или png)',
                'data_class' => null,
                    'mapped' => true,
                    'required' => true,
                    'constraints' =>
                        [new File(['maxSize' => '1024k','mimeTypes' => ['image/jpeg','image/png'],
                            'mimeTypesMessage' => 'Please upload a valid PNG/JPEG image']) ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BookEntity::class,
        ]);
    }
}
