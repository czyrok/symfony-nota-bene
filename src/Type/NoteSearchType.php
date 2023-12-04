<?php

namespace App\Type;

use App\Entity\Tag;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;

class NoteSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('authorUsername', TextType::class, [
                'required' => false
            ])
            ->add('title', TextType::class, [
                'required' => false
            ])
            ->add('tags', EntityType::class, [
                'query_builder' => fn(EntityRepository $repository)
                    => $repository->createqueryBuilder('t')->orderBy('t.name', 'ASC'),
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false
            ])
            ->add('onlyLikedByCurrentUser', CheckboxType::class, [
                'required' => false
            ])
            ->add('submit', SubmitType::class);
    }
}