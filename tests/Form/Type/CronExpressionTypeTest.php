<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Tests\Form\Type;

use Cron\CronExpression;
use Setono\CronExpressionBundle\Form\Type\CronExpressionType;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Test\TypeTestCase;

final class CronExpressionTypeTest extends TypeTestCase
{
    public function testSubmitWithAllSet(): void
    {
        $this->_submit([
            'minutes' => ['0'],
            'hours' => ['12'],
            'days' => ['1'],
            'months' => ['6'],
            'weekdays' => ['3'],
        ], '0 12 1 6 3');
    }

    public function testSubmitMultipleMinutes(): void
    {
        $this->_submit([
            'minutes' => ['0', '13'],
            'hours' => ['12'],
            'days' => ['1'],
            'months' => ['6'],
            'weekdays' => ['3'],
        ], '0,13 12 1 6 3');
    }

    public function testSubmitMinutesOnly(): void
    {
        $this->_submit([
            'minutes' => ['0'],
        ], '0 * * * *');
    }

    public function testSubmitEmpty(): void
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

    public function testCreateWithFaultyData(): void
    {
        $data = new stdClass();

        $this->expectException(TransformationFailedException::class);
        $this->factory->create(CronExpressionType::class, $data);
    }
}
