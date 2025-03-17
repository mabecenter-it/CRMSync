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
        #$arrayFields = ['secondname', 'gender', 'social_security', 'document', 'work', 'income', 'language', 'smoke', 'jail'];
        $arrayFields = ['secondname'];

        $block = Vtiger_Block::getInstance('LBL_CONTACT_INFORMATION', $module);
    
        foreach ($arrayFields as $fieldName) {           
            $field = Vtiger_Field::getInstance($fieldName, $module);
            // Crear una nueva instancia de Vtiger_Field
            if (!$field) {
                $field = new Vtiger_Field();
            }
    
            $field->name = $fieldName;
            $field->label = 'Testing'; // Agregado el ";"
            $field->column = $fieldName;
            $field->table = $module->customizedTable;
            $field->uitype = 55; // Tipo de dato (Número)
            $field->typeofdata = 'V~O'; // Campo opcional
            $field->sequence = 4; // Especificar la secuencia del campo
    
            // Añadir el campo al bloque
            $block->addField($field);
            $field->save(); // Guardar después de configurar todos los valores
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
            self::createArrayField($module, $index, ['dependent', 'relationship', 'document', 'expiration']);
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

    public static function createField($module, $index, $type, $customBlock) {
        $fieldName = "cf_" . $type . "_" . $index;
        $field = Vtiger_Field::getInstance($fieldName, $module);

        if (!$field) {
            $field = new Vtiger_Field();
        }

        $field->name = $fieldName;
        $field->label = ucfirst($type);
        $field->column = $fieldName;
        $field->table = $module->customizedTable;

        // Configurar opciones de picklist según el tipo
        switch ($type) {
            case 'relationship':
                $field->setPicklistValues([
                    'Owner',
                    'Spouse',
                    'Child',
                    'Parent',
                    'Sibling',
                    'Other'
                ]);
                break;
            case 'document':
                $field->setPicklistValues([
                    'Citizen',
                    'Resident',
                    'Work Permission',
                    'Social Security',
                    'Denegate Coverage',
                    'Fiscal Credit',
                    'No Jail',
                    'Coverage Health'
                ]);
                break;
            default:
                break;
        }

        $customBlock->addField($field);
        $field->save();

        switch ($type) {
            case 'dependent':
                $field->uitype = 10; // Número
                $field->typeofdata = 'N~O';
                $field->setRelatedModules(['Contacts']);
                break;
            case 'relationship':
                $field->uitype = 15; // Picklist simple
                $field->typeofdata = 'V~O';
                break;
            case 'document':
                $field->uitype = 33; // Picklist multiselección
                $field->typeofdata = 'V~O';
                break;
            case 'expiration':
                $field->uitype = 5; // Date
                $field->typeofdata = 'D~O';
                break;
            default:
                break;
        }

        $customBlock->addField($field);
    }
}

// Handler obligatorio de Vtiger
function vtlib_handler($moduleName, $eventType) {
    HelloWorld::vtlib_handler($moduleName, $eventType);
}
?>
