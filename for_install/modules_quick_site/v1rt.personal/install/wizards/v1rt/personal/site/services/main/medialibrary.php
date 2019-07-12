<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

CModule::IncludeModule("fileman");
CMedialib::Init();
$wizard =& $this->GetWizard();

$arExCols = array();
$galParent = null;
$galSlider = null;
$galPhoto = null;
$arExCols[0] = null;
$arExCols[1] = null;

$ar = CMedialibCollection::GetList(array('arFilter' => array('NAME' => GetMessage("MEDIA_PARENT"))));
if($ar[0]["ID"] > 0)
{
    $galParent = $ar[0]["ID"];
    $r = CMedialibCollection::GetList(array('arFilter' => array('PARENT_ID' => $galParent)));
    $c = count($r);
    for($i = 0; $i <= $c - 1; $i++)
    {
        if($r[$i]["NAME"] == GetMessage("MEDIA_SLIDER"))
            $galSlider = $r[$i]["ID"];
        elseif($r[$i]["NAME"] == GetMessage("MEDIA_PHOTO"))
        {
            $galPhoto = $r[$i]["ID"];
            $r_ = CMedialibCollection::GetList(array('arFilter' => array('PARENT_ID' => $galPhoto)));
            $c_ = count($r_);
            for($i_ = 0; $i_ <= $c_ - 1; $i_++)
            {
                if($r_[$i_]["NAME"] == GetMessage('MEDIA_MOSCOW'))
                    $arExCols[0] = $r_[$i_]["ID"];
                elseif($r_[$i_]["NAME"] == GetMessage('MEDIA_SPB'))
                    $arExCols[1] = $r_[$i_]["ID"];
            }
        }
    }
}

$source_base = dirname(__FILE__);
$documentRoot = rtrim(str_replace(Array("\\\\", "//", "\\"), Array("\\", "/", "/"), $_SERVER["DOCUMENT_ROOT"]), "\\/");
$source_base = substr($source_base, strLen($documentRoot));
$source_base = str_replace(array("\\", "//"), "/", "/".$source_base."/");

if(is_null($galParent))
{
    $galParent = CMedialib::EditCollection(array(
        'name' => GetMessage('MEDIA_PARENT'),
        'desc' => GetMessage('MEDIA_PARENT_DESC'),
        'keywords' => '',
        'parent' => 0,
        'type' => 1
    ));
}
//Создаем первый уровень галерею для слайдера
if(is_null($galSlider))
{
    $galSlider = CMedialib::EditCollection(array(
        'name' => GetMessage('MEDIA_SLIDER'),
        'desc' => GetMessage('MEDIA_SLIDER_DESC'),
        'keywords' => '',
        'parent' => $galParent,
        'type' => 1
    ));
}
if(is_null($galPhoto))
{
    $galPhoto = CMedialib::EditCollection(array(
        'name' => GetMessage('MEDIA_PHOTO'),
        'desc' => GetMessage('MEDIA_PHOTO_DESC'),
        'keywords' => '',
        'parent' => $galParent,
        'type' => 1
    ));
}
$arCollections = array(
	array('name' => GetMessage('MEDIA_MOSCOW'), 'desc' => GetMessage('MEDIA_MOSCOW_DESC')),
	array('name' => GetMessage('MEDIA_SPB'), 'desc' => GetMessage('MEDIA_SPB_DESC'))
);

for($i = 0, $l = count($arCollections); $i < $l; $i++)
{
    if(is_null($arExCols[$i]))
    {
    	$arExCols[$i] = CMedialib::EditCollection(array(
    		'name' => $arCollections[$i]['name'],
    		'desc' => $arCollections[$i]['desc'],
    		'keywords' => '',
    		'parent' => $galPhoto,
    		'type' => 1
    	));
    }
}

