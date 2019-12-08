<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CModule::IncludeModule("fileman");
CMedialib::Init();
$path = $this->GetPath();
    $arResult[SCRIPTS]=Array($path."/fancy/source/jquery.fancybox.pack.js", $path."/fancy/source/helpers/jquery.fancybox-buttons.js", $path."/fancy/source/helpers/jquery.fancybox-media.js", $path."/fancy/source/helpers/jquery.fancybox-thumbs.js", $path."/fancy/lib/jquery.mousewheel-3.0.6.pack.js");
    $arResult[CSS]=Array(
                         $path."/fancy/source/jquery.fancybox.css?v=2.0.6",
                         $path."/fancy/source/helpers/jquery.fancybox-buttons.css?v=1.0.2",
                         $path."/fancy/source/helpers/jquery.fancybox-thumbs.css?v=2.0.6",
                         $path."/fancy/source/helpers/jquery.fancybox-thumbs.css?v=2.0.6"
                         );
    foreach($arParams[CHOSEN_COLLECTIONS] as $colkey=>$colvalue)
           {
           $arCol = CMedialibCollection::GetList(array('arFilter' => array('ACTIVE' => 'Y')));
           foreach($arCol as $collection)
                  {

                  if($collection[ID]==$colvalue)
                        {
                        $arResult[COLLECTIONS][$colvalue][NAME]=$collection[NAME];
                        }
                  }


           $params = array("arCollections"=>array($colvalue));
           $items = CMedialibItem::GetList($params);

           $arResult[COLLECTIONS][$colvalue][ICON]=$items[0]['THUMB_PATH'];
           $i=0;
           foreach ($items as $key_item =>$item) 
                  {
                  $arResult[COLLECTIONS][$colvalue][ITEMS][$i][NAME]=$item[NAME];
                  $arResult[COLLECTIONS][$colvalue][ITEMS][$i][PATH]=$item[PATH];
                  $arResult[COLLECTIONS][$colvalue][ITEMS][$i][THUMB_PATH]=$item[THUMB_PATH];
                  $i++;
                  }

            }
             $this->SetResultCacheKeys(array(
                        "ID",
                        "SCRIPTS",
                        "CSS",

                ));

	$this->IncludeComponentTemplate();

?>