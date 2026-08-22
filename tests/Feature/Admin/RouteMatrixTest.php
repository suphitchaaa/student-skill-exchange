<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Tests\TestCase;

class RouteMatrixTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_application_route_matrix_methods_uris_middleware_and_access(): void
    {
        $routes = collect(app('router')->getRoutes()->getRoutes());
        $expected = [
            ['GET', '/', ['web']], ['GET', '/login', ['web', 'guest']], ['POST', '/login', ['web', 'guest']], ['GET', '/register', ['web', 'guest']], ['POST', '/register', ['web', 'guest']], ['POST', '/logout', ['web', 'auth']],
            ['GET', '/account/suspended', ['web', 'auth']],
            ['GET', '/dashboard', ['web', 'auth', 'role:student', 'account.active']], ['GET', '/profile', ['web', 'auth', 'role:student', 'account.active']], ['GET', '/profile/edit', ['web', 'auth', 'role:student', 'account.active']], ['PUT', '/profile', ['web', 'auth', 'role:student', 'account.active']], ['POST', '/profile/image', ['web', 'auth', 'role:student', 'account.active']], ['DELETE', '/profile/image', ['web', 'auth', 'role:student', 'account.active']], ['GET', '/my-skills', ['web', 'auth', 'role:student', 'account.active']], ['POST', '/my-skills', ['web', 'auth', 'role:student', 'account.active']], ['PUT', '/my-skills/{userSkill}', ['web', 'auth', 'role:student', 'account.active']], ['DELETE', '/my-skills/{userSkill}', ['web', 'auth', 'role:student', 'account.active']], ['GET', '/students', ['web', 'auth', 'role:student', 'account.active']], ['GET', '/students/{user}', ['web', 'auth', 'role:student', 'account.active']], ['GET', '/students/{user}/exchange-request', ['web', 'auth', 'role:student', 'account.active']], ['POST', '/students/{user}/exchange-request', ['web', 'auth', 'role:student', 'account.active']], ['GET', '/exchange-requests', ['web', 'auth', 'role:student', 'account.active']], ['GET', '/exchange-requests/{exchangeRequest}', ['web', 'auth', 'role:student', 'account.active']], ['PATCH', '/exchange-requests/{exchangeRequest}/accept', ['web', 'auth', 'role:student', 'account.active']], ['PATCH', '/exchange-requests/{exchangeRequest}/reject', ['web', 'auth', 'role:student', 'account.active']], ['PATCH', '/exchange-requests/{exchangeRequest}/cancel', ['web', 'auth', 'role:student', 'account.active']], ['PATCH', '/exchange-requests/{exchangeRequest}/complete', ['web', 'auth', 'role:student', 'account.active']],
            ['GET', '/admin/dashboard', ['web', 'auth', 'role:admin', 'account.active']], ['GET', '/admin/students', ['web', 'auth', 'role:admin', 'account.active']], ['GET', '/admin/students/{user}', ['web', 'auth', 'role:admin', 'account.active']], ['PATCH', '/admin/students/{user}/suspend', ['web', 'auth', 'role:admin', 'account.active']], ['PATCH', '/admin/students/{user}/activate', ['web', 'auth', 'role:admin', 'account.active']], ['GET', '/admin/skills', ['web', 'auth', 'role:admin', 'account.active']], ['GET', '/admin/skills/create', ['web', 'auth', 'role:admin', 'account.active']], ['POST', '/admin/skills', ['web', 'auth', 'role:admin', 'account.active']], ['GET', '/admin/skills/{skill}/edit', ['web', 'auth', 'role:admin', 'account.active']], ['PUT', '/admin/skills/{skill}', ['web', 'auth', 'role:admin', 'account.active']], ['PATCH', '/admin/skills/{skill}', ['web', 'auth', 'role:admin', 'account.active']], ['DELETE', '/admin/skills/{skill}', ['web', 'auth', 'role:admin', 'account.active']], ['GET', '/admin/exchange-requests', ['web', 'auth', 'role:admin', 'account.active']], ['GET', '/admin/exchange-requests/{exchangeRequest}', ['web', 'auth', 'role:admin', 'account.active']],
        ];
        foreach ($expected as [$method, $uri, $middleware]) {
            $expectedUri = ltrim($uri, '/');
            $match = $routes->first(fn (Route $route) => ($uri === '/' ? in_array($route->uri(), ['', '/', '\\'], true) : $route->uri() === $expectedUri) && in_array($method, $route->methods(), true));
            $this->assertNotNull($match, $method.' '.$uri);
            $this->assertSame($middleware, array_values(array_filter($match->gatherMiddleware(), fn ($item) => in_array($item, ['web', 'auth', 'guest', 'role:student', 'role:admin', 'account.active'], true))));
        }
        $this->assertFalse($routes->contains(fn (Route $route) => str_contains($route->uri(), 'restore')));
        $this->assertFalse($routes->contains(fn (Route $route) => str_starts_with((string) $route->getName(), 'admin.exchange-requests.') && array_diff($route->methods(), ['GET', 'HEAD'])));
        $this->assertFalse($routes->contains(fn (Route $route) => str_contains($route->uri(), 'notifications')));
        $this->assertFalse($routes->contains(fn (Route $route) => str_contains($route->uri(), 'email/verify')));
    }
}
