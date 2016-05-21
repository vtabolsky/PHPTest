<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
		$builder->add('price', NumberType::class);
		$builder->add('description', TextareaType::class, array ('required'=> false));
		$builder->add('file', FileType::class, array ('required'=> false));
		$builder->add('tags', TextType::class, array ('required'=> false));
		$builder->setMethod('POST');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Apundle\Document\Product',
        ));
    }

    public function getName()
    {
        return 'product';
    }
}