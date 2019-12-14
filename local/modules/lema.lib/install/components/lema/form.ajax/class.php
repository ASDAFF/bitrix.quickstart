<?php

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
Loc::loadMessages(__DIR__ . '/template.php');

CJSCore::init(array('fx'));

class FormAjaxComponent extends \CBitrixComponent
{
    protected $fields = array();

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    public function executeComponent()
    {
        $this->fields = json_decode(htmlspecialcharsback($this->arParams['FORM_FIELDS']), true);

        $this->includeComponentTemplate();
    }

    public function getType($field)
    {
        return isset($field['type']) ? $field['type'] : 'text';
    }
    public function getFieldType($field)
    {
        return ' type="' . $this->getType($field) . '"';
    }
    public function getFieldId($field)
    {
        return  ' id="form_field_' . $field['name'] . '"';
    }
    public function getFieldName($field)
    {
        return  ' name="' . $field['name'] . '"';
    }
    public function getFieldPlaceholder($field)
    {
        return  ' placeholder="' . $field['placeholder'] . '"';
    }
    public function getInput($field, $additionalParams = null)
    {
        $additional = $this->getParamsAsString($additionalParams);

        return sprintf(
            '<input %s %s %s %s %s %s>',
            $this->getRequired($field),
            $this->getFieldType($field),
            $this->getFieldId($field),
            $this->getFieldName($field),
            $this->getFieldPlaceholder($field),
            $additional
        );
    }

    public function getLabel($field, $additionalParams = null)
    {
        $additional = $this->getParamsAsString($additionalParams);

        return
            '<label for="form_field_' . $field['name'] . '"' . $additional . '>' .
                $this->getFieldTitle($field) . ': ' .
                $this->getRequired($field, '<small style="color:red">*</small>') .
            '</label>';
    }

    public function getInputFull($field, $inputParams = null, $labelParams = null, $withWrapper = true)
    {
        $ret = '';
        if($withWrapper)
            $ret .= '<div class="it-block form-group">';
        $ret .= $this->getLabel($field, $labelParams) . PHP_EOL . $this->getInput($field, $inputParams) . PHP_EOL;
        if($withWrapper)
            $ret .= '<div class="it-error"></div>' . PHP_EOL . '</div>' . PHP_EOL;
        return $ret;
    }

    protected function getParamsAsString($additionalParams = null)
    {
        $additional = null;
        if(isset($additionalParams))
        {
            if(is_array($additionalParams))
            {
                foreach($additionalParams as $k => $v)
                    $additional .= ' ' . $k . '="' . $v . '"';
            }
            else
                $additional = ' ' . $additionalParams;
        }
        return $additional;
    }

    public function getFieldTitle($field)
    {
        $title = Loc::getMessage('AJAX_FORM_' . $field['name']);
        return empty($title) ? $field['title'] : $title;
    }

    public function isRequired($field)
    {
        return $field['required'] == 'Y';
    }
    public function getRequired($field, $symbols = ' required')
    {
        return $this->isRequired($field) ? $symbols : null;
    }
    public function getFormClass()
    {
        return empty($this->arParams['FORM_CLASS']) ? null : ' class="' . $this->arParams['FORM_CLASS'] . '"';
    }
    public function getFormAction()
    {
        $action = empty($this->arParams['FORM_ACTION']) ? $this->getPath() . '/ajax.php' : $this->arParams['FORM_ACTION'];
        return ' action="' . $action . '"';
    }
    public function getBtnTitle()
    {
        $title = empty($this->arParams['FORM_BTN_TITLE']) ? 'SEND' : $this->arParams['FORM_BTN_TITLE'];
        $translated = Loc::getMessage($title);
        return empty($translated) ? $title : $translated;
    }
    public function get152FZ()
    {
        return isset($this->arParams['FORM_152_FZ']) ? htmlspecialcharsback($this->arParams['FORM_152_FZ']) : null;
    }
}