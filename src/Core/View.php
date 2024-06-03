<?php
// src/Core/View.php

namespace Core;

class View
{
    protected $viewPath;

    public function __construct($viewPath)
    {
        $this->viewPath = $viewPath;
    }

    public function render($view, $data = [])
    {
        $viewFile = $this->viewPath . '/' . str_replace('.', '/', $view) . '.php';

        if (file_exists($viewFile)) {
            extract($data);
            ob_start();
            include $viewFile;
            $content = ob_get_clean();

            $content = $this->parseContentDirective($content, $data);
            $content = $this->parseIncludeDirective($content);
            $content = $this->parseForeachDirective($content, $data);

            echo $content;
        } else {
            throw new \Exception("View file not found: $viewFile");
        }
    }

    protected function parseContentDirective($content, $data)
    {
        // Parse @content('...')
        $content = preg_replace_callback('/@content\(\'([^\']+)\'\)/', function ($matches) use ($data) {
            $contentName = $matches[1];
            return isset($data[$contentName]) ? $data[$contentName] : '';
        }, $content);

        return $content;
    }

    protected function parseIncludeDirective($content)
    {
        // Parse @include('...')
        $content = preg_replace_callback('/@import\(\'([^\']+)\'\)/', function ($matches) {
            $filename = $this->viewPath . '/' . str_replace('.', '/', $matches[1]) . '.php';
            return file_exists($filename) ? file_get_contents($filename) : '';
        }, $content);

        return $content;
    }

    protected function parseForeachDirective($content, $data)
    {
        // Parse @foreach($items as $item)
        $content = preg_replace_callback('/@foreach\((.*?)\)/s', function ($matches) use ($data) {
            ob_start();
            eval('foreach(' . $matches[1] . ') { ?>');
            $content = ob_get_clean();
            return $content;
        }, $content);

        // Parse @endforeach
        $content = str_replace('@endforeach', '<?php } ?>', $content);

        return $content;
    }

}
