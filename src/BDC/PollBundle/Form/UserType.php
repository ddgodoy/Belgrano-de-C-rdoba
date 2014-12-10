<?php

namespace BDC\PollBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;

class UserType extends AbstractType
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
        $array_type_user = $this->doctrine->getRepository('BDCPollBundle:Associate')->getArrayForSelect();
        
        $builder
            ->add('dni')
            ->add('name')
            ->add('last_name')
            ->add('email', 'email')
            ->add('gender', 'choice', ['choices' =>['m'=>'Masculino', 'f'=>'Femenino']])        
            ->add('role', 'choice', ['choices' =>['admin'=>'Administrador', 'associate'=>'Socio']])    
            ->add('associate_id', 'choice',['choices' => $array_type_user, 'required'=> true, 'empty_value' => '-- Seleccionar --','empty_data' => null, 'label'  => 'tipo de socio'])
                    
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BDC\PollBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bdc_pollbundle_user';
    }
}
