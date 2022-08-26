<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Tests\Doctrine\DBAL\Types;

use Cron\CronExpression;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;
use Setono\CronExpressionBundle\Doctrine\DBAL\Types\CronExpressionType;
use stdClass;

final class CronExpressionTypeTest extends TestCase
{
    /**
     * @test
     */
    public function testTypeName(): void
    {
        self::assertEquals('cron_expression', $this->getType()->getName());
    }

    /**
     * @test
     */
    public function testTypeRequiresHint(): void
    {
        self::assertTrue($this->getType()->requiresSQLCommentHint($this->getPlatform()));
    }

    /**
     * @test
     */
    public function testTypeColumn(): void
    {
        $length = 255;
        $sql = $this->getType()->getSQLDeclaration([
            'length' => $length,
        ], $this->getPlatform());
        self::assertStringContainsString((string) $length, $sql);
    }

    /**
     * @test
     */
    public function convertToPhpNull(): void
    {
        $val = $this->getType()->convertToPHPValue(null, $this->getPlatform());

        self::assertNull($val);
    }

    /**
     * @test
     */
    public function convertEmptyToPhpNull(): void
    {
        $val = $this->getType()->convertToPHPValue('', $this->getPlatform());

        self::assertNull($val);
    }

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
    public function convertFaultyTypeToPhpThrowsException(): void
    {
        self::expectException(ConversionException::class);

        $this->getType()->convertToPHPValue(new stdClass(), $this->getPlatform());
    }

    /**
     * @test
     */
    public function convertFaultyStringToPhpThrowsException(): void
    {
        self::expectException(ConversionException::class);

        $this->getType()->convertToPHPValue('@never', $this->getPlatform());
    }

    /**
     * @test
     */
    public function convertToDatabaseReturnsString(): void
    {
        $val = $this->getType()->convertToDatabaseValue(CronExpression::factory('0 0 * * *'), $this->getPlatform());

        self::assertSame('0 0 * * *', $val);
    }

    /**
     * @test
     */
    public function convertToDatabaseNull(): void
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
