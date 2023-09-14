<?php

namespace App\Model\Work\UseCase\Projects\Task\Plan\Set;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('plan', DateType::class, ['required' => true, 'widget' => 'single_text', 'input' => 'datetime_immutable']);

	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Command::class
		]);
	}
}