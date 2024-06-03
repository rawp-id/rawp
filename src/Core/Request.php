<?php
// src/Core/Request.php
namespace Core;

class Request {
    public $method;
    public $path;
    public $params;

    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path = $_SERVER['REQUEST_URI'];
        $this->params = $_REQUEST;
    }

    public function query($key) {
        return isset($this->params[$key]) ? $this->params[$key] : null;
    }

    public function input($key) {
        // Cek apakah parameter ada di request
        if(isset($this->params[$key])) {
            return $this->params[$key];
        }
    
        // Jika tidak ada, coba cari dalam data JSON
        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);
    
        // Jika data JSON tidak null dan memiliki kunci yang sesuai, kembalikan nilainya
        if($data !== null && isset($data[$key])) {
            return $data[$key];
        }
    
        // Jika tidak ada data atau tidak ditemukan dalam JSON, kembalikan null
        return null;
    }    

    public function form($key) {
        return isset($_POST[$key]) ? $_POST[$key] : null;
    }

    public function json() {
        return json_decode(file_get_contents('php://input'), true);
    }

    public function isMethod($method) {
        return strtoupper($method) === $this->method;
    }

    public function validate($rules) {
        $errors = [];

        foreach ($rules as $field => $rule) {
            $value = $this->input($field);
            $rulesArray = explode('|', $rule);

            foreach ($rulesArray as $singleRule) {
                $singleRuleArray = explode(':', $singleRule);
                $ruleName = $singleRuleArray[0];

                switch ($ruleName) {
                    case 'required':
                        if (!$value) {
                            $errors[$field] = "$field is required.";
                        }
                        break;
                    case 'max':
                        $maxLength = $singleRuleArray[1];
                        if (strlen($value) > $maxLength) {
                            $errors[$field] = "$field must be at most $maxLength characters.";
                        }
                        break;
                    // Add more validation rules as needed
                }
            }
        }

        if (!empty($errors)) {
            // Throw exception or handle errors as needed
            throw new \Exception(json_encode($errors));
        }

        return $this->params;
    }
}


