<?php

namespace Azuriom\Plugin\ApiExtender\Providers;

use Azuriom\Extensions\Plugin\BasePluginServiceProvider;
use Azuriom\Models\Permission;
use Azuriom\Games\Game;

class ApiExtenderServiceProvider extends BasePluginServiceProvider
{
    /**
     * The plugin's global HTTP middleware stack.
     */
    protected array $middleware = [
        // \Azuriom\Plugin\ApiExtender\Middleware\ExampleMiddleware::class,
    ];

    /**
     * The plugin's route middleware groups.
     */
    protected array $middlewareGroups = [];

    /**
     * The plugin's route middleware.
     */
    protected array $routeMiddleware = [
        'api.key' => \Azuriom\Plugin\ApiExtender\Middleware\VerifyApiKey::class,
    ];

    /**
     * The policy mappings for this plugin.
     *
     * @var array<string, string>
     */
    protected array $policies = [
        // User::class => UserPolicy::class,
    ];

    /**
     * Register any plugin services.
     */
    public function register(): void
    {
        $this->registerMiddleware();

        //
    }

    /**
     * Bootstrap any plugin services.
     */
    public function boot(): void
    {
        // $this->registerPolicies();

        $this->loadViews();

        $this->loadTranslations();

        $this->loadMigrations();

        $this->registerRouteDescriptions();

        $this->registerAdminNavigation();

        $this->registerUserNavigation();

        Permission::registerPermissions([
            'apiextender.keys' => 'apiextender::admin.permissions.keys',
            'apiextender.cron' => 'apiextender::admin.permissions.cron',
        ]);
    }

    /**
     * Returns the routes that should be able to be added to the navbar.
     *
     * @return array<string, string>
     */
    protected function routeDescriptions(): array
    {
        return [
            //
        ];
    }

    /**
     * Return the admin navigations routes to register in the dashboard.
     *
     * @return array<string, array<string, string>>
     */
    protected function adminNavigation(): array
    {
        $items = [
            'apiextender.admin.index' => [
                'name' => trans('apiextender::admin.title'),
            ],                    
            'apiextender.admin.api-keys.index' => [
                'name' => trans('apiextender::admin.keys'),
            ],
            'apiextender.admin.cron' => [
                'name' => trans('apiextender::admin.cron.title'),
            ],
        ];

        if (game()->id() === 'mc-offline' || game()->id() === 'mc-online') {  
            $items['apiextender.admin.images'] = [
                'name' => trans('apiextender::admin.images'),
            ];
        }

        return [
            'apiextender' => [
                'name' => 'API Extender',
                'type' => 'dropdown',
                'icon' => 'bi bi-gear',
                'route' => 'apiextender.admin.*',
                'items' => $items
            ]
        ];
    }

    /**
     * Return the user navigations routes to register in the user menu.
     *
     * @return array<string, array<string, string>>
     */
    protected function userNavigation(): array
    {
        return [
            //
        ];
    }
}
