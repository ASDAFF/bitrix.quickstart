<?php
/**
 * WebForms helper
 *
 */

namespace Ml2WebForms;

use \Bitrix\Main\Application;

/**
 * Class WebForm
 * @package Ml2WebForms
 */
abstract class WebForm {
    /**
     * fields types
     */
    const FIELD_TYPE_TEXT = 0;
    const FIELD_TYPE_SELECT = 1;
    const FIELD_TYPE_TEXTAREA = 2;
    const FIELD_TYPE_RADIO = 3;
    const FIELD_TYPE_HIDDEN = 4;
    const FIELD_TYPE_CHECKBOX = 5;
    const FIELD_TYPE_SELECT_MULTIPLE = 6;
    const FIELD_TYPE_FILE = 7;

    /**
     * fields values types
     */
    const FIELD_VALUE_TYPE_TEXT = 0;
    const FIELD_VALUE_TYPE_STRING = 1;
    const FIELD_VALUE_TYPE_INTEGER = 2;
    const FIELD_VALUE_TYPE_REAL = 3;
    const FIELD_VALUE_TYPE_DATE = 4;
    const FIELD_VALUE_TYPE_DATETIME = 5;

    /**
     * validators
     */
    const VALIDATOR_REGEXP = 1;
    const VALIDATOR_EMAIL = 2;
    const VALIDATOR_INTEGER = 3;
    const VALIDATOR_FLOAT = 4;
    const VALIDATOR_SIZE = 5;
    const VALIDATOR_LENGTH = 6;
    const VALIDATOR_CUSTOM = 7;

    /**
     * @var array request data valudate result
     */
    protected $requestStatus = array();

    /**
     * @var array request fields for saving
     */
    protected $requestFields = array();

    /**
     * @var array errors default text
     */
    protected $errorText = array(
        'required_empty' => array(
            'ru' => 'Поле обязательно для заполнения',
            'en' => 'Field is required',
        ),
        'invalid_regexp' => array(
            'ru' => 'Поле заполнено неверно',
            'en' => 'Incorrect field value',
        ),
        'invalid_email' => array(
            'ru' => 'Поле заполнено неверно',
            'en' => 'Incorrect field value',
        ),
        'invalid_integer' => array(
            'ru' => 'Значение поля должно быть целым числом',
            'en' => 'Field must be integer',
        ),
        'invalid_float' => array(
            'ru' => 'Значение поля должно быть числом',
            'en' => 'Field must be real',
        ),
        'invalid_size_min' => array(
            'ru' => 'Значение поля должно быть не менее {min}',
            'en' => 'Field must be more or equal than {min}',
        ),
        'invalid_size_max' => array(
            'ru' => 'Значение поля должно быть не более {max}',
            'en' => 'Field must be less or equal than {max}',
        ),
        'invalid_length_min' => array(
            'ru' => 'Длина значения поля должна быть не менее {min}',
            'en' => 'Field length must be more or equal than {min}',
        ),
        'invalid_length_max' => array(
            'ru' => 'Длина значения поля должна быть не более {max}',
            'en' => 'Field length must be less or equal than {max}',
        ),
        'invalid_custom' => array(
            'ru' => 'Поле заполнено неверно',
            'en' => 'Incorrect field value',
        ),
    );

    /**
     * returns webform_id
     * @return string
     */
    abstract public function getId();

    public $fieldsVariantsLists = array();

    protected $cacheTime = 3600;

