<?php
/**
 * Class descriptor field content
 * 
 * @author Максим Соколовский <my.sokolovsky@gmail.com>
 */
class WS_ProjectSettings_Field {

    private $_data;

    public function __construct($data) {
        $this->_data = $data;
    }

    public function getType() {
        return $this->_data['type'];
    }

    public function getValue() {
        return $this->_data['value'];
    }

    public function getDefault() {
        return $this->_data['default'];
    }

    public function getVariants() {
        return $this->_data['variants'];
    }

    public function isMany() {
        return (bool) $this->_data['isMany'];
    }

    public function getName() {
        return $this->_data['name'];
    }

    public function getLabel() {
        return $this->_data['label'];
    }

    public function getSort() {
        return $this->_data['sort'];
    }
}
