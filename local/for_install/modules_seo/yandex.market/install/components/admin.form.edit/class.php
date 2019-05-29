<?php

namespace Yandex\Market\Components;

use Bitrix\Main;
use Yandex\Market;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class AdminFormEdit extends \CBitrixComponent
{
    protected static $langPrefix = 'YANDER_MARKET_FORM_EDIT_';

    /** @var \Yandex\Market\Component\Base\EditForm */
    protected $provider;

    public function onPrepareComponentParams($params)
    {
        $params['FORM_ID'] = trim($params['FORM_ID']);
        $params['TITLE'] = trim($params['TITLE']);
        $params['TITLE_ADD'] = trim($params['TITLE_ADD']);
        $params['BTN_SAVE'] = trim($params['BTN_SAVE']);
        $params['LIST_URL'] = trim($params['LIST_URL']);
        $params['SAVE_URL'] = trim($params['SAVE_URL']);
        $params['CONTEXT_MENU'] = (array)$params['CONTEXT_MENU'];
        $params['TABS'] = (array)$params['TABS'];
        $params['FORM_BEHAVIOR'] = ($params['FORM_BEHAVIOR'] === 'steps' ? 'steps' : 'tabs');
        $params['COPY'] = (bool)$params['COPY'];

        if (empty($params['TABS']))
        {
            $params['TABS'] = [
            	[ 'name' => $this->getLang('DEFAULT_TAB_NAME') ]
            ];
        }

        $params['PROVIDER_TYPE'] = trim($params['PROVIDER_TYPE']);

        $provider = $this->getProvider($params['PROVIDER_TYPE']);

        $params = $provider->prepareComponentParams($params);

        return $params;
    }

    public function executeComponent()
    {
        $this->initResult();

        if (!$this->checkParams() || !$this->loadModules())
        {
            $this->showErrors();
            return;
        }

        $hasCriticalError = false;
        $isStepsBehavior = ($this->arParams['FORM_BEHAVIOR'] === 'steps');

        try
        {
            if ($this->hasCancelRequest())
            {
                $this->redirectCancel();
            }

	        $this->loadItem();
	        $this->buildContextMenu();
	        $this->buildTabs();

	        $requestStep = $this->getRequestStep();
	        $hasRequest = $this->hasRequest();
	        $hasSaveRequest = $this->hasSaveRequest();
	        $isFoundRequestStep = false;

            foreach ($this->arResult['TABS'] as &$tab)
            {
            	$tabFields = $this->loadFields($tab['SELECT']);
            	$stepValidateResult = true;

            	$this->registerTabFields($tab, $tabFields);

            	if ($hasRequest)
	            {
	                $this->fillRequest($tabFields);

	                if (
	                    $isStepsBehavior
	                    && (
	                        $hasSaveRequest // validate all on save
	                        || (!$isFoundRequestStep && $requestStep !== $tab['STEP']) // validate previous steps on move
                        )
                    )
	                {
		                $stepValidateResult = $this->validateRequest($tabFields);
			        }
		        }

		        if ($isStepsBehavior && !$isFoundRequestStep)
	            {
	            	$this->arResult['STEP'] = $tab['STEP'];
	            	$this->arResult['STEP_FINAL'] = $tab['FINAL'];

	                if (!$stepValidateResult || $requestStep === $tab['STEP'])
	                {
	                    $isFoundRequestStep = true;
	                }
		        }
            }
            unset($tab);

            if (!$isStepsBehavior && $hasRequest)
            {
                $this->validateRequest();
            }

			if ($this->hasAjaxAction())
			{
				$this->processAjaxAction();
			}
			else if ($this->hasErrors())
			{
				// nothing
			}
            else if ($hasSaveRequest)
            {
	            if (!check_bitrix_sessid())
	            {
	                $this->addError($this->getLang('EXPIRE_SESSION'));
	            }
	            else
	            {
	                $savePrimary = $this->save();

	                if ($savePrimary !== null)
	                {
	                    $this->redirectAfterSave($savePrimary);
	                }
                }
            }

            $this->extendItem();
        }
        catch (Main\SystemException $exception)
        {
            $hasCriticalError = true;
            $this->addError($exception->getMessage());
        }

        $this->setTitle();

        if ($hasCriticalError)
        {
            $this->showErrors();
        }
        else
        {
            $this->includeComponentTemplate();
        }
    }

    protected function initResult()
    {
        $this->arResult['STEP'] = null;
        $this->arResult['STEP_FINAL'] = false;
        $this->arResult['FIELDS'] = [];
        $this->arResult['ITEM'] = [];
        $this->arResult['ERRORS'] = [];
        $this->arResult['FIELD_ERRORS'] = [];
        $this->arResult['TABS'] = [];
        $this->arResult['HAS_REQUEST'] = false;
    }

    protected function getRequiredParams()
    {
        $provider = $this->getProvider();
        $result = [ 'FORM_ID' ] + $provider->getRequiredParams();

        return $result;
    }

    protected function checkParams()
    {
        $result = true;
        $requiredParams = $this->getRequiredParams();

        foreach ($requiredParams as $paramKey)
        {
            if (empty($this->arParams[ $paramKey ]))
            {
                $result = false;

                $this->addError($this->getLang('PARAM_REQUIRE', [
                    '#PARAM#' => $paramKey
                ]));
            }
        }

        return $result;
    }

    protected function getRequiredModules()
    {
        $provider = $this->getProvider();

        return $provider->getRequiredModules();
    }

    protected function loadModules()
    {
        $result = true;
        $modules = $this->getRequiredModules();

        foreach ($modules as $module)
        {
            if (!$this->loadModule($module))
            {
                $result = false;
            }
        }

        return $result;
    }

    protected function loadModule($module)
    {
        $result = true;

        if (!Main\Loader::includeModule($module))
        {
            $result = false;

            $this->addError($this->getLang('MODULE_REQUIRE', [
                '#MODULE#' => $module
            ]));
        }

        return $result;
    }

    protected function addFieldError($fieldName, $message)
    {
        $this->arResult['FIELD_ERRORS'][$fieldName] = true;

        $this->addError($message);
    }

    protected function addError($message)
    {
        $this->arResult['ERRORS'][] = $message;
    }

    public function hasErrors()
    {
        return !empty($this->arResult['ERRORS']);
    }

    public function showErrors()
    {
        \CAdminMessage::ShowMessage([
            'TYPE' => 'ERROR',
            'MESSAGE' => implode('<br />', $this->arResult['ERRORS']),
            'HTML' => true
        ]);
    }

    protected function setTitle()
    {
        global $APPLICATION;

        $title = $this->arParams['TITLE'];

        if (empty($this->arParams['PRIMARY']) && $this->arParams['TITLE_ADD'] !== '')
        {
            $title = $this->arParams['TITLE_ADD'];
        }

        if ($title !== '')
        {
            $APPLICATION->SetTitle($title);
        }
    }

    protected function getFieldsSelect()
    {
        $result = [];

        foreach ($this->arParams['TABS'] as $tab)
        {
            if (!empty($tab['fields']))
            {
                foreach ($tab['fields'] as $field)
                {
                    $result[] = $field;
                }
            }
        }

        return $result;
    }

    protected function hasAjaxAction()
    {
        return ($this->getAjaxAction() !== null);
    }

    protected function getAjaxAction()
    {
        return $this->request->getPost('ajaxAction');
    }

    protected function processAjaxAction()
    {
        global $APPLICATION;

        $ajaxAction = $this->getAjaxAction();
        $provider = $this->getProvider();

        try
        {
            $response = $provider->processAjaxAction($ajaxAction, $this->arResult['ITEM']);
        }
        catch (Main\SystemException $exception)
        {
            $response = [
                'status' => 'error',
                'message' => $exception->getMessage()
            ];
        }

        $APPLICATION->RestartBuffer();
        echo Market\Utils::jsonEncode($response, JSON_UNESCAPED_UNICODE);
        die();
    }

    protected function hasRequest()
    {
        $result = $this->hasStepRequest() || $this->hasSaveRequest() || $this->hasAjaxAction();

        $this->arResult['HAS_REQUEST'] = $result;

        return $result;
    }

    protected function hasCancelRequest()
    {
        return ($this->request->getPost('cancel') !== null);
    }

    protected function hasStepRequest()
    {
        return ($this->request->getPost('stepAction') !== null);
    }

    protected function hasSaveRequest()
    {
        return ($this->request->getPost('apply') !== null || $this->request->getPost('save') !== null);
    }

    protected function getRequestStep()
    {
        $stepCount = \count($this->arResult['TABS']);
        $stepIndex = (int)$this->request->getPost('STEP');

        // step action

        $stepAction = $this->request->getPost('stepAction');

        switch (true)
        {
	        case ($stepAction === 'previous'):
	            $stepIndex -= 1;
	        break;

	        case ($stepAction === 'next'):
	            $stepIndex += 1;
	        break;

	        case (is_numeric($stepAction)):
                $stepIndex = (int)$stepAction;
	        break;
        }

        // normalize index

        if ($stepIndex <= 0)
        {
            $stepIndex = 0;
        }
        else if ($stepIndex >= $stepCount)
        {
            $stepIndex = $stepCount - 1;
        }

        return $stepIndex;
    }

    protected function fillRequest($fields)
    {
		$provider = $this->getProvider();

        foreach ($fields as $field)
        {
            $this->getValueByRequestKey($_POST, $field['FIELD_NAME'], $this->arResult['ITEM']);
        }

        $this->arResult['ITEM'] = $provider->modifyRequest($this->arResult['ITEM']);
    }

    protected function getValueByRequestKey($values, $key, &$result)
    {
        $keyChain = $this->splitFieldNameToChain($key);

        if (!empty($keyChain))
        {
            $valuesLevel = $values;
            $resultLevel = &$result;
            $keyChainLength = count($keyChain);

            for ($i = 0; $i < $keyChainLength; $i++)
            {
				$key = $keyChain[$i];
                $isLastKey = ($i === $keyChainLength - 1);

                if ($isLastKey)
                {
                    $resultLevel[$key] = isset($valuesLevel[$key]) ? $valuesLevel[$key] : null;
                }
                else
                {
                    if (!isset($resultLevel[$key]))
                    {
                        $resultLevel[$key] = [];
                    }

                    $resultLevel = &$resultLevel[$key];
                    $valuesLevel = isset($valuesLevel[$key]) ? $valuesLevel[$key] : null;
                }
            }
        }
    }

    protected function validateRequest($fields = null)
    {
        $provider = $this->getProvider();
        $validationResult = $provider->validate($this->arResult['ITEM'], $fields);
        $result = false;

        if ($validationResult->isSuccess())
        {
            $result = true;
        }
        else
        {
            $errors = $validationResult->getErrors();

            if (!empty($errors))
            {
                foreach ($errors as $error)
                {
                    $errorCustomData = $error->getCustomData();

                    if (isset($errorCustomData['FIELD']))
                    {
                    	$this->addFieldError($errorCustomData['FIELD'], $error->getMessage());
                    }
                    else
                    {
                        $this->addError($error->getMessage());
                    }
                }
            }
            else
            {
                $this->addError($this->getLang('VALIDATE_ERROR_UNDEFINED'));
            }
        }

        return $result;
    }

    protected function save()
    {
        $fields = $this->arResult['ITEM'];
        $provider = $this->getProvider();
        $primary = null;
        $saveResult = null;
        $result = null;

        if (!empty($this->arParams['PRIMARY']) && !$this->arParams['COPY'])
        {
            $primary = $this->arParams['PRIMARY'];
            $saveResult = $provider->update($primary, $fields);
        }
        else
        {
            $saveResult = $provider->add($fields);

            if ($saveResult->isSuccess())
            {
                $primary = $saveResult->getId();
            }
        }

        if ($saveResult->isSuccess())
        {
			$result = $primary;
        }
        else
        {
            $errors = $saveResult->getErrors();

            if (!empty($errors))
            {
                foreach ($errors as $error)
                {
                    $this->addError($error->getMessage());
                }
            }
            else
            {
                $this->addError($this->getLang('SAVE_ERROR_UNDEFINED'));
            }
        }

        return $result;
    }

    protected function redirectCancel()
    {
        LocalRedirect($this->arParams['LIST_URL']);
        die();
    }

    protected function redirectAfterSave($primary)
    {
        global $APPLICATION;

        $redirectUrl = null;

        if ($this->request->getPost('save'))
        {
            $redirectUrl = $this->arParams['SAVE_URL'] ?: $this->arParams['LIST_URL'];
            $redirectUrl = str_replace('#ID#', $primary, $redirectUrl);
        }
        else
        {
            $redirectUrl = $APPLICATION->GetCurPageParam('id=' . $primary, array('id'));

            if ($this->arParams['FORM_BEHAVIOR'] !== 'steps')
            {
	            // active tab

			    $activeTabRequestKey = $this->arParams['FORM_ID'] . '_active_tab';
		        $activeTab = $this->request->getPost($activeTabRequestKey);

		        if ($activeTab)
		        {
		            $redirectUrl .= (strpos($redirectUrl, '?') === false ? '?' : '&') . http_build_query([
		                $activeTabRequestKey => $activeTab
		            ]);
		        }
	        }
        }

        LocalRedirect($redirectUrl);
        die();
    }

    protected function loadItem()
    {
        $provider = $this->getProvider();

        if (!empty($this->arParams['PRIMARY']))
        {
            $fieldsSelect = $this->getFieldsSelect();

            $this->arResult['ITEM'] = $provider->load($this->arParams['PRIMARY'], $fieldsSelect, $this->arParams['COPY']);
        }
    }

    protected function loadFields($select)
    {
        $provider = $this->getProvider();
        $newFields = $provider->getFields((array)$select, $this->arResult['ITEM']);

        $this->arResult['FIELDS'] += $newFields;

        return $newFields;
    }

    protected function extendItem()
    {
        $provider = $this->getProvider();
        $isStepsBehavior = ($this->arParams['FORM_BEHAVIOR'] === 'steps');
        $selectFields = [];

        foreach ($this->arResult['TABS'] as $tab)
        {
            if (!$isStepsBehavior)
            {
                array_splice($selectFields, -1, 0, $tab['FIELDS']);
            }
            else if ($tab['STEP'] === $this->arResult['STEP'])
            {
                $selectFields = $tab['FIELDS'];
            }
        }

        $this->arResult['ITEM'] = $provider->extend($this->arResult['ITEM'], $selectFields);
    }

    protected function buildContextMenu()
    {
		$this->arResult['CONTEXT_MENU'] = $this->arParams['CONTEXT_MENU']; // simple copy, need for future modifications
    }

    protected function buildTabs()
    {
        $paramTabs = $this->arParams['TABS'];
        $countTabs = count($paramTabs);
        $hasFinalTab = false;
        $tabIndex = 0;
        $result = [];

        foreach ($paramTabs as $paramTab)
        {
            $isFinalTab = (!empty($paramTab['final']) || (!$hasFinalTab && $tabIndex === $countTabs - 1));

            if ($isFinalTab)
            {
                $hasFinalTab = true;
            }

            $result[] = [
                'STEP' => $tabIndex,
                'FINAL' => $isFinalTab,
                'DIV' => 'tab' . $tabIndex,
                'TAB' => $paramTab['name'],
                'LAYOUT' => $paramTab['layout'] ?: 'default',
                'SELECT' => $paramTab['fields'] ?: [],
                'FIELDS' => [],
                'HIDDEN' => [],
                'DATA' => isset($paramTab['data']) ? (array)$paramTab['data'] : []
            ];

            $tabIndex++;
        }

        $this->arResult['TABS'] = $result;
    }

    protected function registerTabFields(&$tab, $fields)
    {
        foreach ($fields as $fieldKey => $field)
        {
	        if (!empty($field['HIDDEN']))
	        {
	            $tab['HIDDEN'][] = $fieldKey;
	        }
	        else
	        {
	            $tab['FIELDS'][] = $fieldKey;
	        }
        }
    }

	public function getField($fieldKey)
	{
		$result = null;

        if (isset($this->arResult['FIELDS'][$fieldKey]))
        {
            $result = $this->arResult['FIELDS'][$fieldKey];
        }

        return $result;
	}

    public function hasFieldError($field)
    {
        return !empty($this->arResult['FIELD_ERRORS'][$field['FIELD_NAME']]);
    }

    public function getFieldTitle($field)
    {
        return $this->getFirstNotEmpty(
            $field,
            [ 'EDIT_FORM_LABEL', 'LIST_COLUMN_LABEL', 'LIST_FILTER_LABEL' ]
        );
    }

    public function getFieldValue($field)
    {
        $result = null;

        // try fetch from item

        $keyChain = $this->splitFieldNameToChain($field['FIELD_NAME']);

        if (!empty($keyChain))
        {
            $itemLevel = $this->arResult['ITEM'];
            $keyChainLength = count($keyChain);

            for ($i = 0; $i < $keyChainLength; $i++)
            {
				$key = $keyChain[$i];
                $isLastKey = ($i === $keyChainLength - 1);

                if ($isLastKey)
                {
                    $result = isset($itemLevel[$key]) ? $itemLevel[$key] : null;
                }
                else
                {
                    $itemLevel = isset($itemLevel[$key]) ? $itemLevel[$key] : null;
                }
            }
        }

		// may be defined value

		if (!isset($result) && isset($field['VALUE']))
        {
            $result = $field['VALUE'];
        }

        return $result;
    }

    public function getFieldHtml($field, $value = null)
    {
        global $USER_FIELD_MANAGER;

        $result = null;

        if (empty($field['HIDDEN']))
        {
            $field['VALUE'] = $value !== null ? $value : $this->getFieldValue($field);

	        $html = $USER_FIELD_MANAGER->GetEditFormHTML(false, null, $field);

	        $result = $this->extractAdminInput($html);
        }

        return $result;
    }

    protected function getFirstNotEmpty($data, $keys)
    {
        $result = null;

        foreach ($keys as $key)
        {
            if (!empty($data[ $key ]))
            {
                $result = $data[ $key ];
            }
        }

        return $result;
    }

    protected function extractAdminInput($html)
    {
        $result = $html;

        if (preg_match('/^<tr.*?>(?:<td.*?>.*?<\/td>)?<td.*?>(.*)<\/td><\/tr>$/s', $html, $match))
        {
            $result = $match[1];
        }

        return $result;
    }

    public function getLang($code, $replaces = null)
    {
		return Main\Localization\Loc::getMessage(static::$langPrefix . $code, $replaces) ?: $code;
    }

    public function getProvider($providerType = null)
    {
        if ($this->provider === null)
        {
            if (!Main\Loader::includeModule('yandex.market'))
            {
                throw new Main\SystemException($this->getLang('REQUIRE_SELF_MODULE'));
            }

            if (!isset($providerType))
            {
                $providerType = $this->arParams['PROVIDER_TYPE'];
            }

            $className = 'Yandex\Market\Component\\' . $providerType . '\EditForm';

            if (
                !class_exists($className)
                || !is_subclass_of($className, 'Yandex\Market\Component\Base\EditForm')
            )
            {
				throw new Main\SystemException($this->getLang('INVALID_PROVIDER'));
            }

            $this->provider = new $className($this);
        }

        return $this->provider;
    }

    protected function splitFieldNameToChain($key)
    {
        $keyOffset = 0;
        $keyLength = strlen($key);
        $keyChain = [];

        do
        {
            $keyPart = null;

            if ($keyOffset === 0)
            {
                $arrayEnd = strpos($key, '[');

                if ($arrayEnd === false)
                {
                    $keyPart = $key;
                    $keyOffset = $keyLength;
                }
                else
                {
                    $keyPart = substr($key, $keyOffset, $arrayEnd - $keyOffset);
                    $keyOffset = $arrayEnd + 1;
                }
            }
            else
            {
				$arrayEnd = strpos($key, ']', $keyOffset);

				if ($arrayEnd === false)
				{
					$keyPart = substr($key, $keyOffset);
                    $keyOffset = $keyLength;
				}
				else
				{
					$keyPart = substr($key, $keyOffset, $arrayEnd - $keyOffset);
                    $keyOffset = $arrayEnd + 2;
				}
			}

			if (strlen($keyPart) > 0)
			{
				$keyChain[] = $keyPart;
			}
			else
			{
				break;
			}
        }
        while ($keyOffset < $keyLength);

        return $keyChain;
    }
}