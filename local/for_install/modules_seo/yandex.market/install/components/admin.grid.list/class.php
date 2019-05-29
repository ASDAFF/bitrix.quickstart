<?php

namespace Yandex\Market\Components;

use Bitrix\Main;
use Yandex\Market;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class AdminGridList extends \CBitrixComponent
{
    protected static $langPrefix = 'YANDER_MARKET_GRID_LIST_';

    /** @var \Yandex\Market\Component\Base\GridList */
    protected $provider;
    protected $viewList;
    protected $viewFilter;
    protected $viewSort;

    public function onPrepareComponentParams($params)
    {
        $params['GRID_ID'] = trim($params['GRID_ID']);
        $params['SUBLIST'] = ($params['SUBLIST'] === 'Y');
        $params['SUBLIST_TARGET'] = ($params['SUBLIST_TARGET'] === 'Y');
        $params['USE_FILTER'] = (!$params['SUBLIST'] && $params['USE_FILTER'] !== 'N');
        $params['LIST_FIELDS'] = (array)$params['LIST_FIELDS'];
        $params['FILTER_FIELDS'] = !empty($params['FILTER_FIELDS']) ? (array)$params['FILTER_FIELDS'] : $params['LIST_FIELDS'];
        $params['DEFAULT_LIST_FIELDS'] = (array)$params['DEFAULT_LIST_FIELDS'];
        $params['CONTEXT_MENU'] = (array)$params['CONTEXT_MENU'];
        $params['TITLE'] = trim($params['TITLE']);
        $params['NAV_TITLE'] = trim($params['NAV_TITLE']);
        $params['EDIT_URL'] = trim($params['EDIT_URL']);
        $params['ROW_ACTIONS'] = (array)$params['ROW_ACTIONS'];
        $params['GROUP_ACTIONS'] = (array)$params['GROUP_ACTIONS'];
        $params['PRIMARY'] = !empty($params['PRIMARY']) ? (array)$params['PRIMARY'] : [ 'ID' ];

        $params['PROVIDER_TYPE'] = trim($params['PROVIDER_TYPE']);

        $provider = $this->getProvider($params['PROVIDER_TYPE']);

        $params = $provider->prepareComponentParams($params);

        return $params;
    }

    public function executeComponent()
    {
        global $APPLICATION;

        $this->initResult();

        if (!$this->checkParams() || !$this->loadModules())
        {
            $this->showErrors();
            return;
        }

        $hasCriticalError = false;

        try
        {
	        $this->loadFields();
	        $this->loadFilter();
	        $this->buildHeaders();

	        if ($this->canHandleRequest() && ($this->processAction() || $this->hasAjaxRequest()))
	        {
	            $APPLICATION->RestartBuffer();
	        }

	        $queryParams = [];
	        $queryParams += $this->initFilter();
	        $queryParams += $this->initSelect();
	        $queryParams += $this->initPager($queryParams);
	        $queryParams += $this->initSort();

	        $this->loadItems($queryParams);

	        $this->buildContextMenu();
	        $this->buildRows();
			$this->buildNavString($queryParams);
			$this->buildGroupActions();
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

    protected function canHandleRequest()
    {
        return (
            !$this->arParams['SUBLIST']
            || $this->arParams['SUBLIST_TARGET']
        );
    }

    protected function processAction()
    {
        $viewList = $this->getViewList();
        $action = isset($_REQUEST['action_button']) ? $_REQUEST['action_button'] : null;
        $result = false;

		if ($action !== null)
		{
			$result = true;

	        try
	        {
	            $actionData = [
	                'ID' => $viewList->GroupAction(),
	                'IS_ALL' => false
	            ];

	            if (isset($_REQUEST['action_target']) && $_REQUEST['action_target'] === 'selected')
	            {
	                $filter = $this->initFilter();

	                $actionData['IS_ALL'] = true;
	                $actionData['FILTER'] = isset($filter['filter']) ? $filter['filter'] : null;
	            }

	            $provider = $this->getProvider();
	            $provider->processAjaxAction($action, $actionData);
	        }
	        catch (Main\SystemException $exception)
	        {
	            $this->addError($exception->getMessage());
	        }
        }

        return $result;
    }

    protected function hasAjaxRequest()
    {
        return (
            $this->request->isAjaxRequest()
            && ($this->request->get('table_id') === $this->arParams['GRID_ID'] || !$this->isSubList())
            && $this->request->get('mode') !== null
        );
    }

    protected function deleteItem($id)
    {
        $provider = $this->getProvider();

        $provider->deleteItem($id);
    }

    protected function initResult()
    {
        $this->arResult['CONTEXT_MENU'] = [];
        $this->arResult['FIELDS'] = [];
        $this->arResult['FILTER'] = [];
        $this->arResult['ITEMS'] = [];
        $this->arResult['TOTAL_COUNT'] = null;
        $this->arResult['ERRORS'] = [];
    }

    protected function getRequiredParams()
    {
        $provider = $this->getProvider();
        $result = [ 'GRID_ID' ] + $provider->getRequiredParams();

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
                $message = $this->getLang('PARAM_REQUIRE', array(
                    '#PARAM#' => $paramKey
                ));

                $this->addError($message);
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
            $message = $this->getLang('MODULE_REQUIRE', [
                '#MODULE#' => $module
            ]);

            $this->addError($message);
        }

        return $result;
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
            'MESSAGE' => implode('<br />', $this->arResult['ERRORS'])
        ]);
    }

    protected function setTitle()
    {
		global $APPLICATION;

        if ($this->arParams['TITLE'] !== '')
        {
            $APPLICATION->SetTitle($this->arParams['TITLE']);
        }
    }

    protected function loadFields()
    {
    	$provider = $this->getProvider();
        $select = $this->arParams['LIST_FIELDS'];

        $this->arResult['FIELDS'] = $provider->getFields($select);
    }

    protected function initFilter()
    {
        $provider = $this->getProvider();
        $defaultFilter = $provider->getDefaultFilter();
        $result = [];

		if (!empty($defaultFilter))
		{
			$result['filter'] = $defaultFilter;
		}
		else if (!empty($this->arParams['DEFAULT_FILTER']))
		{
			$result['filter'] = (array)$this->arParams['DEFAULT_FILTER'];
		}

        if (!$this->arParams['USE_FILTER'])
        {
            return $result;
        }

        $listView = $this->getViewList();
        $request = $listView->getFilter();

        foreach ($this->arResult['FILTER'] as &$filter)
        {
            switch ($filter['type'])
            {
	            case 'number':
	            case 'date':

	                $fromRequestKey = $filter['id'] . '_from';
	                $hasFromRequest = (isset($request[$fromRequestKey]) && $request[$fromRequestKey] !== '');
	                $toRequestKey = $filter['id'] . '_to';
	                $hasToRequest = (isset($request[$toRequestKey]) && $request[$toRequestKey] !== '');
	                $filter['value'] = [
                        'from' => $hasFromRequest ? htmlspecialcharsbx($request[$fromRequestKey]) : '',
                        'to' => $hasToRequest ? htmlspecialcharsbx($request[$toRequestKey]) : ''
                    ];

	                if ($hasFromRequest || $hasToRequest)
	                {
	                    if (!isset($result['filter']))
		                {
		                    $result['filter'] = [];
		                }

		                if ($hasFromRequest)
		                {
		                    $result['filter']['>=' . $filter['fieldName']] = $request[$fromRequestKey];
		                }

		                if ($hasToRequest)
		                {
		                    $result['filter']['<=' . $filter['fieldName']] = $request[$toRequestKey];
		                }
	                }

	            break;

	            default:

	                if (isset($request[$filter['id']]) && $request[$filter['id']] !== '')
		            {
		                $filterRequest = $request[$filter['id']];

		                $filter['value'] = htmlspecialcharsbx($filterRequest);

		                if (!isset($result['filter']))
		                {
		                    $result['filter'] = [];
		                }

		                $result['filter'][$filter['fieldName']] = $filterRequest;
		            }

	            break;
            }
        }
        unset($filter);

	    return $result;
    }

    protected function initSelect()
    {
        $view = $this->getViewList();

	    return [
            'select' => $view->GetVisibleHeaderColumns()
        ];
    }

    protected function initPager($queryParams)
    {
        $result = [];
        $navSize = null;

        if ($this->isSubList())
        {
            if ($this->isSubListAjaxPage())
            {
                $this->fillEmptyPager();
            }

            $navSize = \CAdminSubResult::GetNavSize(
                $this->arParams['GRID_ID'],
                20,
                $this->arParams['AJAX_URL']
            );
        }
        else
        {
            $navSize = \CAdminResult::GetNavSize($this->arParams['GRID_ID']);
        }

        $navParams = \CDBResult::GetNavParams($navSize);

		if (!$navParams['SHOW_ALL'])
		{
			$page = (int)$navParams['PAGEN'];
			$pageSize = (int)$navParams['SIZEN'];

			$totalCount = $this->loadTotalCount($queryParams);
			$maxPageNum = max(1, ceil($totalCount / $pageSize));

			if ($page > $maxPageNum)
			{
				$page = $maxPageNum;
			}

			$result['limit'] = $pageSize;
			$result['offset'] = $pageSize * ($page - 1);

			$this->arResult['TOTAL_COUNT'] = $totalCount;
		}

        return $result;
    }

    protected function fillEmptyPager()
    {
        global $NavNum;

        if ($NavNum === null) { $NavNum = 0; }

        for ($i = $NavNum + 1; $i < 10; $i++)
        {
            if (isset($_REQUEST['SIZEN_' . $i]))
            {
                $NavNum = $i - 1;
                break;
            }
        }
    }

    protected function initSort()
    {
	    $viewSort = $this->getViewSort();
	    $order = null;

	    if (!empty($GLOBALS[$viewSort->by_name]))
	    {
	    	$sortField = strtoupper($GLOBALS[$viewSort->by_name]);

	    	if (isset($this->arResult['FIELDS'][$sortField]))
		    {
		        $sortOrder = (
		            isset($GLOBALS[$viewSort->ord_name]) && strtoupper($GLOBALS[$viewSort->ord_name]) === 'DESC'
		                ? 'DESC'
		                : 'ASC'
		        );

		        $order = [
		            $sortField => $sortOrder
		        ];
		    }
	    }

	    if ($order === null)
	    {
	        $provider = $this->getProvider();
	        $order = $provider->getDefaultSort();
	    }

	    return [
	        'order' => $order
	    ];
    }

    protected function loadItems($queryParams)
    {
        if (!empty($queryParams['select']))
        {
            $queryParams['select'] = array_merge(
                $queryParams['select'],
                $this->arParams['PRIMARY']
            );
        }

        $this->arResult['ITEMS'] = $this->queryItems($queryParams);
    }

    protected function queryItems($queryParams)
    {
        $provider = $this->getProvider();

        return $provider->load($queryParams);
    }

    protected function loadTotalCount($queryParams)
    {
        $provider = $this->getProvider();

        return $provider->loadTotalCount($queryParams);
    }

    protected function loadFilter()
    {
        if (!$this->arParams['USE_FILTER']) { return; }

        $useFieldsMap = array_flip($this->arParams['FILTER_FIELDS']);
        $filterIdList = [];

        foreach ($this->arResult['FIELDS'] as $fieldName => $field)
        {
            if (!isset($useFieldsMap[$fieldName]) || $field['USER_TYPE']['BASE_TYPE'] === 'file') { continue; }

			$hasClassName = !empty($field['USER_TYPE']['CLASS_NAME']);
            $item = [
                'id' => 'find_' . strtolower($fieldName),
                'fieldName' => $fieldName,
                'value' => null,
                'name' => $this->getFirstNotEmpty($field, array('LIST_COLUMN_LABEL', 'EDIT_FORM_LABEL', 'LIST_FILTER_LABEL')),
                'type' => null
            ];

			if ($field['USER_TYPE']['BASE_TYPE'] === 'list' && !empty($field['VALUES']))
            {
	            $item['type'] = 'list';
	            $item['items'] = [];

	            foreach ($field['VALUES'] as $option)
	            {
	            	$item['items'][$option['ID']] = $option['VALUE'];
	            }

	            $filterIdList[] = $item['id'];
            }
			else if ($hasClassName && is_callable(array($field['USER_TYPE']['CLASS_NAME'], 'GetList')))
            {
                $item['type'] = 'list';
                $item['items'] = [];

                $query = call_user_func(array($field['USER_TYPE']['CLASS_NAME'], 'GetList'), $field);

                if (is_array($query))
                {
	                foreach ($query as $option)
	                {
		                $item['items'][$option['ID']] = $option['VALUE'];
	                }
                }
                else if ($query)
                {
	                while ($option = $query->Fetch())
	                {
		                $item['items'][$option['ID']] = $option['VALUE'];
	                }
                }

                $filterIdList[] = $item['id'];
            }
            else if ($field['USER_TYPE']['BASE_TYPE'] === 'datetime')
            {
                $item['type'] = 'date';

                $filterIdList[] = $item['id'] . '_from';
            	$filterIdList[] = $item['id'] . '_to';
            }
            else if ($field['USER_TYPE']['BASE_TYPE'] === 'int')
            {
            	$item['type'] = 'number';

            	$filterIdList[] = $item['id'] . '_from';
            	$filterIdList[] = $item['id'] . '_to';
            }
            else if ($hasClassName && is_callable(array($field['USER_TYPE']['CLASS_NAME'], 'GetFilterHTML')))
            {
                $item['type'] = 'custom';

                $filterIdList[] = $item['id'];
            }
            else
            {
                $item['type'] = 'string';

                $filterIdList[] = $item['id'];
            }

            $this->arResult['FILTER'][$fieldName] = $item;
        }

        $this->getViewList()->InitFilter($filterIdList);
    }

    public function getFilterHtml($filter)
    {
        $field = $this->arResult['FIELDS'][$filter['fieldName']];

        return call_user_func(
            [ $field['USER_TYPE']['CLASS_NAME'], 'GetFilterHTML' ],
            $field,
			[
				'NAME' => $filter['id'],
				'VALUE' => $filter['value'],
				'TABLE_ID' => $this->arParams['GRID_ID'] . '_filter',
			]
		);
    }

	protected function buildContextMenu()
    {
		if (!empty($this->arParams['CONTEXT_MENU']))
		{
			$view = $this->getViewList();
			$view->AddAdminContextMenu($this->arParams['CONTEXT_MENU'], false, true);
		}
    }

    protected function buildHeaders()
    {
        $defaultFieldsMap = array_flip($this->arParams['DEFAULT_LIST_FIELDS']);
        $headers = [];
        $view = $this->getViewList();

        foreach ($this->arResult['FIELDS'] as $fieldName => $field)
        {
            $headers[$fieldName] = [
                'id' => $fieldName,
                'content' => $this->getFirstNotEmpty($field, array('LIST_COLUMN_LABEL', 'EDIT_FORM_LABEL', 'LIST_FILTER_LABEL')),
                'sort' => $fieldName,
                'first_order' => 'asc',
                'default' => empty($defaultFieldsMap) || isset($defaultFieldsMap[$fieldName])
            ];
        }

        $view->AddHeaders($headers);
    }

    protected function buildRows()
    {
        if (!empty($this->arResult['ITEMS']))
        {
            $view = $this->getViewList();
            $headers = $view->GetVisibleHeaderColumns();

            foreach ($this->arResult['ITEMS'] as $item)
            {
                $link = null;
                $actions = $this->buildRowActions($item);

                if ($this->arParams['EDIT_URL'] !== '')
                {
                    $link = str_replace('#ID#', $item['ID'], $this->arParams['EDIT_URL']);
                }

                $viewRow = $view->AddRow($item['ID'], [], $link);

                foreach ($headers as $fieldName)
                {
                    $viewRow->AddViewField($fieldName, $this->buildRowValue($item, $fieldName));
                }

				if ($actions !== null)
				{
                    $viewRow->AddActions($actions);
                }
            }
        }
    }

    protected function buildRowValue($item, $fieldKey)
    {
        $result = null;

        if (isset($this->arResult['FIELDS'][$fieldKey]['USER_TYPE']['CLASS_NAME']))
        {
            $field = $this->arResult['FIELDS'][$fieldKey];
            $result = $this->buildRowValueFromUserField($field, $item[$fieldKey], $item);
        }
        else if (isset($item[$fieldKey]))
        {
            $result = $item[$fieldKey];
        }

        return $result;
    }

    protected function buildRowValueFromUserField($userField, $value, $row)
    {
        global $USER_FIELD_MANAGER;

        $userField['ROW'] = $row;
        $userField['VALUE'] = $value;
		$isValidValue = true;

		if ($userField['MULTIPLE'] !== 'N')
		{
			if (is_array($value))
			{
				foreach ($value as $option)
				{
					if (is_array($option))
					{
						$isValidValue = false;
						break;
					}
				}
			}
		}
		else if (is_array($value))
		{
			$isValidValue = false;
		}

		if (!$isValidValue)
		{
			$value = null;
		}

        return $USER_FIELD_MANAGER->getListView($userField, $value);
    }

    protected function buildRowActions($item)
    {
    	global $APPLICATION;

        $result = null;

        if (!empty($this->arParams['ROW_ACTIONS']))
        {
            $result = [];
            $replacesFrom = [];
            $replacesTo = [];

            foreach ($item as $key => $value)
            {
                if (is_scalar($value))
                {
                    $replacesFrom[] ='#' . $key . '#';
                    $replacesTo[] = $value;
                }
            }

            foreach ($this->arParams['ROW_ACTIONS'] as $type => $action)
            {
                $actionMethod = null;

                switch ($type)
                {
	                case 'DELETE':
	                    $queryParams = bitrix_sessid_get() . '&' . http_build_query([
							'action_button' => 'delete',
							'ID' => $item['ID'],
						]);

						$url = $APPLICATION->GetCurPageParam(
							$queryParams,
							[ 'action_button', 'ID' ]
						);

						$actionMethod = $this->arParams['GRID_ID'] . '.GetAdminList("' . \CUtil::addslashes($url) . '");';
	                break;

	                default:
	                    $actionUrl = str_replace($replacesFrom, $replacesTo, $action['URL']);

					    if (strpos($actionUrl, 'lang=') === false)
					    {
						    $actionUrl .= (strpos($actionUrl, '?') === false ? '?' : '&') . 'lang=' . LANGUAGE_ID;
					    }

					    if (isset($action['MODAL']) && $action['MODAL'] === 'Y')
					    {
							$actionMethod = '(new BX.CAdminDialog({
								"content_url": "' . \CUtil::AddSlashes($actionUrl) . '",
								"draggable": true,
								"resizable": true
							})).Show();';
					    }
					    else if (isset($action['WINDOW']) && $action['WINDOW'] === 'Y')
					    {
					        $actionMethod = 'jsUtils.OpenWindow("' . \CUtil::AddSlashes($actionUrl) . '", 900, 700);';
					    }
					    else
					    {
					        $actionMethod = "BX.adminPanel.Redirect([], '".\CUtil::AddSlashes($actionUrl)."', event);";
				        }
                    break;
                }

                if ($actionMethod !== null)
                {
                    if (!empty($action['CONFIRM']))
                    {
                        $confirmMessage = !empty($action['CONFIRM_MESSAGE']) ? $action['CONFIRM_MESSAGE'] : $this->getLang('ROW_ACTION_CONFIRM');
                        $actionMethod = 'if (confirm("' . \CUtil::AddSlashes($confirmMessage) . '")) ' . $actionMethod;
                    }

	                $result[] = [
				        'ACTION'=> $actionMethod,
					    'ICON' => isset($action['ICON']) ? $action['ICON'] : null,
					    'DEFAULT' => isset($action['DEFAULT']) ? $action['DEFAULT'] : null,
					    'TEXT' => $action['TEXT']
				    ];
	            }
            }
        }

        return $result;
    }

    protected function buildNavString($queryParams)
    {
        $listView = $this->getViewList();
        $iterator = null;

        if ($this->isSubList())
        {
            $iterator = new \CAdminSubResult([], $this->arParams['GRID_ID'], $listView->GetListUrl(true));
        }
        else
        {
            $iterator = new \CAdminResult([], $this->arParams['GRID_ID']);
        }

		if (isset($queryParams['limit']))
		{
			$page = floor($queryParams['offset'] / $queryParams['limit']) + 1;
			$totalCount = $this->arResult['TOTAL_COUNT'];
			$totalPages = ceil($totalCount / $queryParams['limit']);

			$iterator->NavStart($queryParams['limit'], true, $page);
			$iterator->NavRecordCount = $totalCount;
			$iterator->NavPageCount = $totalPages;
			$iterator->NavPageNomer = $page;
		}
		else
		{
			$iterator->NavStart();
		}

		$listView->NavText($iterator->GetNavPrint($this->arParams['NAV_TITLE']));
    }

    protected function buildGroupActions()
    {
		if (!empty($this->arParams['GROUP_ACTIONS']))
		{
			$viewList = $this->getViewList();

			$viewList->AddGroupActionTable($this->arParams['GROUP_ACTIONS']);
		}
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

    public function getViewList()
    {
        if ($this->viewList === null)
        {
            if ($this->isSubList())
            {
                $this->viewList = new \CAdminSubList(
                    $this->arParams['GRID_ID'],
                    false, //$this->getViewSort(), sort inside class
                    $this->arParams['AJAX_URL']
                );
            }
            else
            {
            	$this->viewList = new \CAdminList(
	                $this->arParams['GRID_ID'],
                    $this->getViewSort()
	            );
	        }
        }

        return $this->viewList;
    }

    public function getViewSort()
    {
        if ($this->viewSort === null)
        {
            $this->viewSort = new \CAdminSorting($this->arParams['GRID_ID']);
        }

        return $this->viewSort;
    }

    public function getViewFilter()
    {
        if ($this->viewFilter === null)
        {
            $this->viewFilter = new \CAdminFilter(
                $this->arParams['GRID_ID'] . '_filter',
                $this->getViewFilterPopup()
            );
        }

        return $this->viewFilter;
    }

    protected function getViewFilterPopup()
    {
        $result = [];

        foreach ($this->arResult['FILTER'] as $filter)
        {
            $result[] = $filter['name'];
        }

        return $result;
    }

    protected function isSubList()
    {
        $result = false;

        if ($this->arParams['SUBLIST'] && Main\Loader::includeModule('iblock'))
        {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/iblock/classes/general/subelement.php';

            $result = true;
        }

        return $result;
    }

    protected function isSubListAjaxPage()
    {
        global $APPLICATION;

        $curPage = $APPLICATION->GetCurPage(false);

        return strpos($this->arParams['AJAX_URL'], $curPage) === 0;
    }

    public function getUrl()
    {
        global $APPLICATION;

        return $APPLICATION->GetCurPageParam();
    }

    public function getBaseUrl()
    {
        global $APPLICATION;

        return $APPLICATION->GetCurPage();
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

            $className = 'Yandex\Market\Component\\' . $providerType . '\GridList';

            if (
                !class_exists($className)
                || !is_subclass_of($className, 'Yandex\Market\Component\Base\GridList')
            )
            {
				throw new Main\SystemException($this->getLang('INVALID_PROVIDER'));
            }

            $this->provider = new $className($this);
        }

        return $this->provider;
    }
}