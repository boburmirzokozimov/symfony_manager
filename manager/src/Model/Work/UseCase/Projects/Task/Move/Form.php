<?php

namespace App\Model\Work\UseCase\Projects\Task\Move;

use App\ReadModel\Work\Projects\Project\ProjectFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
	public function __construct(private ProjectFetcher $fetcher)
	{
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('project', ChoiceType::class, [
				'choices' => array_flip($this->fetcher->allList())
			])
			->add('withChildren', CheckboxType::class, ['required' => false]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Command::class
		]);
	}
}