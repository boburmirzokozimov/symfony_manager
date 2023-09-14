<?php

namespace App\Model\User\UseCase\Edit;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('email', EmailType::class, ['label' => 'Email'])
			->add('firstName', TextType::class, ['label' => 'First Name'])
			->add('lastName', TextType::class, ['label' => 'Last Name']);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		return $resolver->setDefaults([
			'data_class' => Command::class
		]);
	}
}