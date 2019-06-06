<?php
/**
 * WebForms helper
 *
 */

namespace Ml2WebForms;
use Bitrix\Main\Page\Asset;

/**
 * Class WebFormTemplate
 * @package Ml2WebForms
 */
class WebFormTemplate {
    /**
     * @var WebForm
     */
    protected $webForm;

    /**
     * Web form action attribute
     * @var string
     */
    protected $formAction;

    /**
     * WebFrom fields
     * @var array
     */
    protected $fields;

    /**
     * Object constructor
     * @param WebForm $webForm
     */
    public function __construct(WebForm $webForm) {
        $this->webForm = $webForm;
        $this->formAction = '/bitrix/tools/ml2webforms_controller.php';
        $this->fields = &$this->getWebForm()->getFields();
    }

    /**
     * Returns tpl web form
     * @return WebForm
     */
    public function getWebForm() {
        return $this->webForm;
    }

    /**
     * Returns form "action" attribute
     * @return string
     */
    public function getFormAction() {
        return $this->formAction;
    }

    /**
     * Appends to page form's js
     */
    public function getTplWebFormId() {
        return '<input type="hidden" name="webform_id" value="' . $this->getWebForm()->getId() . '">';
    }

    /**
     * Returns form default javascript
     */
    /*public function includeJs() {
        Asset::getInstance()->addString('<script type="text/javascript">' . @file_get_contents(__DIR__ . '/forms/' . $this->getWebForm()->getId() . '/script.js') . '</script>');
    }*/

    /**
     * Appends to page form's css
     */
    /*public function includeCss() {
        Asset::getInstance()->addString('<style type="text/css">' . @file_get_contents(__DIR__ . '/forms/' . $this->getWebForm()->getId() . '/style.css') . '</style>');
    }*/

    /**
     * Returns form target name
     * @return string
     */
    public function getFormTarget() {
        return $this->getFormName() . '_frame';
    }

    /**
     * Returns form name
     * @return string
     */
    public function getFormName() {
        return 'ml2webforms_' . $this->getWebForm()->getId();
    }

    /**
     * Returns form target iframe
     * @return string
     */
    public function getFormTargetIFrame() {
        return '<iframe style="display:none" id="' . $this->getFormTarget() . '" name="' . $this->getFormTarget() . '"></iframe>';
    }

    /**
     * Returns web form template
     */
    /*public function getTemplate() {
        $this->includeCss();
        $this->includeJs();

        require __DIR__ . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . $this->getWebForm()->getId() . DIRECTORY_SEPARATOR . 'template.php';
    }*/

    /**
     * Returns form begin html tag and form default script and css
     * @param array $htmlOptions <form> html element attributes
     * @return string
     */
    public function getFormBegin($htmlOptions = array('class' => 'ml2webforms_default')) {
        $htmlOptionsStr = '';
        foreach ($htmlOptions as $attr => $value) {
            if (in_array($attr, array('name', 'id', 'method', 'action', 'target', 'enctype'))) {
                continue;
            }

            $htmlOptionsStr .= ' ' . $attr . '="' . $value . '"';
        }

        $tpl = '';
        $tpl .= $this->getFormTargetIFrame();
        $tpl .= '<form name="' . $this->getFormName() . '" id="' . $this->getFormName() . '" method="post" action="' . $this->getFormAction() . '" target="' . $this->getFormTarget() . '" enctype="multipart/form-data"' . $htmlOptionsStr . '>';
        $tpl .= $this->getTplWebFormId();

        return $tpl;
    }

    /**
     * Returns form close tag
     * @return string
     */
    public function getFormEnd() {
        return '</form>';
    }
}
