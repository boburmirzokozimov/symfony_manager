<?php

namespace App\Model\User\UseCase\Role;

use App\Model\User\Entity\User\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('role', ChoiceType::class, ['label' => 'Role', 'choices' => [
				'User' => Role::USER,
				'Admin' => Role::ADMIN
			]]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		return $resolver->setDefaults([
			'data_class' => Command::class
		]);
	}
}