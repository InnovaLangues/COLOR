<?php

namespace Innova\ForumMultimodalBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Range;

class TinymceForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $builder
            ->add('Editeur', 'textarea', array(
                'attr' => array(
                    'class' => 'tinymce',
                    'data-theme' => 'advanced' // simple, medium, bbcode, advanced
        )
    ))
            ->add('token', 'hidden', array(
            "mapped" => false,
        ));
    }
    public function getName()
    {
        return 'innova_forummultimodalbundle_TinymceForm';
    }
}