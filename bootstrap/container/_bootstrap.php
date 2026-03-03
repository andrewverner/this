<?php
/**
 * @author Denis Khodakovskii <denis.khodakovskiy@gmail.com>
 */

use App\This\Infrastructure\Container\Container;
use This\Contracts\ContainerInterface;
use This\ORM\Container\OrmContainer;

$lambdas = [
    require_once __DIR__ . '/handlers.php',
    require_once __DIR__ . '/middlewares.php',
    require_once __DIR__ . '/services.php',
    require_once __DIR__ . '/test.php',
    \This\TemplateEngine\Container\TemplateEngineContainer::register(),
    \This\Session\Container\SessionContainer::register(),
    \This\Security\Container\SecurityContainer::register(),
    ORMContainer::register(),
];

return function () use ($lambdas): ContainerInterface {
    $container = new Container();

    foreach ($lambdas as $callback) {
        $callback($container);
    }

    return $container;
};
