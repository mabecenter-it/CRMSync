<?php
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/Contacts/Contacts.php';

class HelloWorld extends Vtiger_Detail_View {

    public function getFooterScripts(Vtiger_Request $request) {
        // Obtiene los scripts originales
        $footerScriptInstances = parent::getFooterScripts($request);

        // Define la ruta de tu archivo JS personalizado
        $jsFileNames = [
            'modules/SalesOrder/resources/SalesOrder_Detail_Custom.js'
        ];

        // Convierte las rutas a objetos que vtiger entiende
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);

        // Combina los scripts originales con el nuevo script
        return array_merge($footerScriptInstances, $jsScriptInstances);
    }

    public static function vtlib_handler($moduleName, $eventType) {
        if (in_array($eventType, ['module.postinstall', 'module.postupdate'])) {
            self::addCustomFields();
            self::customAccountsFields();
            self::customContactsFields();
            $moduleInstance = Vtiger_Module::getInstance('SalesOrder');
            $moduleInstance->addLink('HEADERSCRIPT', 'HelloWorldJS', 'modules/HelloWorld/resources/custom_script.js');

        }
    }
     public static function customAccountsFields() {
        $module = Vtiger_Module::getInstance('Accounts');
        $arrayTypes = ['ship_country', 'bill_country'];
        foreach ($arrayTypes as $type) {
            $field = Vtiger_Field::getInstance($type, $module);

            if ($field) {
                $db = PearDatabase::getInstance();
                $field->uitype = 15;
                $db->pquery("UPDATE vtiger_field SET uitype = ? WHERE fieldid = ?",
                    [$field->uitype, $field->id]);
                $field->setPicklistValues(['United States']);
                $field->save();
            }
        }
    }

    public static function customContactsFields() {
        $module = Vtiger_Module::getInstance('Contacts');
        $module->customizedTable = 'vtiger_contactscf';
        $arrayFields = ['contact_no', 'firstname', 'lastname', 'second_name', 'gender', 'social_security', 'migratory', 'work', 'income', 'language', 'smoke', 'jail', 'relationship', 'document', 'expiration'];

        $block = Vtiger_Block::getInstance('LBL_CONTACT_INFORMATION', $module);

        foreach ($arrayFields as $fieldName) {
            self::createField($module, $fieldName, $block, null);
        }
    }

    public static function addCustomFields() {
        $module = Vtiger_Module::getInstance('SalesOrder');
        $module->customizedTable = 'vtiger_salesordercf';

        if (!$module) {
            return;
        }

        // Para cada índice del 1 al 7, se crea un conjunto de campos
        for ($index = 1; $index <= 7; $index++) {
            self::createArrayField($module, $index, ['dependent', 'apply']);
        }
    }

    public static function createArrayField($module, $index, $arrayTypes) {
        // Crear un bloque único para los campos personalizados
        $blockLabel = 'Dependent #' . $index;
        $customBlock = Vtiger_Block::getInstance($blockLabel, $module);
        if (!$customBlock) {
            $customBlock = new Vtiger_Block();
            $customBlock->label = $blockLabel;
            $module->addBlock($customBlock);
        }

        foreach ($arrayTypes as $type) {
            self::createField($module, $type, $customBlock, $index);
        }
    }

    public static function createField($module, $type, $customBlock, $index = null) {

        $field = Vtiger_Field::getInstance($type, $module);

        if (!$field) {
            // Definir el nombre del campo
            $fieldName = $index ? "cf_{$type}_{$index}" : "cf_{$type}";
            // Intentar obtener la instancia del campo si ya existe
            $field = Vtiger_Field::getInstance($fieldName, $module) ?: new Vtiger_Field();

            // Configurar propiedades del campo
            $field->name = $fieldName;
            $field->label = ucwords(str_replace('_', ' ', $type));
            $field->column = $fieldName;
            $field->table = $module->customizedTable;
        }
        // Configuración de tipos de campo
        $fieldsConfig = [
            'contact_no'        => ['sequence' => 4],
            'firstname'         => ['sequence' => 1],
            'second_name'       => ['uitype' => 55, 'typeofdata' => 'V~O', 'sequence' => 2],
            'lastname'          => ['sequence' => 3],
            'expiration'        => ['uitype' => 5, 'typeofdata' => 'D~O'],
            'income'            => ['uitype' => 7,  'typeofdata' => 'I~O'],
            'dependent'         => ['uitype' => 10, 'typeofdata' => 'N~O', 'relatedModules' => ['Contacts']],
            'apply'             => ['uitype' => 56, 'typeofdata' => 'C~O'],
            'work'              => ['uitype' => 15, 'typeofdata' => 'V~O', 'picklist' => ['1099', 'w2', 'subsidy']],
            'language'          => ['uitype' => 15, 'typeofdata' => 'V~O', 'picklist' => ['Spanish', 'English']],
            'smoke'             => ['uitype' => 15, 'typeofdata' => 'V~O', 'picklist' => ['Yes', 'No']],
            'jail'              => ['uitype' => 15, 'typeofdata' => 'V~O', 'picklist' => ['Yes', 'No']],
            'relationship'      => ['uitype' => 15, 'typeofdata' => 'V~O', 'picklist' => ['Owner', 'Spouse', 'Child', 'Parent', 'Sibling', 'Other']],
            'gender'            => ['uitype' => 15, 'typeofdata' => 'V~O', 'picklist' => ['Female', 'Male']],
            'migratory'         => ['uitype' => 15, 'typeofdata' => 'V~O', 'picklist' => [
                'Citizen', 'Resident', 'Work Permission', 'Social Security',
                'Denegate Coverage', 'Fiscal Credit', 'No Jail', 'Coverage Health'
            ]],
            'document'          => ['uitype' => 33, 'typeofdata' => 'V~O', 'picklist' => [
                'Citizen', 'Resident', 'Work Permission', 'Social Security',
                'Denegate Coverage', 'Fiscal Credit', 'No Jail', 'Coverage Health'
            ]],
            'social_security'   => ['uitype' => 55, 'typeofdata' => 'V~O'],
        ];

        $customBlock->addField($field);
        $field->save();

        // Aplicar la configuración si el tipo de campo está definido
        if (isset($fieldsConfig[$type])) {
            $config = $fieldsConfig[$type];

            if (!empty($config['uitype'])) {
                $field->uitype = $config['uitype'];
            }

            if (!empty($config['typeofdata'])) {
                $field->typeofdata = $config['typeofdata'];
            }

            // Asignar módulos relacionados si existen
            if (!empty($config['relatedModules'])) {
                $field->setRelatedModules($config['relatedModules']);
            }

            if (!empty($config['sequence'])) {
                $db = PearDatabase::getInstance();
                $db->pquery("UPDATE vtiger_field SET sequence = ? WHERE fieldid = ?",
                    [$config['sequence'], $field->id]);
            }

            if (!empty($config['picklist'])) {
                $field->setPicklistValues($config['picklist']);
            }
        }

        // Agregar y guardar el campo

    }
}

class SalesOrder_DetailView_Custom extends Vtiger_Detail_View {
    public function getFooterScripts(Vtiger_Request $request) {
        // Obtiene los scripts originales
        $footerScriptInstances = parent::getFooterScripts($request);

        // Define la ruta de tu archivo JS personalizado
        $jsFileNames = [
            'modules/SalesOrder/resources/SalesOrder_Detail_Custom.js'
        ];

        // Convierte las rutas a objetos que vtiger entiende
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);

        // Combina los scripts originales con el nuevo script
        return array_merge($footerScriptInstances, $jsScriptInstances);
    }
}

// Handler obligatorio de Vtiger
function vtlib_handler($moduleName, $eventType) {
    HelloWorld::vtlib_handler($moduleName, $eventType);
}
?>
