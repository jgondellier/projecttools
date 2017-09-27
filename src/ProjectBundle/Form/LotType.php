<?php

namespace ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class LotType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateCreation',DateTimeType::class, array(
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy H:mm',
                'read_only' => true
            ))
            ->add('etat', 'choice', array('choices' => array('En cours' => 'En cours', 'En attente' => 'En attente', 'Livre' => 'Livré'), 'required' => true, 'label' => 'Etat', 'empty_data' => 'En cours'))
            ->add('version')
            ->add('description', CKEditorType::class, array(
                'config' => array(
                    'uiColor' => '#eeeeee'
                )))
            ->add('recette', CheckboxType::class)
            ->add('dateRecette',DateTimeType::class, array(
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy H:mm',
                'read_only' => true
            ))
            ->add('preprod', CheckboxType::class)
            ->add('datePreprod',DateTimeType::class, array(
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy H:mm',
                'read_only' => true
            ))
            ->add('prod', CheckboxType::class)
            ->add('dateProd',DateTimeType::class, array(
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy H:mm',
                'read_only' => true
            ))
            ->add('project');

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ProjectBundle\Entity\Lot'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'projectbundle_lot';
    }


}
