<?if(!defined('B_PROLOG_INCLUDED')||B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;

global $alfaCTemplate, $alfaCSortType, $alfaCSortToo, $alfaCOutput;

$com_uniq_ = 'ALFASORTER';

$action_name = $arParams['ALFA_ACTION_PARAM_NAME'];
$action_name_value = $arParams['ALFA_ACTION_PARAM_VALUE'];
$backUrl = $APPLICATION->GetCurPageParam('',array($action_name,$action_name_value));

$arParams['BACK_URL'] = $backUrl;
$arParams['ACTION_CHANGE_TEMPLATE'] = 'ctemplate';
$arParams['ACTION_CHANGE_SORT'] = 'csort';
$arParams['ACTION_CHANGE_OUTPUT'] = 'coutput';

/* posting part */
if($_REQUEST[$action_name]==$arParams['ACTION_CHANGE_TEMPLATE']) {//_______ change template
	$val = htmlspecialchars($_REQUEST[$action_name_value]);
	$_SESSION[$com_uniq_]['ALFASORTER_CTEMPLATE'] = $val;
	LocalRedirect( $backUrl );
} elseif($_REQUEST[$action_name]==$arParams['ACTION_CHANGE_SORT']) {//_______ change sort type
	$val = htmlspecialchars($_REQUEST[$action_name_value]);
	$_SESSION[$com_uniq_]['ALFASORTER_CSORT'] = $val;
	LocalRedirect( $backUrl );
} elseif($_REQUEST[$action_name]==$arParams['ACTION_CHANGE_OUTPUT']) {//_______ change elements on page
	$val = htmlspecialchars($_REQUEST[$action_name_value]);
	$_SESSION[$com_uniq_]['ALFASORTER_COUTPUT'] = $val;
	LocalRedirect( $backUrl );
}

/* work with arResult (check data) */
$arResult['CTEMPLATE'] = array();
$arResult['CSORTING'] = array();
$arResult['COUTPUT'] = array();

// template
if($arParams['ALFA_CHOSE_TEMPLATES_SHOW']=='Y')
{
	$alfaCTemplate = $arParams['ALFA_DEFAULT_TEMPLATE'];
	for($i=0;$i<$arParams['ALFA_CNT_TEMPLATES'];$i++)
	{
		$addParam = $arParams['ALFA_ACTION_PARAM_NAME'].'='.$arParams['ACTION_CHANGE_TEMPLATE'].'&'.$arParams['ALFA_ACTION_PARAM_VALUE'].'='.$arParams['ALFA_CNT_TEMPLATES_NAME_'.$i];
		$arResult['CTEMPLATE'][] = array(
			'NAME_LANG' => $arParams['ALFA_CNT_TEMPLATES_'.$i],
			'VALUE' => $arParams['ALFA_CNT_TEMPLATES_NAME_'.$i],
			'USING' => 'N',
			'URL' => $APPLICATION->GetCurPageParam($addParam,array($arParams['ALFA_ACTION_PARAM_NAME'],$arParams['ALFA_ACTION_PARAM_VALUE'])),
		);
	}
}
// sort
if($arParams['ALFA_SORT_BY_SHOW']=='Y'){
	foreach($arParams['ALFA_SORT_BY_NAME'] as $key2 => $sort){
		$addParam1 = $arParams['ALFA_ACTION_PARAM_NAME'].'='.$arParams['ACTION_CHANGE_SORT'].'&'.$arParams['ALFA_ACTION_PARAM_VALUE'].'='.$sort.'_asc';
		$addParam2 = $arParams['ALFA_ACTION_PARAM_NAME'].'='.$arParams['ACTION_CHANGE_SORT'].'&'.$arParams['ALFA_ACTION_PARAM_VALUE'].'='.$sort.'_desc';
		if(strpos(strtolower($sort), 'price')){
			$group = 'price';
		}
		else{
			$group = $sort;
		}
		$arResult['CSORTING'][] = array(
			'NAME_LANG' => GetMessage('ALFA_MSG_SORT_BY_'.$group.'_asc'),
			'VALUE' => $sort.'_asc',
			'GROUP' => $group,
			'DIRECTION' => 'asc',
			'USING' => 'N',
			'URL' => $APPLICATION->GetCurPageParam($addParam1,array($arParams['ALFA_ACTION_PARAM_NAME'],$arParams['ALFA_ACTION_PARAM_VALUE'])),
			'URL2' => $APPLICATION->GetCurPageParam($addParam2,array($arParams['ALFA_ACTION_PARAM_NAME'],$arParams['ALFA_ACTION_PARAM_VALUE'])),
		);
		$arResult['CSORTING'][] = array(
			'NAME_LANG' => GetMessage('ALFA_MSG_SORT_BY_'.$group.'_desc'),
			'VALUE' => $sort.'_desc',
			'GROUP' => $group,
			'DIRECTION' => 'desc',
			'USING' => 'N',
			'URL' => $APPLICATION->GetCurPageParam($addParam2,array($arParams['ALFA_ACTION_PARAM_NAME'],$arParams['ALFA_ACTION_PARAM_VALUE'])),
			'URL2' => $APPLICATION->GetCurPageParam($addParam1,array($arParams['ALFA_ACTION_PARAM_NAME'],$arParams['ALFA_ACTION_PARAM_VALUE'])),
		);
	}
}
// output
if($arParams['ALFA_OUTPUT_OF_SHOW']=='Y')
{
	$alfaCOutput = $arParams['ALFA_OUTPUT_OF_DEFAULT'];
	foreach($arParams['ALFA_OUTPUT_OF'] as $key3 => $output)
	{
		if($output!='')
		{
			$addParam = $arParams['ALFA_ACTION_PARAM_NAME'].'='.$arParams['ACTION_CHANGE_OUTPUT'].'&'.$arParams['ALFA_ACTION_PARAM_VALUE'].'='.$output;
			$arResult['COUTPUT'][] = array(
				'NAME_LANG' => $output,
				'VALUE' => $output,
				'USING' => 'N',
				'URL' => $APPLICATION->GetCurPageParam($addParam,array($arParams['ALFA_ACTION_PARAM_NAME'],$arParams['ALFA_ACTION_PARAM_VALUE'])),
			);
		}
	}
	if($arParams['ALFA_OUTPUT_OF_SHOW_ALL']=='Y')
	{
		$addParam = $arParams['ALFA_ACTION_PARAM_NAME'].'='.$arParams['ACTION_CHANGE_OUTPUT'].'&'.$arParams['ALFA_ACTION_PARAM_VALUE'].'=1000000';
		$arResult['COUTPUT'][] = array(
			'NAME_LANG' => GetMessage('ALFA_MSG_OUTPUT_ALL'),
			'VALUE' => 1000000,
			'USING' => 'N',
			'URL' => $APPLICATION->GetCurPageParam($addParam,array($arParams['ALFA_ACTION_PARAM_NAME'],$arParams['ALFA_ACTION_PARAM_VALUE'])),
		);
	}
}
//

/* set global params */
// template
$arResult['USING'] = array();
if($arParams['ALFA_CHOSE_TEMPLATES_SHOW']=='Y')
{
	$default_template_id = 0;
	$using = false;
	foreach($arResult['CTEMPLATE'] as $key1 => $template)
	{
		if($template['VALUE']==$arParams['ALFA_DEFAULT_TEMPLATE']){
			$default_template_id = $key1;
		}
		if($template['VALUE']==$_SESSION[$com_uniq_]['ALFASORTER_CTEMPLATE'])
		{
			$alfaCTemplate = $template['VALUE'];
			$arResult['CTEMPLATE'][$key1]['USING'] = 'Y';
			$arResult['USING']['CTEMPLATE'] = array(
				'KEY' => $key1,
				'ARRAY' => $template,
			);
			$using = true;
			break;
		}
	}
	if(!$using)
	{
		$arResult['CTEMPLATE'][$default_template_id]['USING'] = 'Y';
		$arResult['USING']['CTEMPLATE'] = array(
			'KEY' => $default_template_id,
			'ARRAY' => $arResult['CTEMPLATE'][$default_template_id],
		);
	}
}
// sort
if($arParams['ALFA_SORT_BY_SHOW']=='Y')
{
	$default_sort_id = 0;
	$using = false;
	
	foreach($arResult['CSORTING'] as $key2 => $sort){
		if($sort['VALUE']==$arParams['ALFA_SORT_BY_DEFAULT']){
			$tmp = explode('_', $arParams['ALFA_SORT_BY_DEFAULT']);
			$alfaCSortToo = end($tmp);
			unset($tmp[(count($tmp)-1)]);
			$alfaCSortType = implode('_', $tmp);
			$default_sort_id = $key2;
		}
		if($sort['VALUE']==$_SESSION[$com_uniq_]['ALFASORTER_CSORT']){
			$tmp2 = explode('_', $sort['VALUE']);
			$alfaCSortToo = end($tmp2);
			unset($tmp2[(count($tmp2)-1)]);
			$alfaCSortType = implode('_', $tmp2);
			$arResult['CSORTING'][$key2]['USING'] = 'Y';
			$arResult['USING']['CSORTING'] = array(
				'KEY' => $key2,
				'ARRAY' => $sort,
			);
			$using = true;
			break;
		}
	}
	if(!$using){
		$arResult['CSORTING'][$default_sort_id]['USING'] = 'Y';
		$arResult['USING']['CSORTING'] = array(
			'KEY' => $default_sort_id,
			'ARRAY' => $arResult['CSORTING'][$default_sort_id],
		);
	}
}
// ouput
if($arParams['ALFA_OUTPUT_OF_SHOW']=='Y')
{
	$default_output_id = 0;
	$using = false;
	foreach($arResult['COUTPUT'] as $key3 => $output)
	{
		if($output['VALUE']==$arParams['ALFA_OUTPUT_OF_DEFAULT']) $default_output_id = $key3;
		if($output['VALUE']==$_SESSION[$com_uniq_]['ALFASORTER_COUTPUT'])
		{
			$alfaCOutput = $output['VALUE'];
			$arResult['COUTPUT'][$key3]['USING'] = 'Y';
			$arResult['USING']['COUTPUT'] = array(
				'KEY' => $key3,
				'ARRAY' => $output,
			);
			$using = true;
			break;
		}
	}
	if(!$using)
	{
		$arResult['COUTPUT'][$default_output_id]['USING'] = 'Y';
		$arResult['USING']['COUTPUT'] = array(
			'KEY' => $default_output_id,
			'ARRAY' => $arResult['COUTPUT'][$default_output_id],
		);
	}
}
$this->IncludeComponentTemplate();