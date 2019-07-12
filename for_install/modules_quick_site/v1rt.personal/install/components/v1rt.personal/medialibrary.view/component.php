<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if(!CModule::IncludeModule("v1rt.personal")) die();
//убирает запоминание страниц
CPageOption::SetOptionString("main", "nav_page_in_session", "N");
$arResult["MIN_H"] = $arParams["RESIZE_MODE_H"];
$arResult["MIN_W"] = $arParams["RESIZE_MODE_W"];

if($arParams["COUNT_IMAGE"] == 0)
    $arParams["COUNT_IMAGE"] = "";
if($arParams["VARIABLE"] != "" && is_numeric($_REQUEST[$arParams["VARIABLE"]]))
    $arParams["FOLDERS"] = $_REQUEST[$arParams["VARIABLE"]];
//if($arParams["FOLDERS"] != "" && is_numeric($arParams["FOLDERS"]))
if(count($arParams["FOLDERS"]) > 0) // изменил условие (старое условие - выше)
{
    global $DB;
    if($arParams["TITLE"] == "Y")
    {
        $title = CMediaComponents::getTitle($arParams["FOLDERS"]);
        if($arParams["ADD_TITLE"] != "")
            $APPLICATION->SetTitle($arParams["ADD_TITLE"]." ".$title["NAME"]);
        else
            $APPLICATION->SetTitle($title["NAME"]);
        $arResult["NAME"] = $title["NAME"];
    }
    //Вытаскиваем ID картинок этой папки
    $res = CMediaComponents::getImages($arParams["FOLDERS"], $arParams["RANDOM"], $arParams["COUNT_IMAGE"]);

    if($arParams["PAGE_NAV_MODE"] == "Y")
    {
        //Постраничная навигация
        $arParams["PAGER_SHOW_ALWAYS"] = ($arParams["PAGER_SHOW_ALWAYS"] == "N" ? false : true);
        $arParams["PAGER_SHOW_ALL"] = ($arParams["PAGER_SHOW_ALL"] == "N" ? false : true);

        $arNavParams = array(
            "nPageSize" => $arParams["ELEMENT_PAGE"],
            "bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
            "bShowAll" => $arParams["PAGER_SHOW_ALL"],
        );
        $arNavigation = $res->GetNavParams($arNavParams);
        $res->NavStart($arParams["ELEMENT_PAGE"]);
        $res->bShowAll = $arNavParams["bShowAll"];
        $arResult["NAV_STRING"] = $res->GetPageNavStringEx($navComponentObject, $arParams["PAGER_TITLE"], $arParams["PAGER_TEMPLATE"], $arParams["PAGER_SHOW_ALWAYS"]);
        $arResult["NAV_RESULT"] = $res;
    }

    if($this->StartResultCache(false, $arNavigation))
    {
        if($count = @mysql_num_rows($res->result))
        {
            $iteration = 1;
            while($img = $res->Fetch())
            {
                //Количество изображений
                if(is_numeric($arParams["COUNT_IMAGE"]))
                    if($iteration > $arParams["COUNT_IMAGE"])
                        break;
                $arImg = CFile::GetByID($img["SOURCE_ID"]);
                $arResult["FOLDER_IMAGE"][$iteration] = $arImg->Fetch();
                //URL картинки
                $arResult["FOLDER_IMAGE"][$iteration]["URL"]["FILE"] = "/upload/".$arResult["FOLDER_IMAGE"][$iteration]["SUBDIR"]."/".$arResult["FOLDER_IMAGE"][$iteration]["FILE_NAME"];
                $arResult["FOLDER_IMAGE"][$iteration]["URL"]["IMAGE"] = $arResult["FOLDER_IMAGE"][$iteration]["URL"]["FILE"];
                //Режим уменьшения изображений
                if($arParams["RESIZE_MODE"] != "N")
                {
                    if($arParams["RESIZE_MODE_W"] != "" && $arParams["RESIZE_MODE_H"] != "")
                    {
                        if($arParams["RESIZE_MODE"] == "F")
                        {
                            $size = getimagesize($_SERVER["DOCUMENT_ROOT"]."/upload/".$arResult["FOLDER_IMAGE"][$iteration]["SUBDIR"]."/".$arResult["FOLDER_IMAGE"][$iteration]["FILE_NAME"]);
                            if($size[0]/$arParams["RESIZE_MODE_W"] >= $size[1]/$arParams["RESIZE_MODE_H"])
                            {
                                $arResult["FOLDER_IMAGE"][$iteration]["SIZE_TYPE"] = "H";
                                $arResult["FOLDER_IMAGE"][$iteration]["SIZE_INT"] = $arParams["RESIZE_MODE_H"];
                                $nSize = ' height="'.$arParams["RESIZE_MODE_H"].'"';
                            }
                            else
                            {
                                $arResult["FOLDER_IMAGE"][$iteration]["SIZE_TYPE"] = "W";
                                $arResult["FOLDER_IMAGE"][$iteration]["SIZE_INT"] = $arParams["RESIZE_MODE_W"];
                                $nSize = ' width="'.$arParams["RESIZE_MODE_W"].'"';
                            }

                            $arResult["FOLDER_IMAGE"][$iteration]["SIZE"] = $nSize;
                        }
                        elseif($arParams["RESIZE_MODE"] == "P")
                        {
                            $file = CFile::ResizeImageGet($arResult["FOLDER_IMAGE"][$iteration], array('width'=>$arParams["RESIZE_MODE_W"], 'height'=>$arParams["RESIZE_MODE_H"]), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true);
                            if($arResult["MIN_H"] > $file["height"])
                                $arResult["MIN_H"] = $file["height"];
                            if($arResult["MIN_W"] > $file["width"])
                                $arResult["MIN_W"] = $file["width"];
                            $arResult["FOLDER_IMAGE"][$iteration]["URL"]["IMAGE"] = $file["src"];
                        }
                    }
                }
                //Случайное число
                $arResult["FOLDER_IMAGE"][$iteration]["RAND_ID"] = rand(00000, 99999);
                $arResult["FOLDER_IMAGE"][$iteration]["RAND_ID"] = "link_".$arResult["FOLDER_IMAGE"][$iteration]["RAND_ID"];
                /**
                 * Update 2015-01-05 version 2.1.9
                 */
                if ($img["DESCRIPTION"] == "" || $img["DESCRIPTION"] == " ") {
                    $arResult["DESCRIPTION"][$img["SOURCE_ID"]] = $img["NAME"];
                } else {
                    $arResult["DESCRIPTION"][$img["SOURCE_ID"]] = $img["DESCRIPTION"];
                }
                $iteration++;
            }
        }
        else
        {
            $arResult["ERRORS"][] = GetMessage("NO_IMAGE_FOLDER");
            $this->AbortResultCache();
        }

        $this->IncludeComponentTemplate();
    }
}
?>