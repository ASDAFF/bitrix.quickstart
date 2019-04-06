<?php
/**
 * Main module class is Singletone
 *
 * @author Максим Соколовский <my.sokolovsky@gmail.com>
 */
class WS_PSettings {

    const MODULE_NAME = 'ws.projectsettings';

    const FIELD_TYPE_NUMERIC    = 'number';
    const FIELD_TYPE_STRING     = 'string';
    const FIELD_TYPE_SIGN       = 'sign';
    const FIELD_TYPE_LIST       = 'list';
    const FIELD_TYPE_USER       = 'user';
    const FIELD_TYPE_USER_GROUP = 'user_group';
    const FIELD_TYPE_IBLOCK     = 'iblock';

    private $_fields = array();

    private $_fieldsData = array();

    private $_fieldsList = array();

    private static $_self;

    /**
     * Get field object
     * @param str $name Field simbol code
     * @return WS_ProjectSettings_Field
     */
    static public function getField($name){
        $self = self::_getInstance();
        if (!$self->_hasField($name)) {
            return null;
        }
        return $self->_getField($name);
    }

    /**
     * Get field value by name
     * @param str $name
     * @param mixed $default any value if empty field
     * @return type
     */
    static public function getFieldValue($name, $default = null) {
        $self = self::_getInstance();
        if (!$self->_hasField($name)) {
            return $default;
        }
        return $self->_getField($name)->getValue();
    }

    /**
     * Setup settings field
     * @param array $params
     *  `label` Label by field
     *  `name`  Name or simbol code by field
     *  `sort`  Order in fields list
     *  `type` Type field
     *  `isMany` Sing many field
     *  `default` Default value
     *  `value` Field value
     *  `variants` Data by variants value (for list type)
     */
    static public function setupField($params) {
        $self = self::_getInstance();
        if (! $name = $params['name']) {
            return null;
        }
        if (! $type = $params['type']) {
            return null;
        } elseif ($type == self::FIELD_TYPE_SIGN) {
            $params['value'] = $params['value'] == 'Y';
            $params['default'] = $params['default'] == 'Y';
        }
        $params['isMany'] = $params['isMany'] == 'Y';
        if ($params['isMany']) {
            $params['value'] = (array)$params['value'];
            $params['default'] = (array)$params['default'];
        }
        $self->_saveFieldData($params);
        if (!in_array($name, $self->_fieldsList)) {
            $self->_fieldsList[] = $name;
            COption::SetOptionString(self::MODULE_NAME, 'list', implode(',', $self->_fieldsList));
        }
    }

    /**
     * Clear field by name registred in module
     * @param str $name
     */
    static public function clearField($name) {
        $self = self::_getInstance();
        if ($self->_hasField($name)) {
            COption::RemoveOption(self::MODULE_NAME, $name);
        }
    }

    /**
     * Clear all settings
     */
    static public function clearAll() {
        COption::RemoveOption(self::MODULE_NAME);
        $self = self::_getInstance();
        $self->_fieldsList = array();
        $self->_fields = array();
        $self->_fieldsData = array();
        COption::SetOptionString(self::MODULE_NAME, 'list', '');
    }

    /**
     * List of names fields registred in module
     * @return array
     */
    static public function getFieldsList () {
        return self::_getInstance()->_fieldsList;
    }

    /**
     * @param str $name Fields simbol code
     * @param mixed $value Value by field
     */
    static public function setFieldValue($name, $value) {
        $self = self::_getInstance();
        if ($self->_hasField($name)) {
            $fieldsData = $self->_getFieldData($name);
            $fieldsData['value'] = $value;
            $self->_saveFieldData($fieldsData);
        }
    }

    private function _saveFieldData($data) {
        $serialized = serialize($data);
        COption::SetOptionString(self::MODULE_NAME, $data['name'], $serialized);
    }

    private function _getFieldData($name) {
        $serialized = COption::GetOptionString(self::MODULE_NAME, $name);
        $fieldData = unserialize($serialized);
        return $this->_fieldsData[$name] = $fieldData;

    }

    private function __construct() {
        $fields = COption::GetOptionString(self::MODULE_NAME, 'list');
        if ($fields) {
            $this->_fieldsList = explode(',', $fields);
        }
    }

    /**
     * @return WS_PSettings
     */
    private static function _getInstance() {
        if (!self::$_self) {
            self::$_self = new self();
        }
        return self::$_self;
    }

    /**
     * @param str $name
     * @return bool
     */
    private function _hasField($name) {
        return in_array($name, $this->_fieldsList);
    }

    /**
     * @param str $name
     * @return WS_ProjectSettings_Field
     */
    private function _getField($name) {
        if (!$this->_fields[$name]) {
            $this->_fields[$name] = new WS_ProjectSettings_Field($this->_getFieldData($name));
        }
        return $this->_fields[$name];
    }
}
