<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$cp = $this->__component;
if(is_object($cp))
{
    if(count($arResult["FOLDER_IMAGE"]))
    {
        foreach($arResult["FOLDER_IMAGE"] as $i=>$img)
        {
            $size = getimagesize($_SERVER["DOCUMENT_ROOT"].$img["URL"]["IMAGE"]);
            if($img["SIZE_TYPE"] == "H")
            {
                $new_width = ($size[0]/$size[1]) * $img["SIZE_INT"];
                $new_height = $img["SIZE_INT"];
            }
            else
            {
                $new_height = ($size[0]/$size[1]) * $img["SIZE_INT"];
                $new_width = $img["SIZE_INT"];
            }
            
            $file = CFile::ResizeImageGet($img, array('width' => $new_width, 'height' => $new_height), BX_RESIZE_IMAGE_PROPORTIONAL, true);
            $img["URL"]["IMAGE"] = $file["src"];
            
            $arResult["FOLDER_IMAGE"][$i] = $img;
        }
    }
    
    $cp->arResult = $arResult;
}
?>