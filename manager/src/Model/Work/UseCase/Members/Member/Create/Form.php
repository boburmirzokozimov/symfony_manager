<?php

namespace App\Model\Work\UseCase\Members\Member\Create;

use App\ReadModel\Work\Members\Groups\GroupFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
	public function __construct(private GroupFetcher $fetcher)
	{
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('group', ChoiceType::class, ['choices' => array_flip($this->fetcher->assoc())])
			->add('firstName', TextType::class)
			->add('lastName', TextType::class)
			->add('email', TextType::class);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Command::class
		]);
	}
}