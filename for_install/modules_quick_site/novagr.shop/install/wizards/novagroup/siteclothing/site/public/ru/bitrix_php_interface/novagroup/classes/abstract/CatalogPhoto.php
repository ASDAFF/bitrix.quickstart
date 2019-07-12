<?php

abstract class Novagroup_Classes_Abstract_CatalogPhoto extends Novagroup_Classes_Abstract_IBlock
{

    protected $ID, $photoID = 0, $iBlockID = 0;
    protected $arSelectImageries = Array(
        "ID",
        "NAME",
        "PREVIEW_PICTURE",
        "DETAIL_PICTURE",
        "PROPERTY_PHOTOS"
    );
    protected $arSelect = Array(
        "ID",
        "NAME",
        "PREVIEW_PICTURE",
        "DETAIL_PICTURE",
        "PROPERTY_PHOTONAME_COLOR_1",
        "PROPERTY_PHOTO_COLOR_1",
        "PROPERTY_PHOTONAME_COLOR_2",
        "PROPERTY_PHOTO_COLOR_2",
        "PROPERTY_PHOTONAME_COLOR_3",
        "PROPERTY_PHOTO_COLOR_3",
        "PROPERTY_PHOTONAME_COLOR_4",
        "PROPERTY_PHOTO_COLOR_4",
        "PROPERTY_PHOTONAME_COLOR_5",
        "PROPERTY_PHOTO_COLOR_5",
        "PROPERTY_PHOTONAME_COLOR_6",
        "PROPERTY_PHOTO_COLOR_6",
        "PROPERTY_PHOTONAME_COLOR_7",
        "PROPERTY_PHOTO_COLOR_7",
        "PROPERTY_PHOTONAME_COLOR_8",
        "PROPERTY_PHOTO_COLOR_8",
        "PROPERTY_PHOTONAME_COLOR_9",
        "PROPERTY_PHOTO_COLOR_9",
        "PROPERTY_PHOTONAME_COLOR_10",
        "PROPERTY_PHOTO_COLOR_10",
        "PROPERTY_MORE_PHOTO"
    );
    protected $isUseLastResult;

    function __construct($ID, $iBlockID = 0, $photoID = 0, $isImagery = "N")
    {
        $this->ID = $ID;
        $this->iBlockID = $iBlockID;
        $this->photoID = $photoID;
        $this->isImagery = $isImagery;
        $this->checkInstalledModule();
    }

    function getEmptyPhoto()
    {
        return null;
    }

    function setUseLastResult($flag)
    {
        $this->isUseLastResult = (bool) $flag;
    }

    function getFields()
    {
        if ($this->isUseLastResult===true) {
            $getFields = $this->__getFieldsByLastListResult();
            if(isset($getFields['ID']))
            {
                /*
                 * использование этой фишки приводит с запросу с большим количеством джойнов и мускул отказывается выполнять подобный запрос
                 */
                //return $getFields;
            }
        }
        return $this->__getFields();
    }

    function __getFields()
    {
        if ($this->isImagery == "Y") {
            $arSelect = $this->arSelectImageries;
        } else {
            $arSelect = $this->arSelect;
        }

        if ($this->iBlockID > 0) {
            $arFilter = Array(
                "IBLOCK_ID" => IntVal($this->iBlockID),
                "ID" => intval($this->ID)
            );
        } else {
            $arFilter = Array(
                "ID" => intval($this->ID)
            );
        }

        $res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, Array(), $arSelect);

