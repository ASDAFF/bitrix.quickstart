<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/csv_data.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.ping/general/ping.php");

ClearVars();

$PING_RIGHT = $APPLICATION->GetGroupRight("altasib.ping");
if($PING_RIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
IncludeModuleLangFile(__FILE__);
$err_mess = "File: ".__FILE__."<br>Line: ";


$aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("ALX_PING_TITLE"), "ICON" => "main_channel_edit", "TITLE" => GetMessage("ALX_PING_DESC")),
                array("DIV" => "edit2", "TAB" => GetMessage("VOTE_PROP"), "ICON" => "main_channel_edit", "TITLE" => GetMessage("VOTE_GRP_PROP")),

               );
$tabControl = new CAdminTabControl("tabControl", $aTabs);
$message = null;

/********************************************************************
                                Actions
********************************************************************/


require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");



/*******************************************************************
                                                delete ping_elements
*******************************************************************/
CUtil::InitJSCore(Array("jquery"));
if($_REQUEST["Delete"]==GetMessage("ALX_DEL")){

	foreach($_REQUEST['arping'] as $LID=>$arElem){
		$str_id = "(";
			foreach($arElem as $id=>$on){
			$str_id .= $id.", ";	
			}
		$str_id = substr($str_id, 0, -2);
		$str_id .= ")";

        global $DB;
        $res = $DB->Query("DELETE FROM `altasib_table_ping` WHERE `SITE_ID`='".$DB->ForSql($LID)."' AND `ID` IN ".$DB->ForSql($str_id));

	}
}
/*******************************************************************
                                                Ping elements
*******************************************************************/
if($_REQUEST["Ping"]=="Ping"){
        

	
		foreach($_REQUEST['arping'] as $LID=>$arElem){
		$str_id = "(";
			foreach($arElem as $id=>$on){
			$str_id .= $id.", ";	
			}
		$str_id = substr($str_id, 0, -2);
		$str_id .= ")";
		
		
        

       

                global $DB;
				$res = $DB->Query("SELECT * FROM `altasib_table_ping` WHERE `SITE_ID`='".$DB->ForSql($LID)."' AND `ID` IN ".$DB->ForSql($str_id));
                while($arping = $res->Fetch()){
                        $result = CAltasibping::SendPing($arping["NAME"],$arping["URL"],$arping["SITE_ID"],$arping["ERROR"]);
                        $arURL = $result["URL"];
                        unset($result["URL"]);
                        $i=0;
                        $j=0;

                        foreach ($result as $key => $ping){
                                if($ping == "OK"){
                                $j++;
                                }else{
                                $arbadping[$arping["ID"]][] = $arURL[$key];
                                }
                                $arping["RESULT"] = $ping;
                                $arping["SEACH"] = $arURL[$key];
                                $arping["DATE"] = date('d.m.Y');
                                $arping["TIME"] = date("H:i:s");
								$arDataall[]=$arping;
                        $i++;

                        }
                        if ($j == $i){
                                $argoodpingsite[$arping["SITE_ID"]][] = $arping["ID"];
                        }
                }

}
                foreach ($arDataall as $data){
                        $res = $DB->Query("INSERT INTO `altasib_ping_log`
                                        (
                                                ID,
                                                SITE_ID,
                                                DATE,
                                                TIME,
                                                NAME,
                                                URL ,
                                                SEACH,
                                                RESULT
                                        )
                                        VALUES
                                        (".intval($data["ID"]).",'".$DB->ForSql($data["SITE_ID"])."','".$DB->ForSql($data["DATE"])."', '".$DB->ForSql($data["TIME"])."', '".$DB->ForSql($data["NAME"])."', '".$DB->ForSql($data["URL"])."','".$DB->ForSql($data["SEACH"])."', '".$DB->ForSql($data['RESULT'])."')
                                ");
                }
			if(count($argoodpingsite)>0){
				foreach($argoodpingsite as $sid=>$argoodping){
                if(count($argoodping)>0){
                        $str_id = "(";
                        foreach($argoodping as $id){
                                $str_id .= $id.", ";
                        }
                        $str_id = substr($str_id, 0, -2);
                        $str_id .= ")";
                        $res = $DB->Query("DELETE FROM `altasib_table_ping` WHERE `SITE_ID`='".$DB->ForSql($sid)."' AND  `ID` IN ".$DB->ForSql($str_id));
			
			   }
			 }
			}
                if(count($arbadping)>0){
                        foreach ($arbadping as $key => $data){
                        $error = serialize($data);
			
                $res = $DB->Query("UPDATE `altasib_table_ping` SET `ERROR`='".$error."' WHERE `ID`=".intval($key));
                        }
                }



}
?>
<script type="text/javascript">
function setChecked(obj)
   {
   var str = document.getElementById("text").innerHTML;
   str = (str == "<?=GetMessage("ALX_PING_CHECK")?>" ? "<?=GetMessage("ALX_PING_UNCHECK")?>" : "<?=GetMessage("ALX_PING_CHECK")?>");
   document.getElementById("text").innerHTML = str;


   var check = document.getElementsByClassName('checkbox');

   for (var i=0; i<check.length; i++)
      {
      check[i].checked = obj.checked;
      }
   }
</script>
<?
$tabControl->Begin();
?>
<?
//********************
//General Tab
//********************
$tabControl->BeginNextTab();

                global $DB;

                $res = $DB->Query("SELECT * FROM `altasib_table_ping` WHERE `A` = 1");
                while ($string = $res->Fetch()){
                $arDB[] = $string;
                }



/********************************************************************
                                Form1
********************************************************************/
?>
<tr>
        <td colspan="2">

        <form method="POST" action="<?=$APPLICATION->GetCurPage()?>" name="pingpage">
                <?=bitrix_sessid_post()?>
                <input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
        <?if(count($arDB)>0){?>
                <table id="edit1_edit_table" class="edit-table" cellspacing="0" cellpadding="0" border="0">
                <tr>
                <td colspan="2"><span id="text"><?=GetMessage("ALX_PING_CHECK")?></span> <?=GetMessage("ALX_PING_ALL")?></td>
                <td></td>
                <td></td>
                <td></td>
                </tr>
                                        <td>
                                        <input type="checkbox"  name="check_all" onclick="setChecked(this)">
                                        </td>
                                        <td width="50px">
                                        <?=GetMessage("ALX_PING_SITE")?>
                                        </td>
                                        <td>
                                        <?=GetMessage("ALX_PING_DATE")?>
                                        </td>
                                        <td>
                                        <?=GetMessage("ALX_PING_TIME")?>
                                        </td>
                                        <td >
                                        <?=GetMessage("ALX_PING_LINK")?>
                                        </td>
                                </tr>
                                        <?foreach($arDB as $string){?>
                                <tr>
                                        <td>
										<input type="checkbox" id="arping[<?=$string["SITE_ID"]?>][<?=$string["ID"]?>]" class="checkbox" name="arping[<?=$string["SITE_ID"]?>][<?=$string["ID"]?>]" />
                                        </td>
                                        <td width="50px">
                                        <?=$string["SITE_ID"]?>
                                        </td>
                                        <td>
                                        <?=$string["DATE"]?>
                                        </td>
                                        <td>
                                        <?=$string["TIME"]?>
                                        </td>
                                        <td >
                                        <a href="<?=$string["URL"]?>"><?=$string["NAME"]?></a>
                                        </td>
                                </tr>
                <?}?>
                        </table>
                        <input type="submit" name="Ping"  value="Ping"><input type="submit" name="Delete"  value="<?=GetMessage("ALX_DEL")?>" >
        <?}else{?>

        <?=GetMessage("ALX_PING_TABLE")?>

        <?}?>

                </form>
        </td>
</tr>
<?
$tabControl->EndTab();
$tabControl->BeginNextTab();?>
<tr>
        <td colspan="2">
<?
/********************************************************************
                                Form2
********************************************************************/
?>
<form method="POST" id="post_form" action="<?=$APPLICATION->GetCurPage()?>" name="post_form">

<?if($_REQUEST["AJAX_CALL"]=="Y") {
 global $APPLICATION;
 $APPLICATION -> RestartBuffer();
 }?>



<SCRIPT LANGUAGE="JavaScript">
function clicksubmit(formid, handler)
{
  var str = $('#'+formid).serializeArray();
  str[str.length] = { "name":"TYPE_SUBMIT", "value": handler};
  $.post("/bitrix/admin/altasib_ping_area.php", str, function(data)
          {
                $("#post_form").html(data);
          }
        );
  return false;
}
function checksite(lid){
$('.edit-table-result').each(function(i,val){
$(val).hide();
});
$('#site_'+lid).show();
}
$(document).ready(function() {
checksite($(':radio:checked').attr('id'));
});

</SCRIPT>

<?
$arrSites = array();
$fistSite=false;
$rs = CSite::GetList(($by="sort"), ($order="asc"));
while ($ar = $rs->Fetch()){
        $arrSites[$ar["ID"]] = $ar;
        if($fistSite===false){
                $fistSite=$ar["ID"];
        }
}
?>
<?=bitrix_sessid_post()?>
<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
<input type="hidden" name="AJAX_CALL" value="Y">

                <span class="required">*</span><?=GetMessage("VOTE_SITE")?><br / >
				
                <?
				$first = true;
				if(!empty($_REQUEST['arrSITE'][0]) && isset($arrSites[$_REQUEST['arrSITE'][0]])) $first=false;?>
				<?foreach($arrSites as $sid=>$arS){
				if($first){
						$checked = 'checked="checked"'; 
						$first=false;
				}else{
					if($sid==$_REQUEST['arrSITE'][0]){
						$checked = 'checked="checked"'; 
					}else{
						$checked="";
					}
				
				}
				?>
				 <input onchange="checksite('<?=$sid?>');" type="radio" name="arrSITE[]" value="<?echo htmlspecialcharsex($sid);?>" id="<?=htmlspecialcharsex($sid)?>" class="typecheckbox" <?=$checked?>>
                 <label for="<?=htmlspecialchars($sid)?>"><?echo '[<a title="'.GetMessage("VOTE_SITE_EDIT").'" href="/bitrix/admin/site_edit.php?LID='.htmlspecialchars($sid).'&lang='.LANGUAGE_ID.'">'.htmlspecialchars($sid).'</a>]&nbsp;'.htmlspecialcharsex($arrS["NAME"])?></label>
                 <br>
				
				<?}?>
	
                <?global $DB;
                if(empty($_REQUEST["arrSITE"][0])){
                $site_id = $fistSite;
                }else{
                $site_id = $DB->ForSql($_REQUEST["arrSITE"][0]);
                }
                $arDBsites=array();

                if($_REQUEST["TYPE_SUBMIT"]=="clear"){
				//DELETE FROM emp WHERE JOB
                $res = $DB->Query("DELETE FROM `altasib_ping_log` WHERE `SITE_ID`='".$DB->ForSql($_REQUEST["arrSITE"][0])."'");
                }
                $res = $DB->Query("SELECT * FROM `altasib_ping_log` ORDER BY `DATE` DESC, `TIME` DESC LIMIT 0, 50 ");
                while ($string = $res->Fetch()){
                $arDBsites[$string['SITE_ID']][] = $string;
                }

        ?> 
        <div style="height:100%;overflow: auto;">
                <?if(count($arDBsites)>0){?>
                        <table class="edit-table" cellspacing="0" cellpadding="0" border="0">
                                <tr valign="top">
                                        <td><?=GetMessage("ALX_PING_DATE")?></td>
                                        <td><?=GetMessage("ALX_PING_TIME")?></td>
                                        <td><?=GetMessage("ALX_PING_LINK")?></a></td>
                                        <td><?=GetMessage("ALX_PING_PING")?></td>
                                        <td><?=GetMessage("ALX_PING_RESULT")?></td>
                                </tr>
						</table>
						
                                <?foreach ($arrSites as $lid=>$arDB){?>
                        <table style="display:none;" id="site_<?=$lid?>" class="edit-table-result" cellspacing="0" cellpadding="0" border="0">
								<?
							if(isset($arDBsites[$lid])){
								foreach ($arDBsites[$lid] as $string){?>
								<tr valign="top">
                                        <td><?=$string["DATE"]?></td>
                                        <td><?=$string["TIME"]?></td>
                                        <td><a href="<?=$string["URL"]?>"><?=$string["NAME"]?></a></td>
                                        <td><?=$string["SEACH"]?></td>
                                        <td><?=$string["RESULT"]?></td>
                                </tr>
								<?}?>
							<?}else{?>
							<tr valign="top">
                                <td colspan="5"><?=GetMessage("ALX_PING_LOG")?></td>
                            </tr>
							
							<?}?>	
						</table>
                                <?}?>
                        
                <?}else{?>
                <br /><br />
                <?=GetMessage("ALX_PING_LOG")?>
                <br /><br /><br />
                <?}?>
        </div>


        <input type="submit" name="Refresh"  value="<?=GetMessage("ALX_REFRESH")?>" onclick = "clicksubmit('post_form','refresh'); return false;"><input type="submit" name="Clear"  value="<?=GetMessage("ALX_CLEAR")?>" onclick = "clicksubmit('post_form','clear');return false;" >


<?if($_REQUEST["AJAX_CALL"]=="Y") {
 die();
 }?> 
                </form>
        </td>
</tr>

<?$tabControl->EndTab();
$tabControl->End();
?>



<?
$tabControl->ShowWarnings("post_form", $message);
?>
<?
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
