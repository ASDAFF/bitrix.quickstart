<?php
/**
 * Created by Multiline / A M I O.
 * User: r.panfilov@amio.ru
 * Date: 21.10.14
 * Time: 17:00
 */

namespace Ml2WebForms;

use Bitrix\Main;
use Bitrix\Main\Application;
use Bitrix\Main\DB\MssqlConnection;
use Bitrix\Main\DB\OracleConnection;
use Bitrix\Main\Entity;
use Bitrix\Main\Type;
use Bitrix\Main\Localization\Loc;

/**
 * Class MlAdminPanelBuilderEntity for module entities configuration
 * @package Ml2WebForms
 */
class MlAdminPanelBuilderEntity {
    /**
     * Returns module entities fields configuration map
     *
     * @return mixed
     */
    public static function getConfig() {
        return array();
    }

    /**
     * Returns module admin menu configuration
     *
     * @return mixed
     */
    public static function getMenu() {
        return array();
    }

    /**
     * Prepares expression for entity field
     *
     * @param string $field field name
     * @param array $list list of select options
     * @return array
     */
    public static function compileSelectExpression($field, $list = array()) {
        if (!(is_array($list) && count($list) > 0)) {
            $expression = '(%s)';
        } else {
            $expression = '(CASE';
            foreach ($list as &$variant) {
                $expression .= ' WHEN %s = \'' . $variant['id'] . '\' THEN \'' . $variant['text'] . '\'';
            }
            $expression .= ' ELSE \'\' END)';
        }

        $result = array(
            $expression,
        );

        for ($i = 0; $i < count($list); $i++) {
            $result[] = $field;
        }

        return $result;
    }

    /**
     * Creates menu items array for admin panel menu
     *
     * @param array $items menu config
     * @return array
     */
    public static function compileMenu(&$items = array()) {
        $menu_config = &static::getMenu();
        if (@count($items) > 0) {
            $menu_config['menu'] = &$items;
        }
	//var_dump($menu_config);

        $module_config = &static::getConfig();

        $arMenu = array();
        foreach ($menu_config['menu'] as $key => $value) {
            if (!is_array($value)) {
                $key = $value;
                $value = array();
            }
            $lkey = strtolower($key);
            $arMenuItem = array(
                "items_id" => "{$menu_config['module']}_{$lkey}_items",
                "text" => @$value['text'] ? $value['text'] : Loc::getMessage("{$menu_config['module']}_{$lkey}_list_title"),
                "title" => @$value['title'] ? $value['title'] : Loc::getMessage("{$menu_config['module']}_{$lkey}_list_title_alt"),
                "url" => @$value['url'] ? $value['url'] : (isset($module_config[$key]) ? "{$menu_config['page']}?sect=list&entity={$key}&lang=".LANGUAGE_ID : false),
                "more_url" =>  @$value['more_url'] ? $value['more_url'] :
                    (!@$value['url'] ? array(
                        "{$menu_config['page']}?sect=list&entity={$key}&lang=".LANGUAGE_ID,
                        "{$menu_config['page']}?sect=edit&entity={$key}&lang=".LANGUAGE_ID,
                    ) : array()),
            );
            $arSubItems = array();
            if (@count($value['items']) > 0) {
                $arSubItems = static::compileMenu($value['items']);
            }
            if (@count($arSubItems) > 0) {
                $arMenuItem['items'] = $arSubItems;
            }

            $arMenu[] = $arMenuItem;
        }

        return $arMenu;
    }

