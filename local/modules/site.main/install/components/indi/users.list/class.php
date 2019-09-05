<?
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\UserTable;
use \Bitrix\Main\Application;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class UsersListModule extends CBitrixComponent
{

	/**
	 * Возвраещает текущий счетчик компонентов на странице сайта, в которых есть постраничная навигация + 1
	 *
	 * @return int $NavNum
	 */
	private function getNavNum() {
		global $NavNum;
		return intval($NavNum)+1;
	}

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
		//\Site\Main\Util::debug($arParams);
		$arParams["CACHE_TIME"] = (int)$arParams["CACHE_TIME"];
		$arParams["FIELDS"] = array_filter($arParams["FIELDS"]);

		return $arParams;
	}

	/**
	 * Получение реквеста
	 * @return \Bitrix\Main\HttpRequest
	 */

	private function getRequest() {
		$app = Application::getInstance();
		$context = $app->getContext();
		$request = $context->getRequest();
		return $request;
	}

	/**
	 * Получение фильтра
	 * @return array
	 */

	private function getFilter() {
		$ufName = $this->arParams["FILTER_USER_FIELD"];
		$arrFilter = $GLOBALS[$this->arParams["FILTER_NAME"]];
		if(!$arrFilter) {
			$arrFilter = array();
		}
		if($arrFilter[$ufName] == "all" || !$arrFilter[$ufName]) {
			unset($arrFilter[$ufName]);
		}
		return $arrFilter;
	}


	/**
	 * Получение постраничной навигации
	 *
	 * @param integer $pageNumber Номер текущей страницы
	 * @param object $rs Результат выборки из таблицы с пользователями
	 * @return string
	 */

	private function getPagenation($pageNumber, $rs) {
		$limit = $rs->SelectedRowsCount();
		$pageElementCount = $this->arParams["PAGE_ELEMENT_COUNT"];
		$rs->NavStart($pageElementCount, false, $pageNumber);
		$rs->NavPageCount = ceil($limit / $pageElementCount);
		$rs->NavPageNomer = $pageNumber;
		$rs->NavPageSize = $pageElementCount;
		$rs->NavRecordCount = $limit;
		$rs->bShowAll = false;
		return $rs->GetPageNavStringEx($navComponentObject);
	}

	/**
	 * Получение пользователей
	 *
	 * @param integer $pageNumber Номер текущей страницы
	 * @return array
	 */

	private function getUsers($pageNumber) {
		$arrFilter = $this->getFilter();
		$arSelect = $this->arParams["FIELDS"];
		if(!$arSelect) {
			$arSelect = array("*");
		}

        if($this->arParams["SORT_BY"] && $this->arParams["SORT_ORDER"]) {
            $order = array(
                $this->arParams["SORT_BY"] => $this->arParams["SORT_ORDER"]
            );
        }
        else {
            $order = array("ID" => "asc");
        }
		$result = array();

		try {
			$rUserTable = UserTable::getList(Array(
				"select" => $arSelect,
				"filter" => $arrFilter,
				"order" => $order,
				"limit" => $this->arParams["USERS_COUNT"],
				"data_doubling" => false
			));
            $rs = new \CDBResult($rUserTable);

            // Постраничная навигация
            if($this->arParams["SHOW_PAGER"] == "Y") {
                $result['NAV_RESULT'] = $this->getPagenation($pageNumber, $rs);
            }

			// пользователи
			while ($arUser = $rs->Fetch()) {
                if($this->arParams["LIST_LINK_DETAIL"] == "Y") {
                    $arUser["PATH"] = $this->arParams["SEF_FOLDER"] . $arUser["ID"] . "/";
                }
				$result["USERS"][] = $arUser;
			}

		} catch (Exception $e) {
			ShowError($e->getMessage());
			$this->AbortResultCache();
		}

		return $result;
	}

	/**
	 * Получение результата
	 *
	 * @param integer $pageNumber Номер текущей страницы
	 * @return array
	 */

	protected function getResult($pageNumber)
	{
		$arUsers = $this->getUsers($pageNumber);
		$arResult["USERS"] = $arUsers["USERS"];
		$arResult["NAV_RESULT"] = $arUsers["NAV_RESULT"];
		return $arResult;
	}

	/**
	 * Получение номера текущей страницы
	 *
	 * @return integer $pageNumber
	 */

	private function getPageNumber() {
		$Num = $this->getNavNum();
		$request = $this->getRequest();
		$pageNumber = $request->getQuery('PAGEN_'.$Num);
		if(!$pageNumber) {
			$pageNumber = 1;
		}
		return $pageNumber;
	}


	/**
	 * выполняет логику работы компонента
	 *
	 * @return void
	 */

	public function executeComponent()
	{
		try {
			// добавляем номер страницы и фильтер в кэш
			$pageNumber = $this->getPageNumber();
			$arrFilter = $this->getFilter();
			if ($this->StartResultCache($this->arParams["CACHE_TIME"], array($pageNumber, $arrFilter))) {
				$this->arResult = $this->getResult($pageNumber);
				$this->IncludeComponentTemplate();
			}

		} catch (Exception $e) {
			ShowError($e->getMessage());
		}
	}
}

?>