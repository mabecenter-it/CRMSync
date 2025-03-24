<?php

include_once 'modules/HelloWorld/helpers/FieldManager.php';
include_once 'modules/HelloWorld/views/SalesOrder/Detail.php';

class ModuleEventHandler {

    public static function handle($moduleName, $eventType) {
        if (in_array($eventType, ['module.postinstall', 'module.postupdate'])) {
            FieldManager::initializeFields();
            self::registerCustomScripts();
        }
    }

    private static function registerCustomScripts() {
        $moduleInstance = Vtiger_Module::getInstance('SalesOrder');
        $moduleInstance->addLink('HEADERSCRIPT', 'HelloWorldJS', 'modules/HelloWorld/resources/custom_script.js');
    }
}
