<?php

namespace App\Model\Work\UseCase\Projects\Role\Create;

use App\Model\Work\Entity\Projects\Role\Permission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('name', TextType::class)
			->add('permissions', ChoiceType::class, [
				'choices' => array_combine(Permission::names(), Permission::names()),
				'required' => false,
				'multiple' => true,
				'expanded' => true,
				'translation_domain' => 'work_permissions',
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Command::class
		]);
	}

}