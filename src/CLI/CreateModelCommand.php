<?php
// create:model command

namespace CLI;

class CreateModelCommand extends Command
{
    public function execute($args)
    {
        $modelName = $args[0];
        $tableName = strtolower($modelName) . 's'; // Generate table name from model name (e.g., 'User' -> 'users')
        $modelContent = <<<PHP
<?php
namespace App\Models;

use Core\Migration;
use Core\Model;

class {$modelName} extends Model
{
    public static function migrate()
    {
        Migration::table('{$tableName}');
        Migration::id();
        // Add your columns here
        Migration::timestamps();
        Migration::execute();
    }
}

PHP;

        $directory = __DIR__ . "/../../app/Models/";
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true); // Create directory recursively
        }

        file_put_contents($directory . "{$modelName}.php", $modelContent);

        echo "Model {$modelName} created successfully.\n";
    }
}
