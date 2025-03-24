<?php

class FieldManager {

    public static function initializeFields() {
        foreach (FieldsConfig::getModules() as $moduleName => $moduleConfig) {
            $module = Vtiger_Module::getInstance($moduleName);
            if (!$module) continue;

            foreach ($moduleConfig['blocks'] as $blockKey) {
                $blockConfig = FieldsConfig::getBlock($blockKey);
                if ($blockConfig['dynamic']) {
                    self::createDynamicBlocks($module, $blockConfig);
                } else {
                    self::createBlock($module, $blockConfig['label'], $blockConfig['fields']);
                }
            }
        }
    }

    private static function createDynamicBlocks($module, $blockConfig) {
        $iterations = $blockConfig['dynamic_settings']['iterations'];
        for ($i = 1; $i <= $iterations; $i++) {
            $label = str_replace('#', $i, $blockConfig['label']);
            self::createBlock($module, $label, $blockConfig['fields'], $i);
        }
    }

    private static function createBlock($module, $label, $fields, $index = null) {
        $block = Vtiger_Block::getInstance($label, $module) ?: new Vtiger_Block();
        if (!$block->id) {
            $block->label = $label;
            $module->addBlock($block);
        }

        foreach ($fields as $fieldType) {
            self::createField($module, $block, $fieldType, $index);
        }
    }

    private static function createField($module, $block, $fieldType, $index = null) {
        $fieldConfig = FieldsConfig::getFieldDefinition($fieldType);
        if (!$fieldConfig) return;

        $fieldName = $index ? "cf_{$fieldType}_{$index}" : "cf_{$fieldType}";
        if (!Vtiger_Field::getInstance($fieldName, $module)) {
            $field = new Vtiger_Field();
            $field->name = $fieldName;
            $field->label = ucwords(str_replace('_', ' ', $fieldType));
            $field->table = $fieldConfig['tablename'];
            $field->column = $fieldName;
            $field->uitype = $fieldConfig['uitype'];
            $field->typeofdata = $fieldConfig['typeofdata'];

            $block->addField($field);
            $field->save();

            if (isset($fieldConfig['picklist'])) {
                $field->setPicklistValues($fieldConfig['picklist']);
            }
        }
    }
}
