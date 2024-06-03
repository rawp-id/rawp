<?php
namespace CLI;

use Core\Migration;

class DropTableCommand extends Command
{
    public function execute($args)
    {
        if (count($args) !== 1) {
            echo "Usage: php console drop:table <table_name>\n";
            return;
        }

        $tableName = $args[0];

        Migration::drop($tableName);

        echo "Table '$tableName' dropped successfully.\n";
    }
}
