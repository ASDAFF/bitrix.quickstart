<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
if (!empty($arResult['ERROR']))
{
    echo $arResult['ERROR'];
    return false;
}
?>

<div class="reports-result-list-wrap">
    <div class="alf" id="alf_ms">
        <a class="simbol" href="<?=$APPLICATION->GetCurPageParam("alf=", array("alf"));?>"><span style="transform: rotate(45deg); display: block;">+</span></a>
        <?
        foreach ($arResult['alf'] as $value_alf){?>
            <a class="simbol <?echo ($_REQUEST['alf']==$value_alf)? "active" : '';?>" href="<?=$APPLICATION->GetCurPageParam("alf=".$value_alf, array("alf", 'page'));?>"><?=$value_alf?></a>
        <?}?>
    </div>
</div>
