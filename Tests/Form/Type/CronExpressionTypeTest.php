<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Tests\Form\Type;

use Cron\CronExpression;
use Setono\CronExpressionBundle\Form\Type\CronExpressionType;
use Symfony\Component\Form\Test\TypeTestCase;

class CronExpressionTypeTest extends TypeTestCase
{
    /**
     * @test
     */
    public function submitWithAllSet(): void
    {
        $this->_submit([
            'minutes' => ['0'],
            'hours' => ['12'],
            'days' => ['1'],
            'months' => ['6'],
            'weekdays' => ['3'],
        ], '0 12 1 6 3');
    }

    /**
     * @test
     */
    public function submitMultipleMinutes(): void
    {
        $this->_submit([
            'minutes' => ['0', '13'],
            'hours' => ['12'],
            'days' => ['1'],
            'months' => ['6'],
            'weekdays' => ['3'],
        ], '0,13 12 1 6 3');
    }

    /**
     * @test
     */
    public function submitMinutesOnly(): void
    {
        $this->_submit([
            'minutes' => ['0'],
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
        $form = $this->factory->create(CronExpressionType::class);

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
}
