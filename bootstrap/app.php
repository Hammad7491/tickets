<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Spatie\Permission\Middleware\RoleMiddleware as SpatieRoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware as SpatiePermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware as SpatieRoleOrPermissionMiddleware;

use App\Http\Middleware\EnsureUserIsNotBlocked;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Run on EVERY web request: immediately log out blocked users
        $middleware->web(append: [
            EnsureUserIsNotBlocked::class,
        ]);

        // Route middleware aliases
        $middleware->alias([
            'role'               => SpatieRoleMiddleware::class,
            'permission'         => SpatiePermissionMiddleware::class,
            'role_or_permission' => SpatieRoleOrPermissionMiddleware::class,
            'not_blocked'        => EnsureUserIsNotBlocked::class, // optional use in route groups
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
