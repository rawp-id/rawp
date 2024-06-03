<?php
// src/CLI/Command.php
namespace CLI;

abstract class Command {
    abstract public function execute($args);
}