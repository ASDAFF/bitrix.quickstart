<?

define("ROOT_COLLECTION_NAME", "module_gallery_multiupload");
// TODO:  Создать эту коллекцию если она ещё не существует, а лучше создать её при инсталяции, хотя фиг знает как лучше, наверное при инсталяции модуля все же правильнее

CModule::IncludeModule("fileman");
CMedialib::Init();

class CArtDepoGallerySection
{
    static private
        $ml_type = 1,
        $root_collection_id,
        $SORT_default = 500;
        
    public
        $LAST_ERROR = "";
    
    function CArtDepoGallerySection()
    {
        self::$root_collection_id = $this->GetRootCollectionID();
        return self::$root_collection_id;
    }
    
    
    function GetList($arOrder = array("id" => "desc"), $arFilterIn = array())
    {
        $arFilter = array_merge(
            array(
                "ACTIVE" => "Y", // TODO: Это наверное стоит убрать.
                "ML_TYPE" => self::$ml_type, 
                "PARENT_ID" => self::$root_collection_id
            ), 
            $arFilterIn
        );
        $Params = array("arOrder" => $arOrder, "arFilter" => $arFilter);
        $arData = CMedialibCollection::GetList($Params);
        if(!is_array($arData)) 
            $arData = array();
        $arDataNew = array();
        foreach ($arData as $k => $arItem) {
            // IMPORTATN: Дополнительное отсеивание, т.к. в API CMedialibCollection::GetList ошибка - PARENT_ID выполняет поиск по like(%ID%)
            if ( $arFilter["PARENT_ID"] <= 0 || ($arFilter["PARENT_ID"] == $arItem["PARENT_ID"]) ) {
                $arDesc = CArtDepoGalleryUtils::DescriptionUnpack($arItem["DESCRIPTION"]);
                if (!isset($arDesc["SORT"]))
                    $arDesc["SORT"] = self::$SORT_default;
                $arDataNew[$k] = array_merge($arItem, $arDesc);
            }
        }
        // Sort, by SORT virtual field
        CArtDepoGalleryUtils::SortArray($arOrder, $arDataNew);
        
        $rsData = new CDBResult;
        $rsData->InitFromArray($arDataNew);
        return $rsData;
    }
    
    function GetByID($ID)
    {
        if(!$ID)
            return false;
        $arItem = array();
        $arData = CMedialibCollection::GetList(array(
            'arFilter' => array("ID" => $ID),
        ));
        if(is_array($arData) && count($arData) == 1){
            $arItem = array_shift($arData);
        }
        if($arItem["DESCRIPTION"]){    
            $arDesc = CArtDepoGalleryUtils::DescriptionUnpack($arItem["DESCRIPTION"]);
            $arItem = array_merge($arItem, $arDesc);
        }
        return $arItem;
    }
    
    function Update($ID, $arFields)
    {
        if(intval($ID) <= 0 || !is_array($arFields) || empty($arFields)){
            return false;
        }
        $ID = intval($ID);
        
        $arAllowedFields = array('ID', 'NAME', 'DESCRIPTION', 'ACTIVE', 'OWNER_ID', 'PARENT_ID', 'KEYWORDS', 'ML_TYPE');
        
        $arSaveFields = array_merge(
            array('ID' => $ID, "ACTIVE" => "N"),
            $arFields
        );
        
        // UPDATE NAME && DESCRIPTION if needed...
        
        $arItem = self::GetByID($ID, true);
        $arItemNew = array();
        foreach($arItem as $field => $val){
            if(in_array($field, $arAllowedFields) === true){
                $arItemNew[$field] = $val;
            }
        }
        $arSaveFields = array_merge($arItemNew, $arSaveFields);
		return CMedialibCollection::Edit(array(
			'arFields' => $arSaveFields
		));
    }
    
    // Удаляет коллекцию, и всё деревоего потомков
    function Delete($ID)
    {
        if (intVal($ID) <= 0)
            return false;
        $arRes = CMedialib::GetCollectionTree();
        $child_cols = array();
        self::GetAllChildrenID($arRes["arColTree"], $ID, $child_cols);
        return CMedialib::DelCollection($ID, $child_cols);; // коллекция и её потомки
    }
    
