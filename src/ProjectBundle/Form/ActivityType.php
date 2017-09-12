<?php

namespace ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ActivityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cadreContractuel', 'choice', array('choices' => array('MCO' => 'MCO', 'Recette' => 'Recette', 'VSR' => 'VSR', 'Garantie' => 'Garantie'), 'required' => true, 'label' => 'Cadre contractuel', 'empty_value' => ' ', 'data' => 'MCO'))
            ->add('libelle')
            ->add('etat', 'choice', array('choices' => array('En cours' => 'En cours', 'En attente' => 'En attente', 'Terminé' => 'Terminé'), 'required' => true, 'label' => 'Etat', 'data' => 'En cours'))
            ->add('project')
            ->add('dateCreation',DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'read_only' => true
            ))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ProjectBundle\Entity\Activity'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'projectbundle_activity';
    }


}
