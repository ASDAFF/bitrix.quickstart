<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

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
    ),
);
?>
