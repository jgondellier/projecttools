<?php

namespace ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idBnp','text')
            ->add('idJtrac','text')
            ->add('nom','text', array('required' => true))
            ->add('prenom','text', array('required' => true))
            ->add('mail','text')
            ->add('description','textarea')
            ->add('project', 'entity', array('property' => 'name', 'class' => 'ProjectBundle\Entity\Project'));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ProjectBundle\Entity\Contact'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'projectbundle_contact';
    }


}
