<?php

namespace Innova\ForumMultimodalBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Range;

/**
 * TinymceForm Form
 * @category   Form
 * @package    Innova
 * @author Mahmoud Charfeddine <[charfeddine.mahmoud@gmail.com]>
 * @copyright  2014 Mahmoud Charfeddine.
 * @version    0.1
 */

class TinymceForm extends AbstractType
{
    /**
     * [buildForm description]
     * @param  FormBuilderInterface $builder
     * @param  array                $options
     * @return Response
     */
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