# Cron Expression Bundle

[![Latest Version][ico-version]][link-packagist]
[![Latest Unstable Version][ico-unstable-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]
[![Code Coverage][ico-code-coverage]][link-code-coverage]

Symfony bundle that integrates [dragonmantank/cron-expression](https://github.com/dragonmantank/cron-expression)

## Installation

### Step 1: Download

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```bash
$ composer require setono/cron-expression-bundle
```

This command requires you to have Composer installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.

### Step 2: Enable the bundle

If you use Symfony Flex it will be enabled automatically. Else you need to add it to the `bundles.php`.

```php
<?php
// config/bundles.php

return [
    // ...
    Setono\CronExpressionBundle\SetonoCronExpressionBundle::class => ['all' => true],
    // ...
];
```

## Usage

### Add to form type
```php
<?php
// src/Form/TaskType.php

namespace App\Form;

use Setono\CronExpressionBundle\Form\Type\CronExpressionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('task')
            ->add('schedule', CronExpressionType::class)
            ->add('save', SubmitType::class)
        ;
    }
}
```

### Add to entity

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Cron\CronExpression;
use Doctrine\ORM\Mapping as ORM;
use Setono\CronExpressionBundle\Doctrine\DBAL\Types\CronExpressionType;

#[ORM\Entity]
class Task
{
    #[ORM\Column(type: CronExpressionType::CRON_EXPRESSION_TYPE)]
    private CronExpression $schedule;
}
```

[ico-version]: https://poser.pugx.org/setono/cron-expression-bundle/v/stable
[ico-unstable-version]: https://poser.pugx.org/setono/cron-expression-bundle/v/unstable
[ico-license]: https://poser.pugx.org/setono/cron-expression-bundle/license
[ico-github-actions]: https://github.com/Setono/CronExpressionBundle/workflows/build/badge.svg
[ico-code-coverage]: https://codecov.io/gh/Setono/CronExpressionBundle/branch/master/graph/badge.svg

[link-packagist]: https://packagist.org/packages/setono/cron-expression-bundle
[link-github-actions]: https://github.com/Setono/CronExpressionBundle/actions
[link-code-coverage]: https://codecov.io/gh/Setono/CronExpressionBundle
