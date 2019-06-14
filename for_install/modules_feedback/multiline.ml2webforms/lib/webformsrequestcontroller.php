<?php

namespace Ml2WebForms;

/**
 * Class WebFormsRequestController
 * @package Ml2WebForms
 */
class WebFormsRequestController {
    const ERROR_NO_POST_REQUEST = 'This is not a post request';
    const ERROR_WEBFORM_DOES_NOT_EXIST = 'Web form does not exist';
    const ERROR_WEBFORM_ID_IS_EMPTY = 'Web form id is empty';

    /**
     * @var WebForm object instance
     */
    protected $webForm;

    /**
     * @var string document root
     */
    protected $DOC_ROOT = '';

    /**
     * Web forms controller constructor
     * @param string $webFormId
     * @throws \Exception
     */
    public function __construct($webFormId) {
        $this->DOC_ROOT = $_SERVER['DOCUMENT_ROOT'];

        $webFormId = trim($webFormId);

        if (strlen($webFormId) == 0) {
            throw new \Exception(self::ERROR_WEBFORM_ID_IS_EMPTY);
        }

        if (file_exists($this->DOC_ROOT . '/local/modules/multiline.ml2webforms/lib/forms/' . $webFormId . '/class.php')) {
			require_once $this->DOC_ROOT . '/local/modules/multiline.ml2webforms/lib/forms/' . $webFormId . '/class.php';
        } elseif (file_exists($this->DOC_ROOT . '/bitrix/modules/multiline.ml2webforms/lib/forms/' . $webFormId . '/class.php')) {
			require_once $this->DOC_ROOT . '/bitrix/modules/multiline.ml2webforms/lib/forms/' . $webFormId . '/class.php';
        } else {
            throw new \Exception(self::ERROR_WEBFORM_DOES_NOT_EXIST . "\r\n" . $webFormId);
        }

        $webFormClassName = '\\Ml2WebForms\\' . ucfirst($webFormId) . 'WebForm';

        if (!class_exists($webFormClassName)) {
            throw new \Exception(self::ERROR_WEBFORM_DOES_NOT_EXIST . "\r\n" . $webFormId . "\r\n" . $webFormClassName);
        }

        $this->webForm = new $webFormClassName();
    }

    /**
     * Process web form post request
     * @throws \Exception
     */
    public function processForm() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            throw new \Exception(self::ERROR_NO_POST_REQUEST);
        }

        $this->webForm->validateRequest();

        $validateResult = $this->getProcessResult();
        if (isset($validateResult['status']) && $validateResult['status'] === 'success') {
            if (!$this->webForm->spamDetected()) {
                $this->webForm->addResult();
                $this->webForm->sendPostEvents();
            }
        }
    }

    /**
     * Return process result array
     * @return array
     */
    public function getProcessResult() {
        return $this->webForm->getProcessResult();
    }

    /**
     * Return process result json
     * @return string
     */
    public function getProcessResultJson() {
        return json_encode($this->getProcessResult());
    }

    /**
     * Output process result json
     */
    public function outputProcessResultJson() {
        echo json_encode($this->getProcessResult());
    }

    /**
     * Output process result script
     */
    public function outputProcessResultScript() {
        echo '<script type="text/javascript">top.Ml2WebForms_' . $this->webForm->getId() . '.showResult(' . $this->getProcessResultJson() . ');</script>';
    }

    /**
     * Return web form template object
     * @return string
     */
    public function getFormTemplate() {
        $tpl = new WebFormTemplate($this->webForm);
        return $tpl;
    }

    /**
     * Return web form object
     * @return WebForm
     */
    public function getWebForm() {
        return $this->webForm;
    }
}