if($wizard->GetVar("rewriteMedia") == "Y")
{
    $arItems = array(
    	array('fname' => 'moscow/1.jpg', 'name' => GetMessage('MEDIA_MOSCOW_1'), 'ex_cols' => array(0)),
    	array('fname' => 'moscow/2.jpg', 'name' => GetMessage('MEDIA_MOSCOW_2'), 'ex_cols' => array(0)),
    	array('fname' => 'moscow/3.jpg', 'name' => GetMessage('MEDIA_MOSCOW_3'), 'ex_cols' => array(0)),
    	array('fname' => 'moscow/4.jpg', 'name' => GetMessage('MEDIA_MOSCOW_4'), 'ex_cols' => array(0)),
    	array('fname' => 'moscow/5.jpg', 'name' => GetMessage('MEDIA_MOSCOW_5'), 'ex_cols' => array(0)),
    	array('fname' => 'moscow/6.jpg', 'name' => GetMessage('MEDIA_MOSCOW_6'), 'ex_cols' => array(0)),
    	array('fname' => 'moscow/7.jpg', 'name' => GetMessage('MEDIA_MOSCOW_7'), 'ex_cols' => array(0)),
    	array('fname' => 'moscow/8.jpg', 'name' => GetMessage('MEDIA_MOSCOW_8'), 'ex_cols' => array(0)),
    	array('fname' => 'moscow/9.jpg', 'name' => GetMessage('MEDIA_MOSCOW_9'), 'ex_cols' => array(0)),
    	array('fname' => 'moscow/10.jpg', 'name' => GetMessage('MEDIA_MOSCOW_10'), 'ex_cols' => array(0)),
        array('fname' => 'spb/1.jpg', 'name' => GetMessage('MEDIA_SPB_1'), 'ex_cols' => array(1)),
    	array('fname' => 'spb/2.jpg', 'name' => GetMessage('MEDIA_SPB_2'), 'ex_cols' => array(1)),
    	array('fname' => 'spb/3.jpg', 'name' => GetMessage('MEDIA_SPB_3'), 'ex_cols' => array(1)),
    	array('fname' => 'spb/4.jpg', 'name' => GetMessage('MEDIA_SPB_4'), 'ex_cols' => array(1)),
    	array('fname' => 'spb/5.jpg', 'name' => GetMessage('MEDIA_SPB_5'), 'ex_cols' => array(1)),
    	array('fname' => 'spb/6.jpg', 'name' => GetMessage('MEDIA_SPB_6'), 'ex_cols' => array(1)),
    	array('fname' => 'spb/7.jpg', 'name' => GetMessage('MEDIA_SPB_7'), 'ex_cols' => array(1)),
    	array('fname' => 'spb/8.jpg', 'name' => GetMessage('MEDIA_SPB_8'), 'ex_cols' => array(1)),
    	array('fname' => 'spb/9.jpg', 'name' => GetMessage('MEDIA_SPB_9'), 'ex_cols' => array(1)),
    	array('fname' => 'spb/10.jpg', 'name' => GetMessage('MEDIA_SPB_10'), 'ex_cols' => array(1)),
        array('fname' => 'slider/1.jpg', 'name' => GetMessage('MEDIA_SLIDER_1'), 'ex_cols' => $galSlider),
    	array('fname' => 'slider/2.jpg', 'name' => GetMessage('MEDIA_SLIDER_2'), 'ex_cols' => $galSlider),
    	array('fname' => 'slider/3.jpg', 'name' => GetMessage('MEDIA_SLIDER_3'), 'ex_cols' => $galSlider),
    	array('fname' => 'slider/4.jpg', 'name' => GetMessage('MEDIA_SLIDER_4'), 'ex_cols' => $galSlider),
    	array('fname' => 'slider/5.jpg', 'name' => GetMessage('MEDIA_SLIDER_5'), 'ex_cols' => $galSlider),
    );
    
    $count = count($arItems);
    for($i = 0; $i < $l = count($arItems); $i++)
    {
        $path = $source_base.'files/'.$arItems[$i]['fname'];
       	$arCols = array();
        if(is_array($arItems[$i]['ex_cols']))
        {
       	    for ($j = 0, $n = count($arItems[$i]['ex_cols']); $j < $n; $j++)
      		    $arCols[] = $arExCols[$arItems[$i]['ex_cols'][$j]];
        }
        else
        {
            $arCols[] = $arItems[$i]['ex_cols'];
        }
            
        CMedialibItem::Edit(array(
            'path' => $path,
      		'arFields' => array(
                'NAME' => $arItems[$i]['name'],
     			'DESCRIPTION' => '',
     			'KEYWORDS' => ''
      		),
      		'arCollections' => $arCols
       	));
    }
}

CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID."_".WIZARD_SITE_ID."/header.php", array("MEDIA_FOLDER_SLIDER" => $galSlider));
CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID."_".WIZARD_SITE_ID."/header.php", array("MEDIA_FOLDER_MOSCOW" => $arExCols[0]));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."gallery/index.php", array("PHOTO_GALLERY_ID" => $galPhoto));
?>