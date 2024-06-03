<?php
namespace CLI;

class MigrateRunListCommand extends Command
{
    public function execute($args)
    {
        $modelDirectory = __DIR__ . "/../../app/Models/";
        $modelFiles = scandir($modelDirectory);
        $models = [];

        foreach ($modelFiles as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $modelDirectory . $file;
                if (is_file($filePath)) {
                    require_once $filePath;
                    $className = pathinfo($file, PATHINFO_FILENAME);
                    $fullClassName = "\\App\\Models\\$className";
                    if (method_exists($fullClassName, 'migrate')) {
                        $models[] = $className;
                    }
                }
            }
        }

        if (empty($models)) {
            echo "No models found with migrate method.\n";
            return;
        }

        echo "Models with migrate method:\n";
        foreach ($models as $index => $model) {
            echo ($index + 1) . ". $model\n";
        }

        echo "\nEnter the numbers of models to migrate (comma-separated), or 'all' to migrate all models: \n";
        $input = trim(fgets(STDIN));

        if ($input === 'all') {
            foreach ($models as $selectedModel) {
                $className = "\\App\\Models\\$selectedModel";
                $className::migrate();
                echo "Migration for model $selectedModel executed successfully.\n";
            }
        } else {
            $selectedModelArray = explode(',', $input);

            foreach ($selectedModelArray as $selectedModelIndex) {
                $selectedModelIndex = (int)trim($selectedModelIndex);
                if ($selectedModelIndex > 0 && $selectedModelIndex <= count($models)) {
                    $selectedModel = $models[$selectedModelIndex - 1];
                    $className = "\\App\\Models\\$selectedModel";
                    $className::migrate();
                    echo "Migration for model $selectedModel executed successfully.\n";
                } else {
                    echo "Invalid model number: $selectedModelIndex\n";
                }
            }
        }
    }
}
