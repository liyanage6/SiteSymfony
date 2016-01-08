<?php

namespace NL\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdvertType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // TODO: AMELIORE
//        $advert = $builder->getData();
//        dump($advert);die;
//        if ( null === $advert->getId() ) {
//            $builder->add('date',       'date');
//        }

        $builder
            ->add('date',       'date') // A retier si methode au dessus est fonctionnelle
            ->add('title',      'text')
            ->add('author',     'text')
            ->add('content',    'textarea')
            ->add('image',      new ImageType())
            ->add('categories', 'entity', array(
                'class'     => 'NL\PlatformBundle\Entity\Category',
                'property'  => 'name',
                'multiple'  => true,
                'expanded' => false
            ))
            ->add('save',       'submit')
        ;

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function(FormEvent $event) {
                $advert = $event->getData();

                if (null === $advert) {
                    return;
                }

                if (!$advert->getPublished() || null === $advert->getId()) {
                    $event->getForm()->add('published', 'checkbox', array('required' => false));
                } else {
                    $event->getForm()->remove('published');
                }
            }
        );
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
