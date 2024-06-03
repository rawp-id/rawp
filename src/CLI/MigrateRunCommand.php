<?php
namespace CLI;

class MigrateRunCommand extends Command
{
    public function execute($args)
    {
        // Jika argumen diberikan, gunakan hanya model-model yang sesuai dengan argumen
        if (!empty($args)) {
            foreach ($args as $arg) {
                $modelFile = __DIR__ . "/../../app/Models/{$arg}.php";
                if (file_exists($modelFile)) {
                    require_once $modelFile;
                    $className = "\\App\\Models\\$arg";
                    if (method_exists($className, 'migrate')) {
                        $className::migrate();
                        echo "Migration for model $arg executed successfully.\n";
                    } else {
                        echo "Migration method not found for model $arg.\n";
                    }
                } else {
                    echo "Model file not found for $arg.\n";
                }
            }
        } else {
            // Jika tidak ada argumen, jalankan migrasi untuk semua model
            $modelDirectory = __DIR__ . "/../../app/Models/";
            $modelFiles = scandir($modelDirectory);

            foreach ($modelFiles as $file) {
                if ($file !== '.' && $file !== '..') {
                    $filePath = $modelDirectory . $file;
                    if (is_file($filePath)) {
                        require_once $filePath;
                        $className = pathinfo($file, PATHINFO_FILENAME);
                        $fullClassName = "\\App\\Models\\$className";
                        if (method_exists($fullClassName, 'migrate')) {
                            $fullClassName::migrate();
                            echo "Migration for model $className executed successfully.\n";
                        }
                    }
                }
            }
        }
    }
}
