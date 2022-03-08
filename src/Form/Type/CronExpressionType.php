<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Form\Type;

use Cron\CronExpression;
use Cron\FieldFactory;
use Setono\CronExpressionBundle\Form\DataTransformer\CronExpressionToPartsTransformer;
use Setono\CronExpressionBundle\Form\DataTransformer\CronExpressionToStringPartsTransformer;
use Setono\CronExpressionBundle\Form\DataTransformer\CronExpressionToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class CronExpressionType extends AbstractType
{
    protected FieldFactory $fieldFactory;

    public function __construct()
    {
        $this->fieldFactory = new FieldFactory();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ('single_text' === $options['widget']) {
            $builder->addViewTransformer(new CronExpressionToStringTransformer());
        } elseif ('text' === $options['widget']) {
            $builder
                ->addViewTransformer(new CronExpressionToStringPartsTransformer())
                ->add('minutes', TextType::class, [
                    'required' => true,
                    'empty_data' => '*',
                    'constraints' => [
                        $this->buildCallback(CronExpression::MINUTE),
                    ],
                ])
                ->add('hours', TextType::class, [
                    'required' => true,
                    'empty_data' => '*',
                    'constraints' => [
                        $this->buildCallback(CronExpression::HOUR),
                    ],
                ])
                ->add('days', TextType::class, [
                    'required' => true,
                    'empty_data' => '*',
                    'constraints' => [
                        $this->buildCallback(CronExpression::DAY),
                    ],
                ])
                ->add('months', TextType::class, [
                    'required' => true,
                    'empty_data' => '*',
                    'constraints' => [
                        $this->buildCallback(CronExpression::MONTH),
                    ],
                ])
                ->add('weekdays', TextType::class, [
                    'required' => true,
                    'empty_data' => '*',
                    'constraints' => [
                        $this->buildCallback(CronExpression::WEEKDAY),
                    ],
                ])
            ;
        } else {
            $builder
                ->addViewTransformer(new CronExpressionToPartsTransformer())
                ->add('minutes', ChoiceType::class, [
                    'choices' => range(0, 59),
                    'multiple' => true,
                    'required' => false,
                ])
                ->add('hours', ChoiceType::class, [
                    'choices' => range(0, 23),
                    'multiple' => true,
                    'required' => false,
                ])
                ->add('days', ChoiceType::class, [
                    'choices' => $this->oneIndexedRange(31),
                    'multiple' => true,
                    'required' => false,
                ])
                ->add('months', ChoiceType::class, [
                    'choices' => $this->oneIndexedRange(12),
                    'multiple' => true,
                    'required' => false,
                ])
                ->add('weekdays', ChoiceType::class, [
                    'choices' => $this->oneIndexedRange(7),
                    'multiple' => true,
                    'required' => false,
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $compound = function (Options $options): bool {
            return 'single_text' !== $options['widget'];
        };

        $resolver->setDefaults([
            'widget' => null,
            'data_class' => null,
            'compound' => $compound,
//            'empty_data' => function (Options $options) {
//                return $options['compound'] ? [] : '';
//            },
        ]);

        $resolver->setAllowedValues('widget', [
            null, // default, don't overwrite options
            'single_text',
            'text',
            'choice',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'setono_cron_expression';
    }

    /**
     * Will create an array where the first key is 1
     * oneIndexedRange(3) will return [1 => 1, 2 => 2, 3 => 3].
     */
    private function oneIndexedRange(int $end, int $start = 0): array
    {
        $arr = range($start, $end);
        unset($arr[0]);

        return $arr;
    }

    protected function buildCallback(int $payload): Callback
    {
        // helper function for Symfony 4.4
        return new Callback([
            'callback' => [$this, 'validateCronField'],
            'payload' => $payload,
        ]);
    }

    public function validateCronField(?string $value, ExecutionContextInterface $context, int $payload): void
    {
        if (null === $value) {
            return;
        }
        if (!$this->fieldFactory->getField($payload)->validate($value)) {
            $context->addViolation('{{value}} is not a valid cron part', ['value' => $value]);
        }
    }
}
