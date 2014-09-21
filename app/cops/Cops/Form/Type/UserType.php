<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Form\Type;

use Symfony\Component\Form\AbstractType;
use Cops\Model\User;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

/**
 * User create / edit formType
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username',
                'text',
                array(
                    'constraints' => array(
                        new Constraints\NotBlank(),
                        new Constraints\Length(array('max' => 32))
                    )
                )
            )
            ->add(
                'password',
                'repeated',
                array(
                    'type'            => 'password',
                    'first_name'      => 'password',
                    'second_name'     => 'password_confirm',
                    'invalid_message' => 'Passwords do not match',
                    'first_options'   => array('label' => 'Password'),
                    'second_options'  => array('label' => 'Password confirmation'),
                )
            )
            ->add('role',
                'choice', array(
                    'choices' => $options['data']->getAllRoles(),
                    'expanded' => true,
                )
            )
            ->add('save', 'submit');

        // Password change is not required for existing users
        if ($options['data']->getId()) {
            $builder->get('password')->setRequired(false);
        }

        $builder->getForm();
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'translation_domain' => 'admin'
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'user';
    }
}
