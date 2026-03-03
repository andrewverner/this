<?php
/**
 * @author Denis Khodakovskii <denis.khodakovskiy@gmail.com>
 */

use App\This\Core\Routing\RouteRegistry;

$lambdas = [
    require_once __DIR__ . '/app.php',
    require_once __DIR__ . '/test.php',
    \This\ORM\Router\MigrationRouter::register(),
];

return function (RouteRegistry $router) use ($lambdas) {
    foreach ($lambdas as $callback) {
        $callback($router);
    }
};
