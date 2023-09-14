<?php

namespace App\Model\Work\UseCase\Projects\Project\Membership\Edit;

use App\ReadModel\Work\Projects\Project\Departments\DepartmentFetcher;
use App\ReadModel\Work\Projects\Role\RoleFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
	public function __construct(private DepartmentFetcher $departmentFetcher,
								private RoleFetcher       $roleFetcher)
	{
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('departments', ChoiceType::class, [
				'choices' => array_flip($this->departmentFetcher->listOfProjects($options['project'])),
				'expanded' => true,
				'multiple' => true,
				'translation_domain' => 'work_permissions'
			])
			->add('roles', ChoiceType::class, [
				'choices' => array_flip($this->roleFetcher->allList()),
				'expanded' => true,
				'multiple' => true,
				'translation_domain' => 'work_permissions'
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Command::class,
		])
			->setRequired(['project']);
	}

}