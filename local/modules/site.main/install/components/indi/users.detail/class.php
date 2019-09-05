<?

use \Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserTable;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class UsersDetailModule extends CBitrixComponent
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
        $arParams["FIELDS"] = array_filter($arParams["FIELDS"]);
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
        $userId = intval($this->arParams["ELEMENT_ID"]);

        $arSelect = $this->arParams["FIELDS"];
        if(!$arSelect) {
            $arSelect = array("*");
        }

        $rUserTable = UserTable::getList(Array(
            "select" => $arSelect,
            "filter" => array("ID" => $userId),
            "data_doubling" => false
        ));

        $arUser = $rUserTable->Fetch();
        $arResult["USER"] = $arUser;
        $this->arResult = $arResult;

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
				$this->includeComponentTemplate();
			}
		}
		catch (Exception $e)
		{
			ShowError($e->getMessage());
		}
	}
}
?>