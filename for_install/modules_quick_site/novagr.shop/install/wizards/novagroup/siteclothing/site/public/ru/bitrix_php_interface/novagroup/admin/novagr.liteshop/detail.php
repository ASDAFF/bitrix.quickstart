<?
    if (!defined("ADMIN_SECTION") || ADMIN_SECTION !== true ) return;

    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("NOVAGROUP_DETAIL_TAB"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("NOVAGROUP_DETAIL_TITLE")),
    );

  
    /*
	1 - изображение во всплывающем окне на весь экран(по умолчанию)
	2 - увеличение при наведении на картинку
	3 - изображение во всплывающем окне масштабированное под размер экрана
	*/
    
    if (isset($_POST['detailCartSelect']) )
    {
    	$detailValue = $_POST['detailCartSelect'];
    	COption::SetOptionString("main","detail_card", $detailValue);
        echo CAdminMessage::ShowNote(GetMessage("NOVAGROUP_DETAIL_OK"));
    } else {
    	$detailValue = COption::GetOptionString("main", "detail_card");
    }
    
    if (empty($detailValue)) {
    	$detailValue = 1;
    }

    $tabControl = new CAdminTabControl("tabControl", $aTabs, true, true);
?>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?echo htmlspecialcharsbx(LANG)?>" name="fs1">
    <?
            $tabControl->Begin();
            $tabControl->BeginNextTab();   
            $selectArr = array(
            	'1' => GetMessage("NOVAGROUP_DETAIL_VALUE_1"),
            	'2' => GetMessage("NOVAGROUP_DETAIL_VALUE_2"),
            	'3' => GetMessage("NOVAGROUP_DETAIL_VALUE_3"),
            );
    ?>
    <tr>
    <td colspan="2" style="padding-bottom:10px;"><?echo GetMessage("NOVAGROUP_DETAIL_TEXT")?></td>
    </tr>
    <tr>
        <td colspan="2" width="100%">
            <div>
                <select name="detailCartSelect" id="detailCartSelect">
                <?php 
                foreach ($selectArr as $key => $value) {
                	?>
                	<option <? if ($key == $detailValue) echo "selected"; ?> value="<?=$key?>"><?echo $value?></option>
                	<?php 
                }
                ?>
                </select>
            </div>
        </td>
    </tr>
    <?
            $tabControl->Buttons();
    ?>
    <input class="mybutton" 
        type="submit" name="save" 
        value="<?echo GetMessage("NOVAGROUP_DETAIL_SAVE")?>" 
        title="<?echo GetMessage("NOVAGROUP_DETAIL_SAVE")?>" />
    <?
            $tabControl->End();
    ?>
</form>


<?echo BeginNote();?>
<?= GetMessage("NOVAGROUP_DETAIL_NOTE"); ?>
<?echo EndNote(); ?>
