<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Form\TypeGuesser;

use Cron\CronExpression;
use Setono\CronExpressionBundle\Form\Type\CronExpressionType;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;
use Symfony\Component\Form\Guess\ValueGuess;
use Symfony\Component\PropertyInfo\Extractor\ConstructorExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type as LegacyType;
use Symfony\Component\TypeInfo\Type;

final class CronExpressionTypeGuesser implements FormTypeGuesserInterface
{
    private PropertyTypeExtractorInterface $extractor;

    public function __construct(?PropertyTypeExtractorInterface $extractor = null)
    {
        $this->extractor = $extractor ?? $this->createExtractor();
    }

    /**
     * @param string $class
     * @param string $property
     */
    #[\Override]
    public function guessType($class, $property): ?TypeGuess
    {
        if (!class_exists($class)) {
            return null;
        }

        if ($this->isCronExpression($class, $property)) {
            return new TypeGuess(CronExpressionType::class, [], Guess::VERY_HIGH_CONFIDENCE);
        }

        return null;
    }

    /**
     * @param string $class
     * @param string $property
     */
    #[\Override]
    public function guessRequired($class, $property): ?ValueGuess
    {
        return null;
    }

    /**
     * @param string $class
     * @param string $property
     */
    #[\Override]
    public function guessMaxLength($class, $property): ?ValueGuess
    {
        return null;
    }

    /**
     * @param string $class
     * @param string $property
     */
    #[\Override]
    public function guessPattern($class, $property): ?ValueGuess
    {
        return null;
    }

    private function createExtractor(): PropertyTypeExtractorInterface
    {
        $docExtractor = new PhpDocExtractor();
        $reflectionExtractor = new ReflectionExtractor();

        return new PropertyInfoExtractor([], [
            $docExtractor,
            $reflectionExtractor,
            new ConstructorExtractor([
                $docExtractor,
                $reflectionExtractor,
            ])
        ], [], [], [$reflectionExtractor]);
    }

    /**
     * @psalm-suppress all
     */
    private function isCronExpression(string $class, string $property): bool
    {
        if (class_exists(Type::class) && method_exists($this->extractor, 'getType')) {
            $type = $this->extractor->getType($class, $property);
            if (null === $type) {
                return false;
            }
            if ($type->isIdentifiedBy(CronExpression::class)) {
                return true;
            }
        } else {
            $types = $this->extractor->getTypes($class, $property);
            if (null === $types) {
                return false;
            }
            foreach ($types as $lType) {
                if (LegacyType::BUILTIN_TYPE_OBJECT === $lType->getBuiltinType() &&
                    CronExpression::class === $lType->getClassName()) {
                    return true;
                }
            }
        }

        return false;
    }
}
