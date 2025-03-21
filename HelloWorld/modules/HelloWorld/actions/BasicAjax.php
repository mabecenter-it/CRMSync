<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License.
 * The Original Code is: vtiger CRM Open Source.
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class HelloWorld_BasicAjax_Action extends Vtiger_BasicAjax_Action {

    public function process(Vtiger_Request $request) {
        // Se espera que se envíen estos parámetros:
        // - search_module: el módulo a consultar (por ejemplo, 'Contacts')
        // - base_record: el ID del registro de Contacts
        // - fields: lista separada por comas de los campos deseados (por ejemplo, "phone_work,phone")
        $searchModule = $request->get('search_module');
        $baseRecord = $request->get('base_record');
        $fields = $request->get('fields');

        if(empty($searchModule) || empty($baseRecord) || empty($fields)) {
            throw new Exception('Faltan parámetros requeridos.');
        }
        $fieldsArray = explode(',', $fields);
        $result = array();

        // Obtiene el registro del módulo especificado
        $recordModel = Vtiger_Record_Model::getInstanceById($baseRecord, $searchModule);
        if($recordModel) {
            foreach($fieldsArray as $fieldName) {
                // Para cada campo solicitado, se obtiene el valor (si existe)
                $result[$fieldName] = $recordModel->get($fieldName);
            }
        }

        // Envía la respuesta en formato JSON
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}
