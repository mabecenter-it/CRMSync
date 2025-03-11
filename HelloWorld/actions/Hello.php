<?php

class HelloWorld_Hello_Action extends Vtiger_Action_Controller {

    public function checkPermission(Vtiger_Request $request) {
        return true; // permite cualquier acceso
    }

    public function process(Vtiger_Request $request) {
        $response = new Vtiger_Response();
        $response->setResult(['message' => 'Hello World!']);
        $response->emit();
    }
}
