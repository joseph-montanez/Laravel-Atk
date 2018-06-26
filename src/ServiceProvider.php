<?php

namespace Atk\Laravel;


use Atk\Laravel\Data\Persistence_SQL;
use Atk\Laravel\Ui\App;
use Illuminate\Support\Facades\DB;

/**
 * Class ServiceProvider
 *
 * @category ServiceProvider
 * @package  Atk\Laravel\Ui
 * @author   Joseph Montanez <sutabi@gmail.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/joseph-montanez/atk-laravel
 */
class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap method
     *
     * @return @void
     */
    public function boot()
    {

    }

    /**
     * Register agile.ui bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            App::class,
            function ($app, $defaults = []) {
                return new App($defaults);
            }
        );
        $this->app->singleton(
            Persistence_SQL::class,
            function ($app) {
                /**
                 * Database Manager
                 *
                 * @var \Illuminate\Database\DatabaseManager $db
                 */
                $db = DB::getFacadeRoot();

                return new Persistence_SQL($db);
            }
        );
    }

}