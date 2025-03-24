<?php

include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/HelloWorld/handlers/ModuleEventHandler.php';

// Punto principal de entrada
function vtlib_handler($moduleName, $eventType) {
    ModuleEventHandler::handle($moduleName, $eventType);
}
