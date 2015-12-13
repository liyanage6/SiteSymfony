<?php

namespace NL\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdvertType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date',       'date')
            ->add('title',      'text')
            ->add('author',     'text')
            ->add('content',    'textarea')
            ->add('published',  'checkbox', array('required' => false))
            ->add('image',      new ImageType())
            ->add('categories', 'entity', array(
                'class'     => 'NL\PlatformBundle\Entity\Category',
                'property'  => 'name',
                'multiple'  => false,
                'expanded' => false
            ))
            ->add('save',       'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NL\PlatformBundle\Entity\Advert'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'nl_platformbundle_advert';
    }
}
