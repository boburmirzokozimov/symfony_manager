<?php

namespace App\ReadModel\Work\Projects\Calendar\Query;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $year = range(date('Y') - 5, date('Y') + 5);
        $month = range(1, 12);

        $builder
            ->add('year', ChoiceType::class, [
                'choices' => array_combine($year, $year),
                'attr' => ['onchange' => 'this.form.submit()'],
            ])
            ->add('month', ChoiceType::class, [
                'choices' => array_combine($month, $month),
                'attr' => ['onchange' => 'this.form.submit()'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Query::class,
            'csrf_protection' => false,
            'method' => 'GET',
        ]);
    }
}