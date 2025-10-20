<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Tests\Form\Type;

use Cron\CronExpression;
use Setono\CronExpressionBundle\Form\Type\CronExpressionType;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Test\TypeTestCase;

final class CronExpressionTypeStringTest extends TypeTestCase
{
    public function testSubmitWithAllSet(): void
    {
        $this->_submit('0 12 1 6 3', '0 12 1 6 3');
    }

    public function testSubmitMultipleMinutes(): void
    {
        $this->_submit('0,13 12 1 6 3', '0,13 12 1 6 3');
    }

    public function testSubmitMinutesOnly(): void
    {
        $this->_submit('0 * * * *', '0 * * * *');
    }

    public function testSubmitEmpty(): void
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

    public function testSubmitFaultyEmpty(): void
    {
        $this->_submitFaultyData([]);
    }

    public function testSubmitFaultyArray(): void
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

    public function testCreateWithFaultyData(): void
    {
        $data = new stdClass();

        $this->expectException(TransformationFailedException::class);
        $this->factory->create(CronExpressionType::class, $data, [
            'widget' => 'single_text',
        ]);
    }
}
