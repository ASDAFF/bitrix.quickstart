<?php
/**
 * @var $routes
 */
?>

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use DigitalWand\MVC\BaseComponent;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

Loader::includeModule('digitalwand.mvc');
Loc::loadMessages(__FILE__);

$arComponentParameters = BaseComponent::getArComponentParameters();
$arComponentParameters["PARAMETERS"]['SEF_MODE'] = array(
<?foreach($routes as $route):?>
    "<?=$route['id']?>" => array(
        "NAME" => "<?=$route['name']?>",
        "DEFAULT" => "<?=$route['sef']?>",
    <?
    $variablesString = '';
    $arVariables = explode(',',$route['variables']);
    if(!empty($arVariables)) {
        $variablesString = implode('","', $arVariables);
        $variablesString = '"'.$variablesString.'"';
    }
    ?>
        "VARIABLES" => array(<?=$variablesString?>),
    ),
<?endforeach;?>
);
?>