    /**
     * WebForm constructor
     * @throws \Exception
     */
    public function __construct() {
        $oCache = new \CPHPCache();

        if ($oCache->InitCache($this->cacheTime, 'ml2webforms_fields_lists_' . $this->getId() . "_" . LANGUAGE_ID, '/ml2webforms/')) {
            $vars = $oCache->GetVars();
            $this->fieldsVariantsLists = $vars['fieldsVariantsLists'];
        } elseif ($oCache->StartDataCache()) {
            $fields = &$this->getFields();
            foreach ($fields as $field => &$params) {
                if (in_array($params['type'], array(WebForm::FIELD_TYPE_SELECT, WebForm::FIELD_TYPE_SELECT_MULTIPLE, WebForm::FIELD_TYPE_RADIO))) {
                    if (isset($params['from'])) {
                        if (!isset($params['from']['table'])) {
                            throw new \Exception('Param "table" is required for "from" configuration of "' . $field . '" field');
                        }
                        if (!isset($params['from']['fields'])) {
                            throw new \Exception('Param "fields" is required for "from" configuration of "' . $field . '" field');
                        }
                        if (!isset($params['from']['fields']['id'])) {
                            throw new \Exception('Param "fields[\'id\']" is required for "from" configuration of "' . $field . '" field');
                        }
                        if (!isset($params['from']['fields']['title'])) {
                            throw new \Exception('Param "fields[\'title\']" is required for "from" configuration of "' . $field . '" field');
                        }
                        if (!isset($params['from']['fields']['title']['ru'])) {
                            throw new \Exception('Param "fields[\'title\'][\'ru\']" is required for "from" configuration of "' . $field . '" field');
                        }


                        $order = array();
                        if (isset($params['from']['fields']['title'][LANGUAGE_ID])) {
                            $order[] = $params['from']['fields']['title'][LANGUAGE_ID];
                        } else {
                            $order[] = $params['from']['fields']['title']['ru'];
                        }

                        $rsVariants = Application::getConnection()->query("
                            SELECT
                                `{$params['from']['fields']['id']}` as id,
                                `{$params['from']['fields']['title']['ru']}` as title_ru,
                                " . (isset($params['from']['fields']['title']['en']) ? "`{$params['from']['fields']['title']['en']}`" : "''") . " as title_en
                            FROM
                                `{$params['from']['table']}`
                            " . (strlen($params['from']['filter']) > 0 ? "WHERE " . $params['from']['filter'] : "") . "
                            ORDER BY
                                " . implode(',', $order) . "
                        ");

                        $list = array();
                        while ($row = $rsVariants->fetch()) {
                            $list[$row['id']] = array(
                                'title' => array(
                                    'ru' => $row['title_ru'],
                                    'en' => $row['title_en']
                                )
                            );
                        }

                        $this->fieldsVariantsLists[$field] = $list;
                    }

                    if (isset($params['iblock'])) {
                        \CModule::IncludeModule('iblock');
                        if (!isset($params['iblock']['id'])) {
                            throw new \Exception('Param "id" is required for "iblock" configuration of "' . $field . '" field');
                        }
                        if (!isset($params['iblock']['fields'])) {
                            throw new \Exception('Param "fields" is required for "iblock" configuration of "' . $field . '" field');
                        }
                        if (!isset($params['iblock']['fields']['title'])) {
                            throw new \Exception('Param "fields[\'title\']" is required for "iblock" configuration of "' . $field . '" field');
                        }
                        if (!isset($params['iblock']['fields']['title']['ru'])) {
                            throw new \Exception('Param "fields[\'title\'][\'ru\']" is required for "iblock" configuration of "' . $field . '" field');
                        }

                        $order = array();
                        if (isset($params['iblock']['fields']['title'][LANGUAGE_ID])) {
                            $order[] = $params['iblock']['fields']['title'][LANGUAGE_ID];
                        } else {
                            $order[] = $params['iblock']['fields']['title']['ru'];
                        }

                        $filter = array('IBLOCK_ID' => $params['iblock']['id']);

                        if (isset($params['iblock']['filter']) && is_array($params['iblock']['filter'])) {
                            $filter = array_merge(
                                $filter,
                                $params['iblock']['filter']
                            );
                        }

                        $rsVariants = \CIBlockElement::GetList(
                            array(
                                $order[0] => 'asc'
                            ),
                            $filter,
                            false,
                            false,
                            array(
                                'ID',
                                $params['iblock']['fields']['title']['ru'],
                                isset($params['iblock']['fields']['title']['en']) ? $params['iblock']['fields']['title']['en'] : $params['iblock']['fields']['title']['ru']
                            )
                        );

                        $list = array();
                        while ($row = $rsVariants->Fetch()) {
                            $list[$row['ID']] = array(
                                'title' => array(
                                    'ru' => $row[$params['iblock']['fields']['title']['ru'] . (strpos($params['iblock']['fields']['title']['ru'], 'PROPERTY_') === 0 ? '_VALUE' : '')],
                                    'en' => @$row[$params['iblock']['fields']['title']['en'] . (strpos($params['iblock']['fields']['title']['en'], 'PROPERTY_') === 0 ? '_VALUE' : '')],
                                )
                            );
                        }

                        $this->fieldsVariantsLists[$field] = $list;
                    }
                }
            }

            $oCache->EndDataCache(array('fieldsVariantsLists' => $this->fieldsVariantsLists));
        }
    }

    /**
     * Use ML2WEBFORMSAntispan module for detecting spam-bots
     */
    protected function useMl2WebFormsAntispam() {
        return false;
    }

    /**
     * Returns post event id
     */
    protected function getPostEventId() {
        return '';
    }

    /**
     * Returns post event templates ids
     */
    protected function getPostEventTemplates() {
        return array();
    }

    /**
     * Returns webform fields
     * @return array
     */
    public function getFields() {
        return array();
    }

    /**
     * return true if spam detected
     */
    public function spamDetected() {
        return $this->useMl2WebFormsAntispam() && defined('ML2WEBFORMSANTISPAM_SPAM_DETECTED') && ML2WEBFORMSANTISPAM_SPAM_DETECTED === true;
    }

    /**
     * Form fill error
     * @return string
     */
    public function getErrorMessage() {
        return array(
            'ru' => 'Ошибка заполнения формы',
            'en' => 'Error form fill',
        );
    }

    /**
     * Form fill success
     * @return string
     */
    public function getSuccessMessage() {
        return array(
            'ru' => 'Спасибо, Ваша заявка принята!',
            'en' => 'Your request has been sent successfully',
        );
    }

    /**
     * Form result to DB error text
     * @return string
     */
    public function getDBErrorMessage() {
        return array(
            'ru' => 'Невозможно добавить результаты запроса в БД!',
            'en' => 'Unable to add request to DB',
        );
    }

    /**
     * Host of external database
     * @var string
     */
    protected $extDbHost;
    /**
     * DB name of external database
     * @var string
     */
    protected $extDbName;
    /**
     * User of external database
     * @var string
     */
    protected $extDbUser;
    /**
     * Password of external database
     * @var string
     */
    protected $extDbPassword;
    /**
     * External database connection
     * @var resource
     */
    protected $extDbCon = false;

    /**
     * Use external database for form results
     * @return bool
     */
    public function useExternalDB() {
        return false;
    }

    /**
     * Returns external database connection if specified
     * @return resource|bool
     * @throws \Exception
     */
    public function getExtDBCon() {
        if ($this->useExternalDB() && $this->extDbCon === false) {
            $this->extDbCon = mysql_connect($this->extDbHost, $this->extDbUser, $this->extDbPassword);
            if ($this->extDbCon) {
                mysql_select_db($this->extDbName, $this->extDbCon);
            } else {
                throw new \Exception('External database connect failure: ' . mysql_error());
            }
        }

        return $this->extDbCon;
    }

    /**
     * Process form request
     */
    public function validateRequest() {
        $messages = $this->getSuccessMessage();
        $this->requestStatus = array(
            'status' => 'success',
            'message' => $messages[LANGUAGE_ID],
        );

        if ($this->spamDetected()) {
            return;
        }

        $fields = $this->getFields();

        foreach ($fields as $field => $params) {
            if ($this->validateField($field, $params)) {
                $this->requestFields[$field] = $_REQUEST[$field];
            }
        }
    }

    /**
     * @return array returns request validation result
     */
    public function getProcessResult() {
        return $this->requestStatus;
    }

    /**
     * Add result to IBlock
     * @return bool
     * @throws \Exception
     */
    public function addResult() {
        $fields = array(
            'datetime' => date('Y-m-d H:i:s'),
        );
        $fieldCfg = $this->getFields();
        foreach ($this->requestFields as $field => $value) {
            if ($fieldCfg[$field]['type'] == WebForm::FIELD_TYPE_FILE) {
				if (isset ($_FILES[$field]['tmp_name']) && $_FILES[$field]['tmp_name']) {
					$destDir = '/upload/ml2webforms/' . date('YmdHis') . rand(100000, 999999);
					mkdir($_SERVER['DOCUMENT_ROOT'] . $destDir);
					move_uploaded_file($_FILES[$field]['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $destDir . '/' . $_FILES[$field]['name']);
					$fields[$field] = $destDir . '/' . $_FILES[$field]['name'];
					$this->requestFields[$field] = $fields[$field];
				} else {
					$fields[$field] = "";
				}
            } /*else if (isset($fieldCfg[$field]['list']) && is_array($fieldCfg[$field]['list']) && in_array($fieldCfg[$field]['type'], array(WebForm::FIELD_TYPE_SELECT, WebForm::FIELD_TYPE_SELECT_MULTIPLE, WebForm::FIELD_TYPE_RADIO))) {
                if (!is_array($value)) {
                    $value = array($value);
                }
                $fields[$field] = array();
                foreach ($value as $val) {
                    $fields[$field] = $fieldCfg[$field]['list'][$val]['title'][LANGUAGE_ID];
                }
                $fields[$field] = implode(', ', $fields[$field]);
            }*/ else {
                switch ($fieldCfg[$field]['type']) {
                    case WebForm::FIELD_TYPE_SELECT_MULTIPLE:
                        $fields[$field] = count($value) > 0 ? '|' . implode('|', $value) . '|' : '';
                        break;
                    default:
                        switch ($fieldCfg[$field]['value_type']) {
                            case WebForm::FIELD_VALUE_TYPE_INTEGER:
                                $fields[$field] = (int)$value;
                                break;
                            case WebForm::FIELD_VALUE_TYPE_REAL:
                                $fields[$field] = (float)$value;
                                break;
                            case WebForm::FIELD_VALUE_TYPE_DATE:
                            case WebForm::FIELD_VALUE_TYPE_DATETIME:
                            case WebForm::FIELD_VALUE_TYPE_STRING:
                            case WebForm::FIELD_VALUE_TYPE_TEXT:
                            default:
                                $fields[$field] = $value;
                                break;
                        }
                        break;
                }
            }
        }

        $result = new WebFormResult($this->getId(), $this->getExtDBCon());

        try {
            $fields = $this->onBeforeResultAdd($fields);

            if ($resultId = $result->add($fields)) {
                $fields['id'] = $resultId;
                $this->onAfterResultAdd($fields);
                $this->requestFields['id'] = $fields['id'];
                $this->requestFields['datetime'] = $fields['datetime'];

                $messages = $this->getSuccessMessage();
                $this->requestStatus = array(
                    'status' => 'success',
                    'message' => $messages[LANGUAGE_ID]
                );
            } else {
                $messages = $this->getDBErrorMessage();
                $this->requestStatus = array(
                    'status' => 'failure',
                    'message' => $messages[LANGUAGE_ID]
                );

                return false;
            }
        } catch (\Exception $e) {
            $this->requestStatus = array(
                'status' => 'failure',
                'message' => $e->getMessage(),
            );

            return false;
        }

        return true;
    }

    public function sendPostEvents() {
        if (strlen($this->getPostEventId()) > 0 && count($this->getPostEventTemplates()) > 0) {
            $form_event = $this->getPostEventId();
            $form_tpl = $this->getPostEventTemplates();
            $form_fields = array();
            $fieldCfg = $this->getFields();
            foreach ($this->requestFields as $field => $value) {
                if ($fieldCfg[$field]['type'] == WebForm::FIELD_TYPE_FILE) {
					if ($value) {
	                    $form_fields[strtoupper($field)] = "http" . ($_SERVER["SERVER_PORT"] == "443"?"s":"") . '://' . $_SERVER['HTTP_HOST'] . '' . $value;
					} else {
						$form_fields[strtoupper($field)] = "";
					}
                } else if (isset($fieldCfg[$field]['list']) && is_array($fieldCfg[$field]['list']) && in_array($fieldCfg[$field]['type'], array(WebForm::FIELD_TYPE_SELECT, WebForm::FIELD_TYPE_SELECT_MULTIPLE, WebForm::FIELD_TYPE_RADIO))) {
                    if (!is_array($value)) {
                        $value = array($value);
                    }
                    $form_fields[strtoupper($field)] = array();
                    $form_fields[strtoupper($field) . '_EN'] = array();
                    foreach ($value as $val) {
                        $form_fields[strtoupper($field)][] = $fieldCfg[$field]['list'][$val]['title']['ru'];
                        $form_fields[strtoupper($field) . '_EN'][] = $fieldCfg[$field]['list'][$val]['title']['en'];
                    }
                    $form_fields[strtoupper($field)] = implode(', ', $form_fields[strtoupper($field)]);
                    $form_fields[strtoupper($field) . '_EN'] = implode(', ', $form_fields[strtoupper($field) . '_EN']);
                } else {
                    $form_fields[strtoupper($field)] = htmlspecialchars($value);
                }
            }
            $oEvent = new \CEvent();
            foreach ($form_tpl as $tpl_id) {
                $result = $oEvent->Send(
                    $form_event,    // идентификатор типа почтового события
                    SITE_ID,        // идентификатор сайта
                    $form_fields,   // поля - макросы, которые будут доступны в шаблоне письма
                    "N",            // нужно ли дублировать письмо на ящик, заданный в настройках битрикс
                    $tpl_id         // идентификатор почтового шаблона
                );
            }

            $oEvent->CheckEvents();
        }
    }

    /**
     * Validate request field value
     * @param string $field Form field name
     * @param array $params Field params
     * @return bool validation result
     */
    protected function validateField($field, $params) {
        $result = true;

        if ($result) {
            if (isset($params['required'])) {
                if (is_object($params['required'])) {
                    if ($params['required']() == true) {
                        $result = false;
                        $this->addError($field, $params, 'required_empty');
                    }
                } else if ($params['required']) {
                    if ($params['type'] == WebForm::FIELD_TYPE_FILE) {
                        if ($_FILES[$field]['error']) {
                            $result = false;
                            $this->addError($field, $params, 'required_empty');
                        }
                    } else if (!(isset($_REQUEST[$field]) && strlen($_REQUEST[$field]) > 0)) {
                        $result = false;
                        $this->addError($field, $params, 'required_empty');
                    }
                }
            }
        }

        if ($result && isset($params['validators']) && is_array($params['validators'])) {
            foreach ($params['validators'] as $validator) {
                switch ($validator[0]) {
                    case WebForm::VALIDATOR_REGEXP:
                        preg_match('/' . $validator[1] . '/is', $_REQUEST[$field], $matches);
                        if (strlen($matches[0]) == 0) {
                            $result = false;
                            $this->addError($field, $params, 'invalid_regexp');
                        }
                        break;
                    case WebForm::VALIDATOR_EMAIL:
                        if (!filter_var($_REQUEST[$field], FILTER_VALIDATE_EMAIL)) {
                            $result = false;
                            $this->addError($field, $params, 'invalid_email');
                        }
                        break;
                    case WebForm::VALIDATOR_INTEGER:
                        if ((string)(int)$_REQUEST[$field] != (string)$_REQUEST[$field]) {
                            $result = false;
                            $this->addError($field, $params, 'invalid_integer');
                        }
                        break;
                    case WebForm::VALIDATOR_FLOAT:
                        if ((string)(double)$_REQUEST[$field] != (string)$_REQUEST[$field]) {
                            $result = false;
                            $this->addError($field, $params, 'invalid_float');
                        }
                        break;
                    case WebForm::VALIDATOR_SIZE:
                        if (isset($validator[1]['min']) && (float)$_REQUEST[$field] < (float)$validator[1]['min']) {
                            $result = false;
                            $this->addError($field, $params, 'invalid_size_min', array('min' => $validator[1]['min']));
                        }
                        if (isset($validator[1]['max']) && (float)$_REQUEST[$field] > (float)$validator[1]['max']) {
                            $result = false;
                            $this->addError($field, $params, 'invalid_size_max', array('max' => $validator[1]['max']));
                        }
                        break;
                    case WebForm::VALIDATOR_LENGTH:
                        if (isset($validator[1]['min']) && strlen($_REQUEST[$field]) < (int)$validator[1]['min']) {
                            $result = false;
                            $this->addError($field, $params, 'invalid_length_min', array('min' => $validator[1]['min']));
                        }
                        if (isset($validator[1]['max']) && strlen($_REQUEST[$field]) > (int)$validator[1]['max']) {
                            $result = false;
                            $this->addError($field, $params, 'invalid_length_max', array('max' => $validator[1]['max']));
                        }
                        break;
                    case WebForm::VALIDATOR_CUSTOM:
                        if (!$validator[1]($_REQUEST[$field])) {
                            $result = false;
                            $this->addError($field, $params, 'invalid_custom');
                        }
                        break;
                }
            }
        }

        return $result;
    }

    /**
     * Add validation error to request validation result
     * @param string $field From field name
     * @param array $params Field params
     * @param string $errorCode error code
     * @param array $tplKeys values for put in error message
     */
    protected function addError($field, $params, $errorCode, $tplKeys = array()) {
        if (!is_array($this->requestStatus)) {
            $this->requestStatus = array();
        }

        $this->requestStatus['status'] = 'failure';
        $messages = $this->getErrorMessage();
        $this->requestStatus['message'] = $messages[LANGUAGE_ID];
        if (!isset($this->requestStatus['errors'])) {
            $this->requestStatus['errors'] = array();
        }

        if (!isset($this->requestStatus['errors'][$field])) {
            $this->requestStatus['errors'][$field] = array();
        }

        $errorText = isset($params['errorText'][$errorCode][LANGUAGE_ID]) ?
            $params['errorText'][$errorCode][LANGUAGE_ID] : (
                $this->errorText[$errorCode][LANGUAGE_ID] ? $this->errorText[$errorCode][LANGUAGE_ID] : ''
            );
        foreach ($tplKeys as $key => $value) {
            $errorText = str_replace('{' . $key . '}', $value, $errorText);
        }
        $this->requestStatus['errors'][$field][] = array(
            $errorCode,
            $errorText
        );
    }

    /**
     * Event before form result add. Returns modified fields
     * @param array $fields fields for add
     * @return array
     * @throws \Exception
     */
    public function onBeforeResultAdd(array $fields) {
        // if some error stop form result save:
        // throw new \Exception('Exception message...');
        return $fields;
    }

    /**
     * Event after form result add
     * @param array $fields added fields
     */
    public function onAfterResultAdd(array $fields) {
    }
}
