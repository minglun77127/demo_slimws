<?php
spl_autoload_register(function ($class_name) {
    require_once $class_name . '.php';
});

class ServiceContainer{
    public static function getService($service){
        return new $service;
    }
}