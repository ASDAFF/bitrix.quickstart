<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

IncludeModuleLangFile(__FILE__);

//Тип почтового события
$eventType         = new CEventType;
$arEventTypeFields = array(
	0 => array(
		'LID'         => 'ru',
		'EVENT_NAME'  => GetMessage('ET_EVENT_NAME'),
		'NAME'        => GetMessage('RU_ET_NAME'),
		'DESCRIPTION' => GetMessage('RU_ET_DESCRIPTION'),
	),
	1 => array(
		'LID'         => 'en',
		'EVENT_NAME'  => GetMessage('ET_EVENT_NAME'),
		'NAME'        => GetMessage('ET_RU_NAME'),
		'DESCRIPTION' => GetMessage('ET_RU_DESCRIPTION'),
	),
);
foreach($arEventTypeFields as $arField)
{
	$rsET = $eventType->GetByID($arField['EVENT_NAME'], $arField['LID']);
	$arET = $rsET->Fetch();

	//v1.3.2
	if(!$arET)
		$eventType->Add($arField);
	else
		$eventType->Update(array('ID'=>$arET['ID']),$arField);
}

unset($arField);

//Почтовые шаблоны
if(!empty($this->SITE_ID))
{
	$eventM            = new CEventMessage;
	//foreach($this->SITE_ID as $siteId)
	//{
		$arEventMessFields = array(
			0 => array(
				'ACTIVE'     => 'Y',
				'EVENT_NAME' => GetMessage('ET_EVENT_NAME'),
				'LID'        => $this->SITE_ID,
				'EMAIL_FROM' => GetMessage('EM_EMAIL_FROM'),
				'EMAIL_TO'   => GetMessage('EM_EMAIL_TO'),
				'SUBJECT'    => GetMessage('EM_SUBJECT_ADMIN'),
				'BODY_TYPE'  => 'text',
				'MESSAGE'    => GetMessage('EM_MESSAGE'),
			),
			1 => array(
				'ACTIVE'     => 'Y',
				'EVENT_NAME' => GetMessage('ET_EVENT_NAME'),
				'LID'        => $this->SITE_ID,
				'EMAIL_FROM' => GetMessage('EM_EMAIL_FROM'),
				'EMAIL_TO'   => GetMessage('EM_EMAIL_TO'),
				'SUBJECT'    => GetMessage('EM_SUBJECT_USER'),
				'BODY_TYPE'  => 'text',
				'MESSAGE'    => GetMessage('EM_MESSAGE'),
			),
		);

		foreach($arEventMessFields as $arField)
		{
			$rsMess = $eventM->GetList($by = 'id', $order = 'asc', array(
				'SUBJECT' => $arField['SUBJECT'],
				'LID'     => $arField['LID']
			));
			if(!$arMess = $rsMess->Fetch())
				$eventM->Add($arField);
		}
	//}
	unset($arField);
}