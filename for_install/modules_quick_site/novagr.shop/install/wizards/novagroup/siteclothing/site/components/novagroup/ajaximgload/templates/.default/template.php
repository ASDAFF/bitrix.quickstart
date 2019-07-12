<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// initialize default params
if(empty($arParams['ATTRIBUTES']['width']))
	$arParams['ATTRIBUTES']['width'] = 240;
if(empty($arParams['ATTRIBUTES']['height']))
	$arParams['ATTRIBUTES']['height'] = 180;

//
if($arResult !== false)
{
    $this->setFrameMode(true);
	$IMGTAG = '<img';
	
	// set attributes
	$arParams['ATTRIBUTES']['src'] = strip_tags($arResult['src']);
	foreach($arParams['ATTRIBUTES'] as $key => $val)
		if(!empty($val))
			$IMGTAG.= ' '.$key.'="'.$val.'"';
	
	// set micro data
	if(isset($arParams['MICRODATA']))
		foreach($arParams['MICRODATA'] as $key => $val)
			if(!empty($val))
				$IMGTAG.= ' data-'.$key.'="'.$val.'"';
	
	$IMGTAG.= " />";
	echo $IMGTAG;
}else{
	$DIVTAG = '<div class="ajaximgload ajaximgload-imgid-'.$arParams['MICRODATA']['imgid'].' ajaximgload'.$arParams['ATTRIBUTES']['width'].'x'.$arParams['ATTRIBUTES']['height'].'"';
	
	// set attributes
	if(isset($arParams['ATTRIBUTES']))
		$DIVTAG.= " data-attribute='".json_encode($arParams['ATTRIBUTES'])."'";
	// set micro data
	if(isset($arParams['MICRODATA']))
		$DIVTAG.= " data-microdata='".json_encode($arParams['MICRODATA'])."'";
	
	$DIVTAG.= '></div>';
	echo $DIVTAG;
}
?><??>