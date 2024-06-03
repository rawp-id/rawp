<?php
namespace CLI;

class CreateControllerCommand extends Command
{
    public function execute($args)
    {
        if (empty($args)) {
            echo "Please provide a controller name.\n";
            return;
        }

        $controllerName = str_replace('/', DIRECTORY_SEPARATOR, $args[0]); // Ganti garis miring dengan pemisah direktori
        $namespace = 'App\\Controllers' . (dirname($controllerName) != '.' ? '\\' . str_replace('/', '\\', dirname($controllerName)) : ''); // Buat namespace dengan mengganti garis miring dengan namespace separator
        $controllerClassName = basename($controllerName); // Ambil nama kelas dari nama file

        // Content of the controller
        $controllerContent = "<?php\nnamespace $namespace;\n\nuse Core\Controller;\n\nclass $controllerClassName extends Controller\n{\n    // Tambahkan logika kontroler Anda di sini\n}\n";

        $directory = __DIR__ . "/../../app/Controllers/" . (dirname($controllerName) != '.' ? dirname($controllerName) : ''); // Buat direktori secara rekursif jika belum ada
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true); // Buat direktori secara rekursif jika belum ada
        }

        file_put_contents($directory . DIRECTORY_SEPARATOR . "{$controllerClassName}.php", $controllerContent); // Gunakan nama kelas untuk nama file

        echo "Controller {$controllerName} berhasil dibuat.\n";
    }
}
