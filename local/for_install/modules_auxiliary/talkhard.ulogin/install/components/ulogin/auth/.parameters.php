<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$group_list = CGroup::GetList(($by = "id"), ($order = "asc"), array("ACTIVE" => "Y"));
$groups = array();

while($group =  $group_list->GetNext()){
    $groups[$group['ID']] = $group['NAME'];
}

$arComponentParameters = array(
    'PARAMETERS' => array(
        'PROVIDERS' => array(
            'NAME' => GetMessage("TALKHARD_ULOGIN_PROVAYDERY"),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'DEFAULT' => 'vkontakte,odnoklassniki,mailru,facebook',
            'PARENT' => 'BASE',
        ),
        'HIDDEN' => array(
            'NAME' => GetMessage("TALKHARD_ULOGIN_SKRYTYE_PROVAYDERY"),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'DEFAULT' => 'other',
            'PARENT' => 'BASE',
        ),
        "TYPE" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("TALKHARD_ULOGIN_TIP"),
            "TYPE" => "LIST",
            "VALUES" => array('small' => 'small', 'panel' => 'panel'),
            "DEFAULT" => 'panel',
            "ADDITIONAL_VALUES" => "N",
            "REFRESH" => "Y",
        ),
        "REDIRECT_PAGE" => array(
            'NAME' => GetMessage("TALKHARD_ULOGIN_STRANICA_REDIREKTA_P"),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'PARENT' => 'BASE',
        ),
        "UNIQUE_EMAIL" => array(
	  'NAME' => GetMessage("TALKHARD_ULOGIN_REGISTRIROVATQ_POLQZ").' email',
	  'TYPE' => 'CHECKBOX',
	  'PARENT' => 'BASE',
	  'DEFAULT' => 'N'
	),
	"SEND_MAIL" => array(
	  'NAME' => GetMessage("TALKHARD_ULOGIN_OTPRAVLATQ").' email '.GetMessage("TALKHARD_ULOGIN_ADMINISTRATORU_PRI_R"),
	  'TYPE' => 'CHECKBOX',
	  'PARENT' => 'BASE',
	  'DEFAULT' => 'N'
	),
    "GROUP_ID" => array(
        'NAME' => GetMessage("TALKHARD_ULOGIN_GROUPS_MESSAGE"),
        'TYPE' => 'LIST',
        'MULTIPLE' => 'Y',
        'VALUES' => $groups,
        'PARENT' => 'BASE',
        'DEFAULT' => '5'
    )
    ),
);
?>
