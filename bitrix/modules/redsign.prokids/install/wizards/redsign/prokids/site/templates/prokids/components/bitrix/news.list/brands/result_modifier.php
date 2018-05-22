<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if($arParams['BRAND_CODE']=='' || $arParams['SECTIONS_CODE']=='')
	return;

if(!function_exists('RSGoPro_PrepareArray'))
{
	function RSGoPro_PrepareArray($arData=array(),$arCharacters=array(),$BRAND_CODE='')
	{
		$newArr = array();
		if( is_array($arData) && count($arData)>0 && is_array($arCharacters) && count($arCharacters)>0 && $BRAND_CODE!='' )
		{
			foreach($arCharacters as $val) 
			{
				foreach($arData as $id => $arItem)
				{
					$digital = substr($arItem['DISPLAY_PROPERTIES'][$BRAND_CODE]['DISPLAY_VALUE'],0,1);
					if($val==$digital)
					{
						$newArr[$val]['ITEMS'][] = $arItem;
					}
				}
			}
		}
		return $newArr;
	}
}

// default arrays
$digital = '0 1 2 3 4 5 6 7 8 9';
$digital_default_array = explode(' ',$digital);

$eng = 'A B C D E F G H I J K L M N O P Q R S T U V W X Y Z';
$eng_default_array = explode(' ',$eng);

$rus = GetMessage('RUS_SYMBOLS');
$rus_default_array = explode('  ',$rus);

foreach($arResult['ITEMS'] as $arItem)
{
	$rest[] = substr($arItem['DISPLAY_PROPERTIES'][$arParams['BRAND_CODE']]['DISPLAY_VALUE'],0,1);
}
$arLetters = array_unique($rest); // array with first characters

// forming character arrays
	// prepare digital array
	if( is_array($digital_default_array) && count($digital_default_array)>0 )
	{
		foreach($digital_default_array as $BUKVA) 
		{
			if(in_array($BUKVA, $arLetters))
			{
				$digital_array[] = $BUKVA;
			}
		}
		$arResult['DIGITAL'] = array();
		$arResult['DIGITAL'] = RSGoPro_PrepareArray($arResult['ITEMS'],$digital_array,$arParams['BRAND_CODE']);
	}

	// prepare eng array
	if( is_array($eng_default_array) && count($eng_default_array)>0 )
	{
		foreach($eng_default_array as $BUKVA) 
		{
			if(in_array($BUKVA, $arLetters))
			{
				$eng_array[] = $BUKVA;
			}
		}
		$arResult['ENG_LETTER'] = array();
		$arResult['ENG_LETTER'] = RSGoPro_PrepareArray($arResult['ITEMS'],$eng_array,$arParams['BRAND_CODE']);
	}
	
	// prepare rus array
	if( is_array($rus_default_array) && count($rus_default_array)>0 )
	{
		foreach($rus_default_array as $BUKVA) 
		{
			if(in_array($BUKVA, $arLetters))
			{
				$rus_array[] = $BUKVA;
			}
		}
		$arResult['RUS_LETTER'] = array();
		$arResult['RUS_LETTER'] = RSGoPro_PrepareArray($arResult['ITEMS'],$rus_array,$arParams['BRAND_CODE']);
	}