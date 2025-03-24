<?php

class FieldsConfig {
    private static $config;

    public static function load() {
        if (!self::$config) {
            $jsonPath = __DIR__ . '/../config/fields.json';
            self::$config = json_decode(file_get_contents($jsonPath), true);
        }
    }

    public static function getModules() {
        self::load();
        return self::$config['modules'];
    }

    public static function getBlock($blockKey) {
        self::load();
        return self::$config['blocks'][$blockKey] ?? null;
    }

    public static function getFieldDefinition($fieldKey) {
        self::load();
        return self::$config['fields'][$fieldKey] ?? null;
    }
}
