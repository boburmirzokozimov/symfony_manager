<?php

namespace App\Model\Work\UseCase\Projects\Task\Create;

use App\Model\Work\Entity\Projects\Task\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', TextType::class)
			->add('content', TextareaType::class, ['required' => false, 'attr' => ['rows' => 10]])
			->add('parent', IntegerType::class, ['required' => false])
			->add('plan', DateType::class, ['required' => false, 'widget' => 'single_text', 'input' => 'datetime_immutable'])
			->add('type', ChoiceType::class, ['choices' => [
				'None' => Type::NONE,
				'Error' => Type::ERROR,
				'Feature' => Type::FEATURE
			]])
			->add('priority', ChoiceType::class, ['choices' => [
				'Low' => 1,
				'Normal' => 2,
				'High' => 3,
				'Extra' => 4,
			]]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => Command::class
		));
	}
}