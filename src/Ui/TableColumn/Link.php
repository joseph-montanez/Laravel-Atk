<?php

namespace Atk\Laravel\Ui\TableColumn;

/**
 * Class Link
 *
 * @category Ui
 * @package  Atk\Laravel\Ui\TableColumn
 * @author   Joseph Montanez <sutabi@gmail.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/joseph-montanez/atk-laravel
 */
class Link extends \atk4\ui\TableColumn\Link
{
    /**
     * Return a URL string from a callable
     *
     * @var callable $cb
     */
    public $cb;

    public function __construct($page = null, $args = [], $cb = null)
    {
        if (is_array($page)) {
            $page = ['page' => $page];
        } elseif (is_string($page)) {
            $page = ['url' => $page];
        }
        if ($args) {
            $page['args'] = $args;
        }

        if (is_callable($cb)) {
            $this->cb = $cb;
        }

        parent::__construct($page);
    }

    public function getHTMLTags($row, $field)
    {
        if (is_callable($this->cb)) {
            return ['c_'.$this->short_name => call_user_func($this->cb, $row->get())];
        }

        // Decide on the content
        if ($this->url) {
            return ['c_'.$this->short_name => $this->url->set($row->get())->render()];
        }

        $p = $this->page ?: [];

        foreach ($this->args as $key => $val) {
            if (is_numeric($key)) {
                $key = $val;
            }

            if ($row->hasElement($val)) {
                $p[$key] = $row[$val];
            }
        }

        return ['c_'.$this->short_name => $this->table->url($p)];
    }
}