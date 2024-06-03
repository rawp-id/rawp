<?php
// src/CLI/MigrateRefreshCommand.php
namespace CLI;

use Core\Migration;

class MigrateRefreshCommand
{
    public function execute($args)
    {
        Migration::dropAll();

        $list = new MigrateRunListCommand;
        $list->execute($args);

        echo "All tables refreshed successfully.\n";
    }
}