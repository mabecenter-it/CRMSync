<?php
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/Contacts/Contacts.php';

class HelloWorld {

    public static function vtlib_handler($moduleName, $eventType) {
        if (in_array($eventType, ['module.postinstall', 'module.postupdate'])) {
            self::addCustomFields();
            self::customAccountsFields();
            self::customContactsFields();
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
            self::createField($module, null, $fieldName, $block);
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
            self::createField($module, $index, $type, $customBlock);
        }
    }

    public static function createField($module, $index = null, $type, $customBlock) {
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
            $field->save();
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

            // Agregar y guardar el campo
            $field->save();
            $customBlock->addField($field);

            // Asignar valores de Picklist si existen
            if (!empty($config['picklist'])) {
                $field->setPicklistValues($config['picklist']);
            }
        }


    }
}

// Handler obligatorio de Vtiger
function vtlib_handler($moduleName, $eventType) {
    HelloWorld::vtlib_handler($moduleName, $eventType);
}
?>
