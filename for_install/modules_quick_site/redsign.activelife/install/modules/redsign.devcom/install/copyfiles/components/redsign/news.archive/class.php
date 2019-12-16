<?php

use Bitrix\Main,
	Bitrix\Iblock,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\Application,
	Bitrix\Main\Web\Uri;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class CRsNewsArchive extends CBitrixComponent
{
	/**
	 * Primary data - months.
	 * @var array[]
	 */
	protected $months = array();


	protected $bUserHaveAccess = false;

    
	/**
	 * Wether module Iblock included?
	 * @var bool
	 */
	protected $isIblock = true;

	/**
	 * Errors list.
	 * @var string[]
	 */
	protected $errors = array();

	/**
	 * Warnings list.
	 * @var string[]
	 */
	protected $warnings = array();


	/**
	 * Url templates for items
	 *
	 * @var array
	 */
	protected $urlTemplates = array();

	/**
	 * Load language file.
	 */
	public function onIncludeComponentLang()
	{
		$this->includeComponentLang(basename(__FILE__));
		Loc::loadMessages(__FILE__);
	}

	/**
	 * Check Required Modules
	 * @throws Exception
	 */
	protected function checkModules()
	{
		if (!Main\Loader::includeModule('iblock'))
			throw new Main\SystemException(Loc::getMessage('RS_DEVCOM.NEWS_ARCHIVE.IBLOCK_MODULE_NOT_INSTALLED'));
		$this->isIblock = true;
	}

	/**
	 * Prepare Component Params.
	 *
	 * @param array $params			Component parameters.
	 * @return array
	 */
	public function onPrepareComponentParams($params)
	{
        if(!isset($params['CACHE_TIME']))
            $params['CACHE_TIME'] = 36000000;

        $params['IBLOCK_TYPE'] = trim($params['IBLOCK_TYPE']);
        if(strlen($params['IBLOCK_TYPE'])<=0)
            $params['IBLOCK_TYPE'] = 'news';
        $params['IBLOCK_ID'] = trim($params['IBLOCK_ID']);
        
        $params['PARENT_SECTION'] = intval($params['PARENT_SECTION']);
        
        $params['ARCHIVE_URL']=trim($params['ARCHIVE_URL']);
        
        $params['PARENT_SECTION'] = CIBlockFindTools::GetSectionID(
            $params['PARENT_SECTION'],
            $params['PARENT_SECTION_CODE'],
            array(
                'GLOBAL_ACTIVE' => 'Y',
                'IBLOCK_ID' => $params['IBLOCK_ID'],
            )
        );
        
        $params['CACHE_FILTER'] = $params['CACHE_FILTER']=='Y';
        if(!$params['CACHE_FILTER'] && count($arrFilter)>0)
            $params['CACHE_TIME'] = 0;

        $params['ACTIVE_DATE_FORMAT'] = trim($params['ACTIVE_DATE_FORMAT']);
        if (strlen($params['ACTIVE_DATE_FORMAT'])<=0) {
            global $DB;
            $params['ACTIVE_DATE_FORMAT'] = $DB->DateFormatToPHP(CSite::GetDateFormat('SHORT'));
        }

        $params['CHECK_PERMISSIONS'] = $params['CHECK_PERMISSIONS']!='N';

        $params['USE_PERMISSIONS'] = $params['USE_PERMISSIONS']=='Y';
        if(!is_array($params['GROUP_PERMISSIONS']))
            $params['GROUP_PERMISSIONS'] = array(1);

        $this->bUserHaveAccess = !$params['USE_PERMISSIONS'];
        if($params['USE_PERMISSIONS'] && isset($GLOBALS['USER']) && is_object($GLOBALS['USER']))
        {
            $arUserGroupArray = $USER->GetUserGroupArray();
            foreach($params['GROUP_PERMISSIONS'] as $PERM)
            {
                if(in_array($PERM, $arUserGroupArray))
                {
                    $this->bUserHaveAccess = true;
                    break;
                }
            }
        }
        
        $params["SHOW_TITLE"] = $params["SHOW_TITLE"] == "Y";
        $params["SHOW_YEARS"] = $params["SHOW_YEARS"] == "Y";
        $params["SHOW_MONTHS"] = $params["SHOW_MONTHS"] == "Y";

		return $params;
	}

	protected function fillUrlTemplates()
	{
		global $APPLICATION;

		$currentPath = CHTTP::urlDeleteParams(
			$APPLICATION->GetCurPageParam(),
			array(),
			array('delete_system_params' => true)
		);
	}

	/**
	 * Get items for view.
	 * @return mixed[]  array()
	 */
	protected function getMonths()
	{

		if ($this->arParams['PARENT_SECTION'] > 0) {
		    $this->filter['=SECTION_ID'] = $this->arParams['PARENT_SECTION'];
		    if ($this->arParams['INCLUDE_SUBSECTIONS']) {
		        $this->filter['=INCLUDE_SUBSECTIONS'] = 'Y';
		    }
		}
	    
	    $arSelect = array(
	        'COUNT',
	        'MONTH',
	        'YEAR',
	    );
	    
	    $arFilter = array(
			'ACTIVE' => 'Y',
			'IBLOCK_ID' => $this->arParams['IBLOCK_ID']
		);
	    

	    $arGroup =  array(
		    'YEAR',
		    'MONTH',
		);
	    
	    $arOrder = array(
	        'YEAR' => 'DESC',
	        'MONTH' => 'DESC',
	    );

	    $arRuntimeFields = array(
            new \Bitrix\Main\Entity\ExpressionField('COUNT', 'COUNT(*)'),
            new \Bitrix\Main\Entity\ExpressionField('MONTH', 'MONTH(ACTIVE_FROM)'),
            new \Bitrix\Main\Entity\ExpressionField('YEAR', 'YEAR(ACTIVE_FROM)'),
	    );
		
		
		$monthsIterator = \Bitrix\Iblock\ElementTable::getlist(array(
		    'select' => $arSelect,
		    'filter' => $arFilter,
		    'group' => $arGroup,
		    'order' => $arOrder,
		    'runtime' => $arRuntimeFields,
		));
		
		$months = array();
		
		while ($month = $monthsIterator->fetch()) {
		    $months[] = $month;
		    unset ($month);
		}
		
		return $months;
	}

	/**
	 * Get main data - viewed products.
	 * @return void
	 */
	protected function prepareData()
	{
	    $request = Application::getInstance()->getContext()->getRequest();
	    
		//$this->fillUrlTemplates();
		$this->months = $this->getMonths();
        
        $year = intval($request->get('YEAR'));
		$month = intval($request->get('MONTH'));

        if ($year > 0) {

            if(strlen($this->arParams['FILTER_NAME'])<=0 || !preg_match('/^[A-Za-z_][A-Za-z01-9_]*$/', $this->arParams['FILTER_NAME']))
            {
                $arrFilter = array();
            }
            else
            {
                $arrFilter = $GLOBALS[$this->arParams['FILTER_NAME']];
                if(!is_array($arrFilter))
                    $arrFilter = array();
            }

            if (!is_array(${$FILTER_NAME})) {
                $arrFilter = array();
            }

            if ($month == 0) {
                $start = new \Bitrix\Main\Type\Date('01.01.'.$year, 'j.n.Y');
                $end = new \Bitrix\Main\Type\Date('31.12.'.$year, 'j.n.Y');
            } else {
                $start = new \Bitrix\Main\Type\Date('01.'.$month.'.'.$year, 'j.n.Y');
                $end = new \Bitrix\Main\Type\Date('31.'.$month.'.'.$year, 'j.n.Y');
            }
            
            if ($month > 0 || $year > 0) {
                $arrFilter['><DATE_ACTIVE_FROM'] = array(
                    $start->toString(),
                    $end->toString(),
                );
            }

            $GLOBALS[$this->arParams['FILTER_NAME']] = $arrFilter;
        }
	}

	/**
	 * Prepare data to render.
	 * @return void
	 */
	protected function formatResult()
	{
	    $request = Application::getInstance()->getContext()->getRequest();
	    $sCurrentUri = $request->getRequestUri();

	    $this->arResult['HAS_SELECTED'] = false;;
		$this->arResult['YEARS'] = array();

		foreach ($this->months as $month) {
            
            if ($month['YEAR'] <= 0) {
                continue;
            }

		    $date = new \Bitrix\Main\Type\Date('01.'.$month['MONTH'].'.'.$month['YEAR'], 'j.n.Y');
		    
    	    if (!isset($this->arResult['YEARS'][$month['YEAR']])) {

		        $this->arResult['YEARS'][$month['YEAR']] = array(
		            'NAME' =>  $month['YEAR'],
		            'SELECTED' => false,
		        );
		        
		        if ($this->arParams['SEF_MODE'] == 'Y') {

		            $archiveParts = array();

		            if ($this->arParams['ARCHIVE_URL']) {
		                $TEMPLATE = $this->arParams['ARCHIVE_URL'];
		            } else {
		                $TEMPLATE = '';
		            }
		            
		            if ($TEMPLATE) {
		                //$TEMPLATE = str_replace("#YEAR#", $month['YEAR'], $TEMPLATE);
		                //$TEMPLATE = str_replace("#MONTH#", '', $TEMPLATE);
		                $archiveParts[] = $month['YEAR'];
		                $TEMPLATE = str_replace('#ARCHIVE_PATH#', implode('/', $archiveParts), $TEMPLATE);
		                
		                $this->arResult['YEARS'][$month['YEAR']]['ARCHIVE_URL'] = preg_replace("'(?<!:)/+'s", "/", $TEMPLATE);
		            }
		        } else {
		            
		            $uri = new Uri($sCurrentUri);
		            $uri->deleteParams(
	                    array(
	                        'YEAR',
	                        'MONTH',
	                    )
                    );
		            $uri->addParams(
	                    array(
	                        'YEAR' => $month['YEAR'],
	                    )
                    );
		            $this->arResult['YEARS'][$month['YEAR']]['ARCHIVE_URL'] = $uri->getUri(); 
		        }

		    }
		    
		    if ($request->get('YEAR') == $month['YEAR']) {
		        $this->arResult['YEARS'][$month['YEAR']]['SELECTED'] = true;
		        $this->arResult['HAS_SELECTED'] = true;
		    }
		    
		    $this->arResult['YEARS'][$month['YEAR']]['COUNT'] += intval($month['COUNT']);
            
            $month['NAME'] = FormatDate('f', $date->getTimestamp());
            
            
            if ($this->arParams['SEF_MODE'] == 'Y') {
                
                $archiveParts = array();
                
                if ($this->arParams['ARCHIVE_URL']) {
					$TEMPLATE = $this->arParams['ARCHIVE_URL'];
                } else {
					$TEMPLATE = '';
                }
                
                if ($TEMPLATE) {
                    $archiveParts[] = $month['YEAR'];
                    $archiveParts[] = $month['MONTH'];
                    
                    $TEMPLATE = str_replace('#ARCHIVE_PATH#', implode('/', $archiveParts), $TEMPLATE);
                    
                    $month['ARCHIVE_URL'] = preg_replace("'(?<!:)/+'s", "/", $TEMPLATE);
                }

            } else {
                
                $uri = new Uri($sCurrentUri);
                $uri->deleteParams(
                    array(
                        'YEAR',
                        'MONTH',
                    )
                );
                
                $uri->addParams(
                    array(
                        'MONTH' => $month['MONTH'],
                    )
                );
                
                $month['ARCHIVE_URL'] = $uri->getUri();
            }
            
            
		    //$month['ARCHIVE_URL'] = $this->arParams['ARCHIVE_URL'].'?year='.$month['YEAR'].'&month='.$month['MONTH'];
		    
            
            
            if ($request->get('YEAR') == $month['YEAR'] && $request->get('MONTH') == $month['MONTH']) {
                $month['SELECTED'] = true;
                $this->arResult['HAS_SELECTED'] = true;
            }
            
		    $this->arResult['YEARS'][$month['YEAR']]['MONTHS'][$month['MONTH']] = $month;
		    unset($month);
		    
		}
		
		$this->arResult['ARCHIVE_URL'] = preg_replace(
		    "'(?<!:)/+'s",
		    "/",
		    str_replace('#ARCHIVE_PATH#', '', $this->arParams['ARCHIVE_URL'])    
		);

		$this->arResult['ERRORS'] = $this->errors;
		$this->arResult['WARNINGS'] = $this->warnings;
	}

	/**
	 * Extract data from cache. No action by default.
	 * @return bool
	 */
	protected function extractDataFromCache()
	{
		return false;
	}

	protected function putDataToCache()
	{
	}

	protected function abortDataCache()
	{
	}

	/**
	 * Start Component
	 */
	public function executeComponent()
	{
		/** @global CMain $APPLICATION */
		global $APPLICATION;
		try
		{
			$this->checkModules();
			if (!$this->extractDataFromCache())
			{
				$this->prepareData();
				$this->formatResult();

				$this->setResultCacheKeys(array());
				$this->includeComponentTemplate();
				$this->putDataToCache();
			}
		}
		catch (Main\SystemException $e)
		{
			$this->abortDataCache();

			ShowError($e->getMessage());
		}
	}
}