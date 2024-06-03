<?php
namespace CLI;

class ServeCommand extends Command
{
    public function execute($args)
    {
        // Menginisialisasi nilai default untuk host dan port
        $host = '127.0.0.1';
        $port = '8000';

        // Mengecek apakah ada argumen yang diberikan
        if (!empty($args)) {
            // Mencari argumen --port di dalam daftar argumen
            $portIndex = array_search('--port', $args);
            if ($portIndex !== false && isset($args[$portIndex + 1])) {
                // Jika argumen --port ditemukan, gunakan nilai port yang diberikan
                $port = $args[$portIndex + 1];
                // Hapus argumen --port beserta nilai port-nya dari daftar argumen
                unset($args[$portIndex], $args[$portIndex + 1]);
                $args = array_values($args); // Reset kembali indeks array
            }
        }

        // Membangun command untuk menjalankan server
        $command = sprintf('php -S %s:%s -t public', $host, $port);

        // Menampilkan pesan bahwa server sedang berjalan
        echo "Starting server at http://$host:$port\n";
        echo "Press Ctrl+C to stop the server\n";

        // Menjalankan server menggunakan perintah passthru
        passthru($command);
    }
}
