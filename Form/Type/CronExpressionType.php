<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Form\Type;

use Setono\CronExpressionBundle\Form\DataTransformer\CronExpressionToPartsTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class CronExpressionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $days = range(0, 31);
        unset($days[0]);

        $months = range(0, 12);
        unset($months[0]);

        $weekdays = range(0, 7);
        unset($weekdays[0]);

        $builder
            ->addViewTransformer(new CronExpressionToPartsTransformer())
            ->add('minutes', ChoiceType::class, [
                'choices' => range(0, 59),
            ])
            ->add('hours', ChoiceType::class, [
                'choices' => range(0, 23),
            ])
            ->add('days',ChoiceType::class, [
                'choices' => $days,
            ])
            ->add('months',ChoiceType::class, [
                'choices' => $months,
            ])
            ->add('weekdays',ChoiceType::class, [
                'choices' => $weekdays,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'setono_cron_expression';
    }
}