        if ($ob = $res->GetNextElement()) {
            return $arFields = $ob->GetFields();
        }
        return false;
    }

    function __getFieldsByLastListResult()
    {
        return Novagroup_Classes_General_CatalogOffers::getPhotoPropertiesByLastResult($this->ID);
    }

    function __getPhoto()
    {
        $photos = array();
        $arFields = $this->GetFields();
        $arResult["ELEMENT"] = $arFields;

		if(is_array($arFields["PROPERTY_PHOTOS_VALUE"]))
		{
			foreach($arFields["PROPERTY_PHOTOS_VALUE"] as $photo)
			{
				$photos["PHOTO"][] = $photo; break;
			}
		}else{
			//1
			if (is_array($arFields["PROPERTY_PHOTO_COLOR_1_VALUE"]))
				foreach ($arFields["PROPERTY_PHOTO_COLOR_1_VALUE"] as $photo) {
					$photos[$arFields["PROPERTY_PHOTONAME_COLOR_1_VALUE"]][] = $photo;
				}
			if (is_numeric($arFields["PROPERTY_PHOTO_COLOR_1_VALUE"]) and $arFields["PROPERTY_PHOTO_COLOR_1_VALUE"] > 0)
				$photos[$arFields["PROPERTY_PHOTONAME_COLOR_1_VALUE"]][] = $arFields["PROPERTY_PHOTO_COLOR_1_VALUE"];
			//2
			if (is_array($arFields["PROPERTY_PHOTO_COLOR_2_VALUE"]))
				foreach ($arFields["PROPERTY_PHOTO_COLOR_2_VALUE"] as $photo) {
					$photos[$arFields["PROPERTY_PHOTONAME_COLOR_2_VALUE"]][] = $photo;
				}
			if (is_numeric($arFields["PROPERTY_PHOTO_COLOR_2_VALUE"]) and $arFields["PROPERTY_PHOTO_COLOR_2_VALUE"] > 0)
				$photos[$arFields["PROPERTY_PHOTONAME_COLOR_2_VALUE"]][] = $arFields["PROPERTY_PHOTO_COLOR_2_VALUE"];
			//3
			if (is_array($arFields["PROPERTY_PHOTO_COLOR_3_VALUE"]))
				foreach ($arFields["PROPERTY_PHOTO_COLOR_3_VALUE"] as $photo) {
					$photos[$arFields["PROPERTY_PHOTONAME_COLOR_3_VALUE"]][] = $photo;
				}
			if (is_numeric($arFields["PROPERTY_PHOTO_COLOR_3_VALUE"]) and $arFields["PROPERTY_PHOTO_COLOR_3_VALUE"] > 0)
				$photos[$arFields["PROPERTY_PHOTONAME_COLOR_3_VALUE"]][] = $arFields["PROPERTY_PHOTO_COLOR_3_VALUE"];
			//4
			if (is_array($arFields["PROPERTY_PHOTO_COLOR_4_VALUE"]))
				foreach ($arFields["PROPERTY_PHOTO_COLOR_4_VALUE"] as $photo) {
					$photos[$arFields["PROPERTY_PHOTONAME_COLOR_4_VALUE"]][] = $photo;
				}
			if (is_numeric($arFields["PROPERTY_PHOTO_COLOR_4_VALUE"]) and $arFields["PROPERTY_PHOTO_COLOR_4_VALUE"] > 0)
				$photos[$arFields["PROPERTY_PHOTONAME_COLOR_4_VALUE"]][] = $arFields["PROPERTY_PHOTO_COLOR_4_VALUE"];
			//5
			if (is_array($arFields["PROPERTY_PHOTO_COLOR_5_VALUE"]))
				foreach ($arFields["PROPERTY_PHOTO_COLOR_5_VALUE"] as $photo) {
					$photos[$arFields["PROPERTY_PHOTONAME_COLOR_5_VALUE"]][] = $photo;
				}
			if (is_numeric($arFields["PROPERTY_PHOTO_COLOR_5_VALUE"]) and $arFields["PROPERTY_PHOTO_COLOR_5_VALUE"] > 0)
				$photos[$arFields["PROPERTY_PHOTONAME_COLOR_5_VALUE"]][] = $arFields["PROPERTY_PHOTO_COLOR_5_VALUE"];
			//6
			if (is_array($arFields["PROPERTY_PHOTO_COLOR_6_VALUE"]))
				foreach ($arFields["PROPERTY_PHOTO_COLOR_6_VALUE"] as $photo) {
					$photos[$arFields["PROPERTY_PHOTONAME_COLOR_6_VALUE"]][] = $photo;
				}
			if (is_numeric($arFields["PROPERTY_PHOTO_COLOR_6_VALUE"]) and $arFields["PROPERTY_PHOTO_COLOR_6_VALUE"] > 0)
				$photos[$arFields["PROPERTY_PHOTONAME_COLOR_6_VALUE"]][] = $arFields["PROPERTY_PHOTO_COLOR_6_VALUE"];
			//7
			if (is_array($arFields["PROPERTY_PHOTO_COLOR_7_VALUE"]))
				foreach ($arFields["PROPERTY_PHOTO_COLOR_7_VALUE"] as $photo) {
					$photos[$arFields["PROPERTY_PHOTONAME_COLOR_7_VALUE"]][] = $photo;
				}
			if (is_numeric($arFields["PROPERTY_PHOTO_COLOR_7_VALUE"]) and $arFields["PROPERTY_PHOTO_COLOR_7_VALUE"] > 0)
				$photos[$arFields["PROPERTY_PHOTONAME_COLOR_7_VALUE"]][] = $arFields["PROPERTY_PHOTO_COLOR_7_VALUE"];
			//8
			if (is_array($arFields["PROPERTY_PHOTO_COLOR_8_VALUE"]))
				foreach ($arFields["PROPERTY_PHOTO_COLOR_8_VALUE"] as $photo) {
					$photos[$arFields["PROPERTY_PHOTONAME_COLOR_8_VALUE"]][] = $photo;
				}
			if (is_numeric($arFields["PROPERTY_PHOTO_COLOR_8_VALUE"]) and $arFields["PROPERTY_PHOTO_COLOR_8_VALUE"] > 0)
				$photos[$arFields["PROPERTY_PHOTONAME_COLOR_8_VALUE"]][] = $arFields["PROPERTY_PHOTO_COLOR_8_VALUE"];
			//9
			if (is_array($arFields["PROPERTY_PHOTO_COLOR_9_VALUE"]))
				foreach ($arFields["PROPERTY_PHOTO_COLOR_9_VALUE"] as $photo) {
					$photos[$arFields["PROPERTY_PHOTONAME_COLOR_9_VALUE"]][] = $photo;
				}
			if (is_numeric($arFields["PROPERTY_PHOTO_COLOR_9_VALUE"]) and $arFields["PROPERTY_PHOTO_COLOR_9_VALUE"] > 0)
				$photos[$arFields["PROPERTY_PHOTONAME_COLOR_9_VALUE"]][] = $arFields["PROPERTY_PHOTO_COLOR_9_VALUE"];
			//10
			if (is_array($arFields["PROPERTY_PHOTO_COLOR_10_VALUE"]))
				foreach ($arFields["PROPERTY_PHOTO_COLOR_10_VALUE"] as $photo) {
					$photos[$arFields["PROPERTY_PHOTONAME_COLOR_10_VALUE"]][] = $photo;
				}
			if (is_numeric($arFields["PROPERTY_PHOTO_COLOR_10_VALUE"]) and $arFields["PROPERTY_PHOTO_COLOR_10_VALUE"] > 0)
				$photos[$arFields["PROPERTY_PHOTONAME_COLOR_10_VALUE"]][] = $arFields["PROPERTY_PHOTO_COLOR_10_VALUE"];
			if (empty($photos) && !empty($arFields["PREVIEW_PICTURE"])) {
	
				$photos["PHOTO"][0] = $arFields["PREVIEW_PICTURE"];
	
			} elseif (empty($photos) && !empty($arFields["DETAIL_PICTURE"])) {
	
				$photos["PHOTO"][0] = $arFields["DETAIL_PICTURE"];
	
			} elseif (empty($photos) && !empty($arFields["PROPERTY_MORE_PHOTO_VALUE"])) {
	
				if (is_array($arFields["PROPERTY_MORE_PHOTO_VALUE"])) {
					$photos["PHOTO"][0] = $arFields["PROPERTY_MORE_PHOTO_VALUE"][0];
				} else {
					$photos["PHOTO"][0] = $arFields["PROPERTY_MORE_PHOTO_VALUE"];
				}
			}
        }
		
        return $photos;
    }

    function getPhoto()
    {
        $arResult['PHOTOS'] = $photos = $this->__getPhoto();
        $arResult['PHOTO'] = $this->getEmptyPhoto();
        if ($this->photoID > 0 && isset($photos[(int)$this->photoID][0])) {
            $arResult['PHOTO'] = $photos[(int)$this->photoID][0];

        } else {

            foreach ($photos as $photo) {
                $arResult['PHOTO'] = $photo[0];
                break;
            }
        }
        return $arResult;
    }
}