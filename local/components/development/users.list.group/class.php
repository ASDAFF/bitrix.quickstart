<?
use \Bitrix\Main\UserTable;
use Bitrix\Main\Entity;
use Site\Main\Registry;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
\CBitrixComponent::includeComponentClass("site:users.list");

class UsersListModuleGroup extends UsersListModule
{

    /**
     * Получение пользователей
     *
     * @param integer $pageNumber Номер текущей страницы
     * @return array
     */

    protected function getUsers($pageNumber) {
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
            $order = array("CNT" => "DESC");
        }
        
        $result = array();
        $groupBy = $this->arParams["GROUP_BY"] ? $this->arParams["GROUP_BY"] : "NAME";
        
        try {
            $arSelect[] = 'CNT';
            $rUserTable = UserTable::getList(Array(
                "select" => $arSelect,
                "filter" => $arrFilter,
                "order" => $order,
                "group" => array($groupBy),
                "limit" => $this->arParams["USERS_COUNT"],
                "data_doubling" => false,
                'runtime' => array(
                    new Entity\ExpressionField('CNT', "COUNT(%s)", array('PERSONAL_CITY'))
                )
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

            $result['NAV_PARAMS'] = Registry::get('NAV_PARAMS');

        } catch (Exception $e) {
            ShowError($e->getMessage());
            $this->AbortResultCache();
        }

        return $result;
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