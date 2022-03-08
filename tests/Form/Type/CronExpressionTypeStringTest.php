<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Tests\Form\Type;

use Cron\CronExpression;
use Setono\CronExpressionBundle\Form\Type\CronExpressionType;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Test\TypeTestCase;

class CronExpressionTypeStringTest extends TypeTestCase
{
    /**
     * @test
     */
    public function submitWithAllSet(): void
    {
        $this->_submit('0 12 1 6 3', '0 12 1 6 3');
    }

    /**
     * @test
     */
    public function submitMultipleMinutes(): void
    {
        $this->_submit('0,13 12 1 6 3', '0,13 12 1 6 3');
    }

    /**
     * @test
     */
    public function submitMinutesOnly(): void
    {
        $this->_submit('0 * * * *', '0 * * * *');
    }

    /**
     * @test
     */
    public function submitEmpty(): void
    {
        $this->_submit(null, '* * * * *');
    }

    private function _submit(?string $formData, string $expected): void
    {
        $form = $this->factory->create(CronExpressionType::class, null, [
            'widget' => 'single_text',
        ]);

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        /** @var CronExpression $cronExpression */
        $cronExpression = $form->getData();

        $this->assertInstanceOf(CronExpression::class, $cronExpression);
        $this->assertSame($expected, $cronExpression->getExpression());
    }

    /**
     * @test
     */
    public function submitFaultyEmpty(): void
    {
        $this->_submitFaultyData([]);
    }

    /**
     * @test
     */
    public function submitFaultyArray(): void
    {
        $this->_submitFaultyData([
            'foo' => 'bar',
        ]);
    }

    /**
     * @param string|array|null $formData
     */
    private function _submitFaultyData($formData): void
    {
        $form = $this->factory->create(CronExpressionType::class, null, [
            'widget' => 'single_text',
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
            'widget' => 'single_text',
        ]);
    }
}
