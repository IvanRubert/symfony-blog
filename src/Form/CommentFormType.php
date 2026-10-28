<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class CommentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',  null, ['attr' => ['class'=>'form-control']])
            ->add('email', EmailType::class, ['attr' => ['class'=>'form-control']])
            ->add('text',  null, ['attr' => ['class'=>'form-control']])
            ->add('Send',  SubmitType::class, ['attr' => ['class'=>'pull-left btn btn-lg sr-button btn-info']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
