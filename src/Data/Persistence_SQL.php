<?php

namespace Atk\Laravel\Data;

use atk4\dsql\Connection;
use Illuminate\Database\DatabaseManager;

/**
 * Class Persistence_SQL
 *
 * @category Data
 * @package  Atk\Laravel\Data
 * @author   Joseph Montanez <sutabi@gmail.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/joseph-montanez/atk-laravel
 */
class Persistence_SQL extends \atk4\data\Persistence_SQL
{

    /**
     * Take a laravel connection and pass it to ATK Data
     *
     * @param \Illuminate\Database\DatabaseManager $db The Laravel database manager
     *
     * @return \atk4\data\Persistence_SQL
     * @throws \atk4\data\Exception
     * @throws \atk4\dsql\Exception
     */
    public function __construct(DatabaseManager $db)
    {
        $pdo = $db->connection()->getPdo();

        $conn = new Connection(['connection' => $pdo]);

        parent::__construct($conn);
    }
}