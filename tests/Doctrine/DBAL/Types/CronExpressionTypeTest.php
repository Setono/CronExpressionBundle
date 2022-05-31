<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Tests\Doctrine\DBAL\Types;

use Cron\CronExpression;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\CronExpressionBundle\Doctrine\DBAL\Types\CronExpressionType;

final class CronExpressionTypeTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function convertToPhpReturnsCronExpression(): void
    {
        $val = $this->getType()->convertToPHPValue('@daily', $this->getPlatform());

        self::assertInstanceOf(CronExpression::class, $val);
    }

    /**
     * @test
     */
    public function convertToDatabaseReturnsString(): void
    {
        $val = $this->getType()->convertToDatabaseValue(CronExpression::factory('0 0 * * *'), $this->getPlatform());

        self::assertSame('0 0 * * *', $val);
    }

    private function getType(): CronExpressionType
    {
        if (!Type::hasType('cron_expression')) {
            Type::addType('cron_expression', CronExpressionType::class);
        }

        /** @var CronExpressionType $type */
        $type = Type::getType('cron_expression');

        return $type;
    }

    private function getPlatform(): AbstractPlatform
    {
        return $this->prophesize(AbstractPlatform::class)->reveal();
    }
}
