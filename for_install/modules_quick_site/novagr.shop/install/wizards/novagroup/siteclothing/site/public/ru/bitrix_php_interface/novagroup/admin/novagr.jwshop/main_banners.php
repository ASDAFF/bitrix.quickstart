<?
if (!defined("ADMIN_SECTION") || ADMIN_SECTION !== true ) return;

    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("NOVAGROUP_MAIN_BANNERS_TAB"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("NOVAGROUP_MAIN_BANNERS_TITLE")),
    );

    $novaGroupBanners = new Novagroup_Classes_General_Banners('novagr.jwshop');
    
    if (isset($_POST['bannersIBlock']) and isset($_POST['bannersElement']))
    {
    	// set active banner element
        $bannersIBlock = $_POST['bannersIBlock'];
        $bannersElement = $_POST['bannersElement'];

        if(isset($bannersElement[$bannersIBlock]) and $bannersElement[$bannersIBlock]>0)
        {
            $novaGroupBanners->setIBlockActive($bannersIBlock);
            $novaGroupBanners->setActive($bannersElement[$bannersIBlock]);
            echo CAdminMessage::ShowNote(GetMessage("NOVAGROUP_MAIN_BANNERS_OK"));
        }
    }
    

    $tabControl = new CAdminTabControl("tabControl", $aTabs, true, true);
?>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?echo htmlspecialcharsbx(LANG)?>" name="fs1">
    <?
            $tabControl->Begin();
            $tabControl->BeginNextTab();   
    ?>
    <tr>
        <td colspan="2" style="padding-bottom:10px;"><?echo GetMessage("NOVAGROUP_MAIN_BANNERS_TEXT")?></td>
    </tr>
    <tr>
        <td colspan="2" width="100%">

            <div>
                <label>
                    <?php
                    $bannersIBlock = array();
                    $bannersIBlockSelect = $novaGroupBanners->getIBlockList();
                    foreach($bannersIBlockSelect as $ar_res){ // цикл по информационным блокам
                        $bannersIBlock['REFERENCE'][] = $ar_res['NAME'];
                        $bannersIBlock['REFERENCE_ID'][] = $ar_res['ID'];
                    }
                    $getIBlockActive = $novaGroupBanners->getIBlockActive();
                    echo GetMessage('NOVAGROUP_MAIN_BANNERS_IBLOCK'); echo SelectBoxFromArray("bannersIBlock", $bannersIBlock, $getIBlockActive['ID'] , "", "onchange='changeBannersIBlock(this.value)'");
                    ?>
                </label>
                <?
                foreach($bannersIBlockSelect as $ar_res){ // цикл по информационным блокам
                    $style = ($getIBlockActive['ID']==$ar_res['ID']) ? 'display:inline;' : 'display:none;';
                ?>
                    <label id="bannersListElementID_<?=$ar_res['ID']?>" style="<?=$style?>">
                        <?php
                        $bannersElement = array();
                        $novaGroupBanners->__setIBlockActive($ar_res['ID']);
                        $bannersSelect = $novaGroupBanners->getList();
                        foreach($bannersSelect as $res){ // цикл по информационным блокам
                            $bannersElement['REFERENCE'][] = $res['NAME'];
                            $bannersElement['REFERENCE_ID'][] = $res['ID'];
                        }
                        $getActive = $novaGroupBanners->getActive();
                        echo GetMessage('NOVAGROUP_MAIN_BANNERS_ACTIVATE'); echo SelectBoxFromArray("bannersElement[".$ar_res['ID']."]", $bannersElement, $getActive['ID'] );
                        ?>
                    </label>
                <?
                }
                ?>
            </div>
        </td>

    </tr>
    <?
            $tabControl->Buttons();
    ?>
    <input class="mybutton" 
        type="submit" name="save" 
        value="<?echo GetMessage("NOVAGROUP_MAIN_BANNERS_SAVE")?>" 
        title="<?echo GetMessage("NOVAGROUP_MAIN_BANNERS_SAVE")?>" />
    <?
            $tabControl->End();
    ?>
</form>
<script type="text/javascript">
    function changeBannersIBlock(id) {
        <?
            foreach($bannersIBlockSelect as $ar_res){
                print "document.getElementById('bannersListElementID_".$ar_res["ID"]."').style.display = 'none';";
            }
        ?>
        document.getElementById('bannersListElementID_'+id+'').style.display = 'inline';
    }
</script>
<?echo BeginNote();?>
<?= GetMessage("NOVAGROUP_MAIN_BANNERS_NOTE"); ?>
<?echo EndNote(); ?>
