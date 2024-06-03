<?php
// src/CLI/MigrateResetCommand.php
namespace CLI;

use Core\Migration;

class MigrateResetCommand {
    public function execute($args) {
        Migration::dropAll();
        
        echo "All tables dropped successfully.\n";
    }
}