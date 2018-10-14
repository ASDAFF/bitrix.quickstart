<?
// Подключаем модуль (выполняем код в файле include.php)
CModule::IncludeModule('nimax.stat');

// Подключаем языковые константы
IncludeModuleLangFile( __FILE__ );

// Описываем табы административной панели битрикса
$aTabs = array();
foreach(Nimax_Stat_Option::getTemplateList() as $id => $name)
{
    $aTabs[] = array(
        'DIV'   => $id,
        'TAB'   => $name,
        'ICON'  => '',
        'TITLE' => ''
    );
}

// Инициализируем табы
$oTabControl = new CAdmintabControl('tabControl', $aTabs);
$oTabControl->Begin();
?>
<form method="post" action="<?=$APPLICATION->GetCurPage()?>?mid=nimax.stat&lang=<?=LANG?>&mid_menu=1">
    <?=bitrix_sessid_post()?>

    <?foreach($aTabs as $arTab){?>

        <?
        $oTabControl->BeginNextTab();
        $error = '';
        try{
            $NSO = new Nimax_Stat_Option();
            $NSO->templateInit($arTab['DIV']);
            if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['tabControl_active_tab'] == $arTab['DIV'])
                $NSO->saveOption($_POST);
        }
        catch(Exception $e){
            $error = $e->getMessage();
        }
        ?>

        <tr><td valign="top" align="center" colspan="2" style="color:red"><?=$error;?></td></tr>

        <?
        $group = '';
        foreach($NSO->code_array as $codeId => $arCode){?>
            <?
            if(empty($group) || $group != $arCode['group']){
                $group = $arCode['group'];
            ?>
                <tr class="heading">
                    <td valign="top" align="center" colspan="2"><b><?=GetMessage('TITLE_SECTION_'.$group)?></b></td>
                </tr>
            <?}?>
            <tr>
                <td width="40%" class="field-name" valign="top"><label for="<?=$codeId?>"><?=GetMessage('LABEL_'.$codeId)?>:</label></td>
                <td valign="top">
                    <textarea name="<?=$codeId?>[<?=$arTab['DIV']?>]" id="<?=$codeId?>" rows="10" cols="50"><?=$NSO->getCurCode($codeId)?></textarea>
                </td>
            </tr>
            <tr>
                <td width="40%" valign="top"><div class="empty"></div></td>
                <td valign="top" align="left">
                    <p style="background: #fff9df; border: 1px solid #eee8d5; padding: 3px 6px; font-size: 11px; max-width: 414px;"><?=GetMessage('DESC_'.$codeId)?></p>
                </td>
            </tr>
            <tr><td colspan="2"><br></td></tr>
        <?}?>

    <?}?>

    <?$oTabControl->Buttons();?>
    <input type="submit" name="Update" value="<?=GetMessage('BUTTON_SAVE')?>" />
    <input type="reset" name="reset" value="<?= GetMessage('BUTTON_RESET')?>" />
    <input type="hidden" name="Update" value="Y" />
    <?$oTabControl->End();?>

</form>