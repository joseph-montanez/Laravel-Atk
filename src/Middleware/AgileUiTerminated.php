<?php

namespace Atk\Laravel\Middleware;

use Closure;

/**
 * Class AgileUiTerminated
 *
 * @category Middleware
 * @package  Atk\Laravel\Middleware
 * @author   Joseph Montanez <sutabi@gmail.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/joseph-montanez/atk-laravel
 */
class AgileUiTerminated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /**
         * The http response
         *
         * @var \Illuminate\Http\Response $response
         */
        $response = $next($request);

        if ($response->exception instanceof \Atk\Laravel\Ui\TerminatedException) {
            $response->setStatusCode(200, 'OK');
            $response->setJson($response->exception->output);
        }

        return $response;
    }
}
