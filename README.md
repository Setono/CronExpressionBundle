# Cron Expression Bundle
Symfony bundle that integrates [dragonmantank/cron-expression](https://github.com/dragonmantank/cron-expression)

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-code-quality]][link-code-quality]

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

# Step 3: Add to form type
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

# Step 4: Add to entity
```php
<?php
// src/Entity/Task.php

namespace App\Entity;

use Cron\CronExpression;
use Doctrine\ORM\Mapping as ORM;

class Task
{
    // ...
    
    /**
     * @var CronExpression
     * 
     * @ORM\Column(type="cron_expression") 
     */
    protected $schedule;
    
    // ...
}
```

[ico-version]: https://img.shields.io/packagist/v/setono/cron-expression-bundle.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/Setono/CronExpressionBundle/master.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Setono/CronExpressionBundle.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/setono/cron-expression
[link-travis]: https://travis-ci.org/Setono/CronExpressionBundle
[link-code-quality]: https://scrutinizer-ci.com/g/Setono/CronExpressionBundle