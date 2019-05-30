<?
GLOBAL $USER;
use Bitrix\Main\Page\Asset,
	 Bitrix\Main\Page\AssetLocation,
	 Bitrix\Main\Web\Json,
	 Bitrix\Main\Localization\Loc;


Loc::loadMessages(__FILE__);

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

/**
 * Bitrix vars
 *
 * @var WebesOneclickComponent  $component
 * @var CBitrixComponentTemplate $this
 * @var array                    $arParams
 * @var array                    $arResult
 * @var array                    $arLangMessages
 * @var array                    $templateData
 *
 * @var string                   $templateFile
 * @var string                   $templateFolder
 * @var string                   $parentTemplateFolder
 * @var string                   $templateName
 * @var string                   $componentPath
 *
 * @var CDatabase                $DB
 * @var CUser                    $USER
 * @var CMain                    $APPLICATION
 * @var CUserTypeManager         $USER_FIELD_MANAGER
 */



$this->setFrameMode(true);


if($USER->isAdmin())
{
    CJSCore::Init(array("jquery"));
    $this->addExternalJs('/bitrix/components/webes/itemconsist/js/configuration'.(mb_strtolower(LANG_CHARSET)!='utf-8'?'-1251':'').'.js');
    $this->addExternalCss('/bitrix/components/webes/itemconsist/css/configuration.css');

    if($arParams['ELEMENT_ID'] > 0)
        print '<div class="w-ic-admin_block" data-element_id="'.$arParams['ELEMENT_ID'].'" data-price_param_id="'.$arParams['PRICE_PARAM_ID'].'"></div>';

}
?>

<?if($arParams['ELEMENT_ID'] > 0):?>
    <?/* вывод параметров публичный */?>
    <?if($arParams['VIEW_PARAMS']=='Y'):?>

        <?  require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/webes/itemconsist/includes/init_bitrix.php');
            require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/webes/itemconsist/classies.php');
            $arElementData=CStepUseIC::get_element_data($arParams['ELEMENT_ID']);
            $arIngridients=CStepUseIC::ingridients_simple();
        ?>

        <?if(!empty($arElementData['consist'])):?>
            <div><b><?=Loc::GetMessage("webes_ic_template_CONSIST")?></b></div>

            <div>
                <?=CStepUseIC::get_public_consist($arElementData['consist'],$arIngridients)?>
                &mdash;
                <b><?=$arElementData['last_price']?> <?=Loc::GetMessage("webes_ic_template_RUB")?></b>
            </div>
        <?endif;?>
        <br>
        <?if($arElementData['offers_exists']):?><?// если есть торговые предложения?>
            <div><b><?=Loc::GetMessage("webes_ic_template_PRICE_VARIANTS")?></b></div>
            <table class="ic-w-public-table">
            <?foreach($arElementData['offers'] as $offer_id => $oar):?>
                <?if(!empty($oar['consist'])):?>
                    <tr>
                        <td><?=$oar['name']?></td>
                        <td><?=CStepUseIC::get_public_consist($oar['consist'],$arIngridients)?></td>
                        <td><?=$oar['last_price']?> <?=Loc::GetMessage("webes_ic_template_RUB")?></td>
                    </tr>
                <?endif;?>
            <?endforeach;?>
            </table>
        <?endif;?>

    <?endif;?>
<?endif;?>