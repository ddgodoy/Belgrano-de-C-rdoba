<?php

namespace BDC\PollBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PollType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('status', 'choice', ['choices' =>['active'=>'Activa', 'inactive'=>'Inactiva', 'ended' => 'Finalizada']])
            ->add('id_user', 'hidden')
            ->add('image_header', 'file', array('label' => 'Imagen cabecera', 'data_class' => null, 'required' => false))
            ->add('image_footer', 'file', array('label' => 'Imagen pie de pagina', 'data_class' => null));

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BDC\PollBundle\Entity\Poll'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bdc_pollbundle_poll';
    }
}
