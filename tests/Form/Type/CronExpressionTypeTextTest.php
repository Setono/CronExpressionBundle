<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Tests\Form\Type;

use Cron\CronExpression;
use Setono\CronExpressionBundle\Form\Type\CronExpressionType;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Test\Traits\ValidatorExtensionTrait;
use Symfony\Component\Form\Test\TypeTestCase;

final class CronExpressionTypeTextTest extends TypeTestCase
{
    use ValidatorExtensionTrait;

    /**
     * @test
     */
    public function submitWithAllSet(): void
    {
        $this->_submit([
            'minutes' => '0',
            'hours' => '12',
            'days' => '1',
            'months' => '6',
            'weekdays' => '3',
        ], '0 12 1 6 3');
    }

    /**
     * @test
     */
    public function submitMultipleMinutes(): void
    {
        $this->_submit([
            'minutes' => '0,13',
            'hours' => '12',
            'days' => '1',
            'months' => '6',
            'weekdays' => '3',
        ], '0,13 12 1 6 3');
    }

    /**
     * @test
     */
    public function submitMinutesOnly(): void
    {
        $this->_submit([
            'minutes' => '0',
        ], '0 * * * *');
    }

    /**
     * @test
     */
    public function submitEmpty(): void
    {
        $this->_submit([], '* * * * *');
    }

    private function _submit(array $formData, string $expected): void
    {
        $form = $this->factory->create(CronExpressionType::class, null, [
            'widget' => 'text',
        ]);

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }

        /** @var CronExpression $cronExpression */
        $cronExpression = $form->getData();

        $this->assertInstanceOf(CronExpression::class, $cronExpression);
        $this->assertSame($expected, $cronExpression->getExpression());
    }

    /**
     * @test
     */
    public function submitFaultyMinutesOnly(): void
    {
        $this->_submitFaultyData([
            'minutes' => '61',
        ]);
    }

    /**
     * @param string|array|null $formData
     */
    private function _submitFaultyData($formData): void
    {
        $form = $this->factory->create(CronExpressionType::class, null, [
            'widget' => 'text',
        ]);

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertFalse($form->isSynchronized());
    }

    /**
     * @test
     */
    public function createWithFaultyData(): void
    {
        $data = new stdClass();

        $this->expectException(TransformationFailedException::class);
        $this->factory->create(CronExpressionType::class, $data, [
            'widget' => 'text',
        ]);
    }

    /**
     * @test
     */
    public function submitChild(): void
    {
        $this->_submitWithChild([
            'minutes' => '0',
        ], '0 * * * *');
    }

    /**
     * @test
     */
    public function submitNull(): void
    {
        $this->_submitWithChild(null, '* * * * *');
    }

    /**
     * @param mixed $formData
     */
    public function _submitWithChild($formData, string $expected): void
    {
        $builder = $this->factory->createBuilder();
        $builder->add('cron', CronExpressionType::class, [
            'widget' => 'text',
        ]);
        $form = $builder->getForm();
        $form->submit(['cron' => $formData]);

        /** @var array $data */
        $data = $form->getData();
        /** @var CronExpression $cronExpression */
        $cronExpression = $data['cron'];

        $this->assertInstanceOf(CronExpression::class, $cronExpression);
        $this->assertSame($expected, $cronExpression->getExpression());
    }
}
