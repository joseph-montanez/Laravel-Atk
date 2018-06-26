<?php

namespace Atk\Laravel\Ui;

/**
 * Class App
 *
 * @category Ui
 * @package  Atk\Laravel\Ui
 * @author   Joseph Montanez <sutabi@gmail.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/joseph-montanez/atk-laravel
 */
class App extends \atk4\ui\App
{
    /**
     * Build a URL that application can use for loading HTML data.
     *
     * @param array|string $page           URL as string or array with page name as first element and other GET arguments
     * @param bool         $needRequestUri Simply return $_SERVER['REQUEST_URI'] if needed
     * @param array        $extra_args     Additional URL arguments
     *
     * @return string
     */
    public function url($page = [], $needRequestUri = false, $extra_args = [])
    {
        if ($needRequestUri) {
            return $_SERVER['REQUEST_URI'];
        }

        $sticky = $this->sticky_get_arguments;
        $result = $extra_args;

        if ($this->page === null) {
            $uri = $this->getRequestURI();

            if (substr($uri, -1, 1) == '/') {
                $this->page = 'index';
            } else {
                $this->page = basename($uri, '.php');
            }
        }

        // if page passed as string, then simply use it
        if (is_string($page)) {
            return $page;
        }

        // use current page by default
        if (!isset($page[0])) {
            $page[0] = $this->page;
        }

        //add sticky arguments
        if (is_array($sticky) && !empty($sticky)) {
            foreach ($sticky as $key => $val) {
                if ($val === true) {
                    if (isset($_GET[$key])) {
                        $val = $_GET[$key];
                    } else {
                        continue;
                    }
                }
                if (!isset($result[$key])) {
                    $result[$key] = $val;
                }
            }
        }

        // add arguments
        foreach ($page as $arg => $val) {
            if ($arg === 0) {
                continue;
            }

            if ($val === null || $val === false) {
                unset($result[$arg]);
            } else {
                $result[$arg] = $val;
            }
        }

        // put URL together
        $args = http_build_query($result);
        $url = ($page[0] ? $page[0] : '').($args ? '?'.$args : '');

        return $url;
    }


    /**
     * Runs app and echo rendered template.
     *
     * @return string
     * @throws \App\Ui\TerminatedException
     * @throws \atk4\core\Exception
     * @throws \atk4\ui\Exception
     */
    public function run()
    {
        try {
            $this->run_called = true;
            $this->hook('beforeRender');
            $this->is_rendering = true;

            // if no App layout set
            if (!isset($this->html)) {
                throw new Exception(['App layout should be set.']);
            }

            $this->html->template->set('title', $this->title);
            $this->html->renderAll();

            $this->html->template->appendHTML('HEAD', $this->html->getJS());
            $this->is_rendering = false;
            $this->hook('beforeOutput');

            if (isset($_GET['__atk_callback']) && $this->catch_runaway_callbacks) {
                $this->terminate('!! Callback requested, but never reached. You may be missing some arguments in '.$_SERVER['REQUEST_URI']);
            }

            return $this->html->template->render();
        } catch (TerminatedException $e) {
            return $e->output ? $e->output : '';
        }
    }

    /**
     * Constructor.
     *
     * @param array $defaults Configuration Options
     */
    public function __construct($defaults = [])
    {
        $this->app = $this;

        // Process defaults
        if (is_string($defaults)) {
            $defaults = ['title' => $defaults];
        }

        if (isset($defaults[0])) {
            $defaults['title'] = $defaults[0];
            unset($defaults[0]);
        }

        /*
        if (is_array($defaults)) {
            throw new Exception(['Constructor requires array argument', 'arg' => $defaults]);
        }*/
        $this->setDefaults($defaults);
        /*

        foreach ($defaults as $key => $val) {
            if (is_array($val)) {
                $this->$key = array_merge(isset($this->$key) && is_array($this->$key) ? $this->$key : [], $val);
            } elseif (!is_null($val)) {
                $this->$key = $val;
            }
        }
         */

        // Set up template folder
        $this->template_dir = base_path('vendor/atk4/ui/template/' . $this->skin);

        // Set our exception handler
        if ($this->catch_exceptions) {
            set_exception_handler(function ($exception) {
                return $this->caughtException($exception);
            });
        }

        if (!$this->_initialized) {
            //$this->init();
        }

        if ($this->fix_incompatible) {
            if (PHP_MAJOR_VERSION >= 7) {
                set_error_handler(function ($errno, $errstr) {
                    return strpos($errstr, 'Declaration of') === 0;
                }, E_WARNING);
            }
        }

        // Always run app on shutdown
        if ($this->always_run) {
            if ($this->_cwd_restore) {
                $this->_cwd_restore = getcwd();
            }

            register_shutdown_function(function () {
                if (is_string($this->_cwd_restore)) {
                    chdir($this->_cwd_restore);
                }

                if (!$this->run_called) {
                    try {
                        $this->run();
                    } catch (TerminatedException $e) {
                        //-- This is okay!
                    } catch (\Exception $e) {
                        $this->caughtException($e);
                    }
                }
            });
        }

        // Set up UI persistence
        if (!isset($this->ui_persistence)) {
            $this->ui_persistence = new \atk4\ui\Persistence\UI();
        }
    }


    /**
     * Will perform a preemptive output and terminate. Do not use this
     * directly, instead call it form Callback, jsCallback or similar
     * other classes.
     *
     * @param string $output
     *
     * @throws \App\Ui\TerminatedException
     */
    public function terminate($output = null)
    {
        $this->run_called = true; // prevent shutdown function from triggering.

        throw new TerminatedException($output, 'Application terminated');
    }

    /**
     * Initialize JS and CSS includes.
     */
    public function initIncludes()
    {
        parent::initIncludes();

        $this->html->template->appendHTML(
            'HEAD',
            $this->getTag('script', '$.ajaxSetup({
    headers: {
        \'X-CSRF-TOKEN\': \'' . csrf_token() . '\'
    }
});')
        );
    }


}