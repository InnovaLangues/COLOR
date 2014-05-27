<?php

namespace Innova\ForumMultimodalBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Range;

/**
 * FileForm Form
 * @category   Form
 * @package    Innova
 * @author Mahmoud Charfeddine <[charfeddine.mahmoud@gmail.com]>
 * @copyright  2014 Mahmoud Charfeddine.
 * @version    0.1
 */

class FileForm extends AbstractType
{
	/**
	 * [buildForm description]
	 * @param  FormBuilderInterface $builder
	 * @param  array                $options
	 * @return Response
	 */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $builder->add('attachment', 'file');
    }
    /**
     * [getName description]
     * @return Response
     */
    public function getName()
    {
        return 'innova_forummultimodalbundle_File';
    }
}