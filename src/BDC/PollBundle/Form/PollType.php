<?php

namespace BDC\PollBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;

class PollType extends AbstractType
{
    protected $doctrine;

    public function __construct(EntityManager $doctrine)
    {
       $this->doctrine = $doctrine;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        
        $builder
            ->add('name')
            ->add('status', 'choice', ['choices' =>['active'=>'Activa', 'inactive'=>'Inactiva', 'ended' => 'Finalizada']])
            ->add('id_user', 'hidden');
            
                    
        
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