    /**
     * Creates mysql tables from entities configuration
     */
    public static function installDB() {
        \CModule::IncludeModule(ADMIN_MODULE_NAME);

        $entities_config = static::getConfig();

        foreach ($entities_config as $entity => &$entity_config) {
            /** @var \Bitrix\Main\Entity\DataManager $entityTable */
            $entityTable = "\\" . ADMIN_MODULE_NAMESPACE . "\\" . $entity . "Table";

            $query = "";
            $primaryKey = '';
            $index = array();
            $queryFields = array();
            foreach ($entity_config['fields'] as $code => $field_properties) {
                if (is_numeric($code)) {
                    $code = $field_properties;
                    $field_properties = array();
                }

                if (@$field_properties['type'] == 'M:N' && strlen(@$field_properties['from']['referenceEntity']) > 0) {
                    /** @var \Bitrix\Main\Entity\DataManager $referenceEntityTable */
                    $referenceEntityTable = "\\" . ADMIN_MODULE_NAMESPACE . "\\" . $field_properties['from']['referenceEntity'] . "Table";
                    $vsquery = "
                        CREATE TABLE IF NOT EXISTS `" . $referenceEntityTable::getTableName() . "` (
                            `{$field_properties['from']['reference']}` INT NOT NULL,
                            `{$field_properties['from']['joined']}` INT NOT NULL,
                            PRIMARY KEY(`{$field_properties['from']['reference']}`, `{$field_properties['from']['joined']}`)
                        )
                    ";

                    Application::getConnection()->query($vsquery);
                } else {
                    $data_type = '';
                    /** @var \Bitrix\Main\Entity\ScalarField $entityField */
                    $entityField = MlAdminPanelBuilder::getFieldFromMap($code, $entityTable::getMap());
                    if ($entityField) {
                        $data_type = MlAdminPanelBuilder::getFieldType($entityField);
                    }

                    if (in_array($data_type, array('expression', 'reference')) || in_array(@$field_properties['type'], array('1:M', 'M:N')))
                        continue;

                    if ($entityField->isPrimary()) {
                        $primaryKey = ", PRIMARY KEY (`{$code}`)";
                    } elseif (@$field_properties['index']) {
                        $index[] = "INDEX (`{$code}`)";
                    }

                    $autocomplete = '';
                    if ($entityField->isAutocomplete()) {
                        $autocomplete = ' AUTO_INCREMENT';
                    }

                    $type = '';
                    switch ($data_type) {
                        case 'boolean':
                            $type = 'TINYINT(1)';
                            break;
                        case 'date':
                            $type = 'DATE';
                            break;
                        case 'datetime':
                            $type = 'DATETIME';
                            break;
                        case 'enum':
                            if (@count($field_properties['values']) == 0)
                                $field_properties['values'] = array();
                            $type = 'ENUM(\'' . implode('\',\'', $field_properties['values']) . '\')';
                            break;
                        case 'float':
                            $type = 'DOUBLE';
                            break;
                        case 'integer':
                            switch (@$field_properties['type']) {
                                case 'checkbox':
                                    $type = 'TINYINT(1)';
                                    break;
                                default:
                                    $type = 'INT';
                                    break;
                            }
                            break;
                        case 'string':
                            $type = 'VARCHAR(1000)';
                            break;
                        case 'text':
                            $type = 'TEXT';
                            break;
                    }

                    if (strlen($type) > 0) {
                        $queryFields[] = "`{$code}` {$type} NOT NULL{$autocomplete}";
                    }
                }
            }

            if (count($queryFields) > 0) {
                $query .= implode(', ', $queryFields);
            }

            if (strlen($primaryKey) > 0) {
                $query .= $primaryKey;
            }

            if (count($index) > 0) {
                $query .= ', ' . implode(', ', $index);
            }

            if (strlen($query) > 0) {
                $query = "
                    CREATE TABLE IF NOT EXISTS `" . $entityTable::getTableName() . "` ({$query})";
            }

            if (strlen($query) > 0) {
                Application::getConnection()->query($query);
            }
        }
    }

    /**
     * Drops mysql tables using entities config
     */
    public static function unInstallDB() {
        \CModule::IncludeModule(ADMIN_MODULE_NAME);
        $entities_config = &static::getConfig();

        foreach ($entities_config as $entity => &$entity_config) {
            foreach ($entity_config['fields'] as $code => $field_properties) {
                if (is_numeric($code)) {
                    $code = $field_properties;
                    $field_properties = array();
                }
                if (@$field_properties['type'] == 'M:N' && strlen(@$field_properties['from']['referenceEntity']) > 0) {
                    /** @var \Bitrix\Main\Entity\DataManager $referenceEntityTable */
                    $referenceEntityTable = "\\" . ADMIN_MODULE_NAMESPACE . "\\" . $field_properties['from']['referenceEntity'] . "Table";
                    $vsquery = "DROP TABLE IF EXISTS `" . $referenceEntityTable::getTableName() . "`";

                    Application::getConnection()->query($vsquery);
                }
            }

            /** @var \Bitrix\Main\Entity\DataManager $entityTable */
            $entityTable = "\\" . ADMIN_MODULE_NAMESPACE . "\\" . $entity . "Table";
            $query = "DROP TABLE IF EXISTS `" . $entityTable::getTableName() . "`";
            Application::getConnection()->query($query);
        }
    }

    /**
     * Reindexing module content
     * @param array $NS
     * @param string $oCallback
     * @param string $callback_method
     * @return array
     */
    public static function OnReindex($NS = array(), $oCallback = NULL, $callback_method = "") {
        return array();
    }
}