    /**
     * Получает ID-шники всех потомков узла c ID=$ID
     * 
     * Нужно передать $arColTree - полное дерево в формате CMedialib::GetCollectionTree
     * и $ID узла от которого нужны потомки
     * в параметр по ссылке $child_cols складываються ID-шники "детишек".
     * @params $child_cols array вида array(id1, id2, id3, ...)
     * Функция рекурсивная, и ничего не возврщает.
     */
    function GetAllChildrenID($arColTree, $ID, &$child_cols)
    {
        // В случае если мы нашли вершину дерева $ID, тогда $ID будет индикатором, и устанавливается в false
        if ($ID) {
            foreach($arColTree as $arCol){
                if ($ID == $arCol["id"]) {
                    self::GetAllChildrenID($arCol["child"], false, $child_cols);
                    break; // not return
                } else {
                    self::GetAllChildrenID($arCol["child"], $ID, $child_cols);
                }
            }
        } else {
            foreach($arColTree as $arCol){
                $child_cols[] = $arCol["id"];
                if(!empty($arCol["child"]))
                    self::GetAllChildrenID($arCol["child"], false, $child_cols);
            }
            
        }
        return false;
    }


    function GetRootCollectionID()
    {
        $rid = self::$root_collection_id;
        if(!$rid){
            $rootCollection = array();
            $res = self::GetList(array("id" => "desc"), array(
                "NAME" => ROOT_COLLECTION_NAME, 
                "PARENT_ID" => false
            ));
            if($rootCollection = $res->Fetch()){
                $rid = $rootCollection["ID"] ? $rootCollection["ID"] : false;
            }else{
                $rid = self::CreateRootCollection();
            }
        }

        return $rid;
    }
    
    function CreateRootCollection()
    {
        $id = CMedialib::EditCollection(array(
            'id' => '',
            'name' => ROOT_COLLECTION_NAME,
            'desc' => '',
            'keywords' => '',
            'parent' => '',
            'site' => LANGUAGE_ID,
            'type' => 1
        ));
        return $id;
    }
}




class CArtDepoGalleryImage
{
    static private
        $SORT_default = 500;
    
    // using CMedialibItem::GetList from medialib.php
    // Implementation of the sort performed by "array_multisort"
    // Return CDBResult
    function GetList($arOrder = array("id" => "desc"), $arFilter)
    {
        if ($arFilter["PARENT_ID"])
            $Params['arCollections'] = (array)$arFilter["PARENT_ID"];
        if ($arFilter["ID"])
            $Params['id'] = $arFilter["ID"];
        // Perfom search only if $arFilter is real
        if($Params){
            $arData = CMedialibItem::GetList($Params);
            foreach ($arData as $k => $arItem) {
                $arDesc = CArtDepoGalleryUtils::DescriptionUnpack($arItem["DESCRIPTION"]);
                if (!isset($arDesc["SORT"]))
                    $arDesc["SORT"] = self::$SORT_default;
                $arData2[$k] = array_merge($arItem, $arDesc);
            }
            $arData = $arData2;
        }
        if (!is_array($arData))
            $arData = array();
        // Sort
        CArtDepoGalleryUtils::SortArray($arOrder, $arData);
        // Return CDBResult
        $rsData = new CDBResult;
        $rsData->InitFromArray($arData);
        return $rsData;
    }
    
    // using self::GetList
    // return Array
    function GetByID($ID)
    {
        if(!$ID)
            return false;
        $arItem = array();
        $rsData = self::GetList(array("id" => "asc"), array(
            'ID' => $ID,
        ));
        if ($arItem = $rsData->GetNext()) if($arItem["DESCRIPTION"]) {
            $arDesc = CArtDepoGalleryUtils::DescriptionUnpack($arItem["DESCRIPTION"]);
            $arItem = array_merge($arItem, $arDesc);
        }
        return $arItem;
    }
    
    // using CMedialib::EditItem from medialib.php
	function Edit($Params)
	{
		$bOpName = $Params['id'] ? 'medialib_edit_item' : 'medialib_new_item';
		$arCols_ = explode(',', $Params['item_collections']);
		$arCols = array();
		for ($i = 0, $l = count($arCols_); $i < $l; $i++)
		{
			if (intVal($arCols_[$i]) > 0 && CMedialib::CanDoOperation($bOpName, $arCols_[$i])) // Check access
				$arCols[] = intVal($arCols_[$i]);
		}
		if (count($arCols) > 0)
		{
			if ($Params['source_type'] == 'PC')
				$Params['path'] = false;
			else if($Params['source_type'] == 'FD')
				$Params['file'] = false;

			$res = CMedialibItem::Edit(array(
				'file' => $Params['file'],
				'path' => $Params['path'],
				'arFields' => array(
					'ID' => $Params['id'],
					'NAME' => $Params['name'],
					'DESCRIPTION' => $Params['desc'],
					'KEYWORDS' => $Params['keywords']
				),
				'arCollections' => $arCols
			));
		}
		return ($res) ? $res : false;
	}
	
