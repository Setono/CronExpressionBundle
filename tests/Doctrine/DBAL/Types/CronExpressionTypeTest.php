<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Tests\Doctrine\DBAL\Types;

use Cron\CronExpression;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;
use Setono\CronExpressionBundle\Doctrine\DBAL\Types\CronExpressionType;
use stdClass;

final class CronExpressionTypeTest extends TestCase
{
    public function testTypeName(): void
    {
        self::assertEquals('cron_expression', $this->getType()->getName());
    }

    public function testTypeRequiresHint(): void
    {
        self::assertTrue($this->getType()->requiresSQLCommentHint($this->getPlatform()));
    }

    public function testTypeColumn(): void
    {
        $length = 255;
        $sql = $this->getType()->getSQLDeclaration([
            'length' => $length,
        ], $this->getPlatform());
        self::assertStringContainsString((string) $length, $sql);
    }

    public function testConvertToPhpNull(): void
    {
        $val = $this->getType()->convertToPHPValue(null, $this->getPlatform());

        self::assertNull($val);
    }

    public function testConvertEmptyToPhpNull(): void
    {
        $val = $this->getType()->convertToPHPValue('', $this->getPlatform());

        self::assertNull($val);
    }

    public function testConvertToPhpReturnsCronExpression(): void
    {
        $val = $this->getType()->convertToPHPValue('@daily', $this->getPlatform());

        self::assertInstanceOf(CronExpression::class, $val);
    }

    public function testConvertFaultyTypeToPhpThrowsException(): void
    {
        self::expectException(ConversionException::class);
        if (class_exists(InvalidType::class)) {
            /**
             * @psalm-suppress InvalidArgument
             */
            self::expectException(InvalidType::class);
        }

        $this->getType()->convertToPHPValue(new stdClass(), $this->getPlatform());
    }

    public function testConvertFaultyStringToPhpThrowsException(): void
    {
        self::expectException(ConversionException::class);
        if (class_exists(ValueNotConvertible::class)) {
            /**
             * @psalm-suppress InvalidArgument
             */
            self::expectException(ValueNotConvertible::class);
        }

        $this->getType()->convertToPHPValue('@never', $this->getPlatform());
    }

    public function testConvertToDatabaseReturnsString(): void
    {
        $val = $this->getType()->convertToDatabaseValue(CronExpression::factory('0 0 * * *'), $this->getPlatform());

        self::assertSame('0 0 * * *', $val);
    }

    public function testConvertToDatabaseNull(): void
    {
        $val = $this->getType()->convertToDatabaseValue(null, $this->getPlatform());
        self::assertNull($val);
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
        $mock = $this->createMock(AbstractPlatform::class);
        $mock->method('getStringTypeDeclarationSQL')
            ->withAnyParameters()
            ->willReturnCallback(function (array $column) {
                /** @var int $length */
                $length = $column['length'];
                /** @var bool $fixed */
                $fixed = $column['fixed'] ?? false;

                return $fixed ? ($length > 0 ? 'CHAR(' . $length . ')' : 'CHAR(254)')
                : ($length > 0 ? 'VARCHAR(' . $length . ')' : 'VARCHAR(255)');
            })
        ;

        return $mock;
    }
}
