<?php

namespace Atk\Laravel\Ui;


use Exception;
use Throwable;

/**
 * Class TerminatedException
 *
 * @category Exception
 * @package  Atk\Laravel\Ui
 * @author   Joseph Montanez <sutabi@gmail.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/joseph-montanez/atk-laravel
 */
class TerminatedException extends Exception
{
    public $output = false;

    /**
     * TerminatedException constructor.
     *
     * @param bool            $output   The JSON or HTML to output when terminated
     * @param string          $message  The error message
     * @param int             $code     The error code
     * @param \Throwable|null $previous The previously thrown exception
     */
    public function __construct(
        $output = false,
        string $message = "",
        int $code = 0,
        Throwable $previous = null
    ) {
        $this->output = $output;
        parent::__construct($message, $code, $previous);
    }

}