	function UpdateSort($ID, $arUpdate)
	{
	    if (!$arUpdate['SORT'] || $ID <= 0)
	        return false;

        $arItem = self::GetByID($ID);
        
        if (!$arItem)
            return false;
        if ($arUpdate["SORT"] == $arItem["SORT"])
            return true;
        if($arItem["DESCRIPTION"])
            $sDesc = preg_replace("/sort=\d+/i", "sort=".$arUpdate["SORT"], $arItem["DESCRIPTION"]);
        else
            $sDesc = "sort=".$arUpdate["SORT"];
        $res = CMedialibItem::Edit(array(
            'file' => false,
            'path' => false,
            'arFields' => array(
                'ID' => $ID,
                'NAME' => $arItem["NAME"],
                'DESCRIPTION' => $sDesc,
                'KEYWORDS' => $arItem['KEYWORDS']
            ),
            'arCollections' => (array)$arItem['COLLECTION_ID']
        ));
        return ($res) ? true : false;
	}
	
	function Delete($ID, $mode = false, $col_id = false)
	{
	    $res = CMedialib::DelItem($ID, $mode == 'current', $col_id);
	    return ($res) ? true : false;
	}
}




class CArtDepoGalleryUtils
{
    static private
        $delimer = "&&";

    function GetSiteLangs()
    {
        $languages = array();
        $rsLang = CLanguage::GetList($by="sort", $order="asc", Array("ACTIVE" => "Y"));
        while ($arLang = $rsLang->Fetch()){
            $languages[] = array(
                "NAME" => $arLang["NAME"],
                "LANGUAGE_ID" => $arLang["LANGUAGE_ID"],
                "CHARSET" => $arLang["CHARSET"],
                "SORT" => $arLang["SORT"],
            );
        }
        return $languages;
    }

    function DescriptionPack($arDesc)
    {
        return string();
    }

    /**
     * Convert item's DESCRIPTION of medialib into an array ready to work with
     *
     * @param $sDesc = "name_ru=name_in_russian&name_en=name_in_english&..."; 
     * @return array("NAME_RU" => "name_in_russian", "NAME_EN" => "name_in_english", ...)
     */
    function DescriptionUnpack($sDesc)
    {
        $arReturn = array();
        foreach (explode(self::$delimer, $sDesc) as $pair) {
            if (preg_match("/^([a-z_]+)=(.+)$/", $pair, $matches)) {
                $arReturn[strtoupper($matches[1])] = $matches[2];
            }
        }
        return $arReturn;
    }

    // Pack NAME in to one string by that pattern: "NAME in Russian / Name in English / ..."
    function PackNamesInStringOrderedByLang($arItem, $arLangs, $glue = " / ")
    {
        $pieces = array();
        foreach ($arLangs as $lan) {
            $key = "NAME_" . strtoupper($lan["LANGUAGE_ID"]);
            if ($arItem[$key])
                $pieces[] = $arItem[$key];
        }
        return implode($glue, $pieces);
    }
    
    function SortArray($arOrder, &$arItems)
    {
        if (empty($arItems) || !is_array($arItems))
            return $arItems;
        $orderEach = each($arOrder);
        $sortBy = strtoupper($orderEach["key"]);
        $sortOrder = strtoupper($orderEach["value"]);
        if ( !($sortBy == "ID" && $sortOrder == "ASC") ) {
            foreach ($arItems as $key => $item) {
                $arSortField_ID[$key] = $item[$sortBy];
                if (array_key_exists($sortBy, $item) !== false)
                    $arSortField[$key] = $item[$sortBy];
            }
            array_multisort($arSortField, ($sortOrder == "ASC") ? SORT_ASC : SORT_DESC, $arSortField_ID, SORT_DESC, $arItems);
        }
    }
}

?>
