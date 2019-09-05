<?

use \Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class ClearComponentD7 extends CBitrixComponent
{

	/**
	* подключает языковые файлы
	*/

	public function onIncludeComponentLang()
	{
		$this->includeComponentLang(basename(__FILE__));
		Loc::loadMessages(__FILE__);
	}   

	/**
	* Обработка входных параметров
	* 
	* @param mixed[] $arParams
	* @return mixed[] $arParams
	*/ 

	public function onPrepareComponentParams($arParams)
	{
		// время кэширования

		$arParams["CACHE_TIME"] = (int) $arParams["CACHE_TIME"];

		return $arParams;
	}



	/**
	* получение результатов
	* 
	* @return void
	*/

	protected function getResult()
	{
		$arResult = array();
	}

	/**
	* выполняет логику работы компонента
	* 
	* @return void
	*/

	public function executeComponent()
	{
		try
		{    
			if($this->StartResultCache($this->arParams["CACHE_TIME"])){ 
				$this->getResult();
				$this->includeComponentTemplate($this->page); 
			}

		}
		catch (Exception $e)
		{   
			ShowError($e->getMessage());
		}
	}
}
?>