<?
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\UserTable;
use \Bitrix\Main\Application;
use Site\Main\Registry;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class UsersListModule extends CBitrixComponent
{

	/**
	 * Возвраещает текущий счетчик компонентов на странице сайта, в которых есть постраничная навигация + 1
	 *
	 * @return int $NavNum
	 */
	protected function getNavNum() {
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
		$arParams["CACHE_TIME"] = (int)$arParams["CACHE_TIME"];
		$arParams["FIELDS"] = array_filter($arParams["FIELDS"]);

		return $arParams;
	}

	/**
	 * Получение реквеста
	 * @return \Bitrix\Main\HttpRequest
	 */

	protected function getRequest() {
		$app = Application::getInstance();
		$context = $app->getContext();
		$request = $context->getRequest();
		return $request;
	}

	/**
	 * Получение фильтра
	 * @return array
	 */

	protected function getFilter() {
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

	protected function getPagenation($pageNumber, $rs) {
		$limit = $rs->SelectedRowsCount();
		$pageElementCount = $this->arParams["PAGE_ELEMENT_COUNT"];
		$rs->NavStart($pageElementCount, false, $pageNumber);
		$rs->NavPageCount = ceil($limit / $pageElementCount);
		$rs->NavPageNomer = $pageNumber;
		$rs->NavPageSize = $pageElementCount;
		$rs->NavRecordCount = $limit;
		$rs->bShowAll = false;
		$res = $rs->GetPageNavStringEx($navComponentObject, "", $this->arParams["PAGER_TEMPLATE"]);
		Registry::set('NAV_PARAMS', $navComponentObject->arResult);
		return $res;
	}

	/**
	 * Получение пользователей
	 *
	 * @param integer $pageNumber Номер текущей страницы
	 * @return array
	 */

	protected function getUsers($pageNumber) {
		$arrFilter = $this->getFilter();
		$arSelect = array(
			'FIELDS' => array_merge(array('ID'), $this->arParams["FIELDS"]),
			'SELECT' => $this->arParams["UFIELDS"],
			'NAV_PARAMS' => $this->arParams["NAV_PARAMS"],
		);

		if( empty($arSelect) ) {
			$arSelect = array("*");
		}

        if($this->arParams["SORT_BY"] ) {
            $sortBy = $this->arParams["SORT_BY"];
        }
		if( $this->arParams["SORT_ORDER"] ){
			$sortOrder = $this->arParams["SORT_ORDER"];
		}
		$result = array();

		try {
			$rsUsers = CUser::GetList(
				($by = $sortBy),
				($order = $sortOrder),
				$arrFilter,
				$arSelect
			);

            // Постраничная навигация
            if($this->arParams["SHOW_PAGER"] == "Y") {
                $result['NAV_RESULT'] = $this->getPagenation($pageNumber, $rsUsers);
            }

			$result['NAV_PARAMS'] = Registry::get('NAV_PARAMS');

			$arCountries = \GetCountryArray();
			// пользователи
			while ($arUser = $rsUsers->Fetch()) {
                if($this->arParams["LIST_LINK_DETAIL"] == "Y") {
                    $arUser["PATH"] = $this->arParams["SEF_FOLDER"] . $arUser["ID"] . "/";
                }

				// Получаем фото
				if( !empty($arUser['PERSONAL_PHOTO']) ){
					$arUser['PERSONAL_PHOTO'] = \CFile::ResizeImageGet($arUser['PERSONAL_PHOTO'], array('width' => 100, 'height' => 100));
				}

				// Получаем название страны по коду
				if( !empty($arUser['PERSONAL_COUNTRY']) ){
					$countryRefId = array_search($arUser['PERSONAL_COUNTRY'], $arCountries['reference_id']);
					$arUser['PERSONAL_COUNTRY'] = $arCountries['reference'][$countryRefId];
				}

				$result["USERS"][$arUser['ID']] = $arUser;
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
		$arResult["NAV_PARAMS"] = $arUsers["NAV_PARAMS"];
		$arResult['PAGE_NUMBER'] = $pageNumber;
		return $arResult;
	}

	/**
	 * Получение номера текущей страницы
	 *
	 * @return integer $pageNumber
	 */

	protected function getPageNumber() {
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