<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();




//echo'<pre>'; print_r($_SESSION['PARSDATA']); echo'</pre>';




 if(CModule::IncludeModule("iblock"))
  {
?>


<p align="center"><a href="http://qteam.ru/parser/template_editor.php"><img align="center" src="http://qteam.ru/images/logobvp.jpg" border="0" width="50"  align="absmiddle" /></a><br /><a href="http://qteam.ru/parser/template_editor.php" style="font-size:11px;"><?=GetMessage("QTEAM_PARSER_REDAKTOR_SABLONOV")?></a> | <a href="http://qteam.ru/parser/faq.php?SECTION_ID=153" style="font-size:11px;">FAQ <?=GetMessage("QTEAM_PARSER_PO_NASTROYKE")?></a> | <a href="http://qteam.ru/parser/help.php" style="font-size:11px;"><?=GetMessage("QTEAM_PARSER_OPISANIE_RABOTY")?></a><br />&nbsp;</p>




<? /*
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
*/
?>


<script language="javascript">
    var jQ = false;  
    function PARS_initJQ() {  
      if (typeof(jQuery) == 'undefined') {  
        if (!jQ) {  
          jQ = true;  
          document.write('<scr' + 'ipt type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></scr' + 'ipt>');  
        }  
        setTimeout('PARS_initJQ()', 50);  
      } else {  
        (function($) {  
        $(function() {  
      
          // здесь пишем jQuery код  
      
        })  
        })(jQuery)  
      }  
    }  
    PARS_initJQ();  
</script>



<style>



.parsbox {
    width:90%;
/*    height:200px; */
    background:#FFF;
    margin:10px auto;
	padding:10px;
}
 
/*==================================================
 * Effect 8
 * ===============================================*/
.parseffect8
{
    position:relative;
    -webkit-box-shadow:0 1px 4px rgba(0, 0, 0, 0.3), 0 0 40px rgba(0, 0, 0, 0.1) inset;
       -moz-box-shadow:0 1px 4px rgba(0, 0, 0, 0.3), 0 0 40px rgba(0, 0, 0, 0.1) inset;
            box-shadow:0 1px 4px rgba(0, 0, 0, 0.3), 0 0 40px rgba(0, 0, 0, 0.1) inset;
}
.parseffect8:before, .parseffect8:after
{
    content:"";
    position:absolute;
    z-index:-1;
    -webkit-box-shadow:0 0 20px rgba(0,0,0,0.8);
    -moz-box-shadow:0 0 20px rgba(0,0,0,0.8);
    box-shadow:0 0 20px rgba(0,0,0,0.8);
    top:10px;
    bottom:10px;
    left:0;
    right:0;
    -moz-border-radius:100px / 10px;
    border-radius:100px / 10px;
}
.parseffect8:after
{
    right:10px;
    left:auto;
    -webkit-transform:skew(8deg) rotate(3deg);
       -moz-transform:skew(8deg) rotate(3deg);
        -ms-transform:skew(8deg) rotate(3deg);
         -o-transform:skew(8deg) rotate(3deg);
            transform:skew(8deg) rotate(3deg);
}





div.parsblk
{
 border:1px #999999 solid; overflow:hidden;min-height:10px;


    width:88%;
/*    height:200px; */
    background:#FFF;
    margin:40px auto;
    padding:10px;
 }




 a.parslnk:link, a.parslnk:visited {
    background: none repeat scroll 0% 0% #cccccc;
    border: medium none;
    clear: both;
    color: #FFF;
    display: block;
    font-size: 12px;
    font-weight: bold;
    height: 30px;
    line-height: 30px;
    text-align: center;
    text-decoration: none;
    width: 150px;
}


 a.parslnk:link, a.parslnk:visited {
    background: none repeat scroll 0% 0% #aaaaaa;
    border: medium none;
    clear: both;
    color: #FFF;
    display: block;
    font-size: 12px;
    font-weight: bold;
    height: 30px;
    line-height: 30px;
    text-align: center;
    text-decoration: none;
    width: 150px;
	}


</style>
 
 










<?


$cnttmplt=0;
if(count($arResult["PARSTEMPLATES"])>0)
 {
?>
<form action="?" method="post" enctype="multipart/form-data">
<p align="center">
<?=GetMessage("QTEAM_PARSER_VYBERITE_SABLON")?><select id="PARSKEYTEMPLATE" name="PARSKEYTEMPLATE" <? /* onChange="javascript: location.replace(document.getElementById('tform').action+'&sort='+this.options[this.selectedIndex].value);" */ ?>>
<?

foreach($arResult["PARSTEMPLATES"] as $it)
{
 $cnttmplt++;
 /* <option <? if($_SESSION['PARSKEY']==$it['KEY'][0]) echo' selected="selected"';  ?> value="<?=$it['KEY'][0]?>" ><?=$it['NAME'][0]?></option> */
?> <option <? if($_SESSION['PARSKEY']==$it['KEY'][0]) echo' selected="selected"';  ?> value="<?=$it['KEY'][0]?>" ><?=$it['NAME']?></option><?


// echo ' id='.$it['ID'][0];
// echo'<pre>'; print_r($it); echo'</pre>';
 }
?>
</select> 


 <?=GetMessage("QTEAM_PARSER_SSYLKI")?><select id="PARSPAGETEMPLATE" name="PARSPAGETEMPLATE" <? /* onChange="javascript: location.replace(document.getElementById('tform').action+'&sort='+this.options[this.selectedIndex].value);" */ ?>>
<?
$getcntpg=10;
if($arParams["GET_COUNT_NEWS"]>0) $getcntpg=$arParams["GET_COUNT_NEWS"];
if($getcntpg>50) $getcntpg=10;


if($_SESSION['PARSPAGE']>0) { } else $_SESSION['PARSPAGE']=1;
for($ipg=1; $ipg<400; $ipg+=$getcntpg)
{
?> <option <? if($_SESSION['PARSPAGE']==$ipg) echo' selected="selected"';  ?> value="<?=$ipg?>" ><?=GetMessage("QTEAM_PARSER_S").$ipg.' '.GetMessage("QTEAM_PARSER_PO").($ipg+($getcntpg-1)) ?></option><?


// echo ' id='.$it['ID'][0];
// echo'<pre>'; print_r($it); echo'</pre>';
 }
?>
</select>


<input type="submit" value="<?=GetMessage("QTEAM_PARSER_POLUCITQ_DANNYE")?>" /></p>




</form>
<?
  } // if(count($arResult["PARSTEMPLATES"])>0)
 






 if($arResult["TYPE"] == "SUBMIT") 
  {
//	echo'<br>--------MESSAGE--------<br><pre>'; print_r($arResult["MESSAGE"]); echo'</pre>';
//	echo'<br>-------ERROR---------<br><pre>'; print_r($arResult["ERROR"]); echo'</pre>';
 
   $cnti=0; 
   foreach($arResult["MESSAGE"] as $infvl) 
    {
     $cnti++; if($cnti==1) echo'<p>&nbsp;<br /><strong>'.GetMessage("QTEAM_PARSER_SOOBSENIA").'</strong></p>';
     echo'<p>'.$infvl.'</p>'; 
     }


   $cnti=0; 
   foreach($arResult["ERROR"] as $infvl) 
    {
     $cnti++; if($cnti==1) echo'<p>&nbsp;<br /><strong>'.GetMessage("QTEAM_PARSER_OSIBKI").'</strong></p>';
     echo'<p style="color:#ff0000;">'.$infvl.'</p>'; 
     }


   echo'<p>&nbsp;<br /><a href="?">&lt;&lt; '.GetMessage("QTEAM_PARSER_VERNUTQSA").'</a></p>';

   return;
   }







?>












 
 
 
<?

if($arResult["ERROR"])
 {
 
 if(count($arResult["ERROR"])>0)
  {
   echo'<p><strong>'.GetMessage("QTEAM_PARSER_OSIBKI_PRI_ZAGRUZKE").'</strong></p>';
   foreach($arResult["ERROR"] as $vl) echo'<p><font class="errortext">'.$vl.'</font></p>';
   } 


 if($cnttmplt>0) { } else echo'<p align="center">&nbsp;<br />'.GetMessage("QTEAM_PARSER_CTO_TO_POSLO_NE_TAK").'</a><br />&nbsp;</p>';

 
  }
else
 {   
 
 if($cnttmplt>0) { } else echo'<p align="center">&nbsp;<br />'.GetMessage("QTEAM_PARSER_CTO_TO_POSLO_NE_TAK").'</a><br />&nbsp;</p>';

 
?> 
 
 
 
 
 
 
 

<form action="?" method="post" enctype="multipart/form-data">

  
<?




//$homepage = file_get_contents('http://qteam.ru/parser/xmlfile.php');
//echo $homepage;
//die('----------------');


	

//echo'<pre>'; print_r($xml1); echo'</pre>';


$numblock=0;
//foreach ($xml1->ITEM as $it){
foreach($arResult["ITEMS"] as $it){

echo'<table style="width:98%;"><tr><td>';


$POSTDATA=array();

// echo'<br>=========================================<br>';

$numblock++;


// echo'<br /><br />';



echo'<div class="parsbox parseffect8">';

if($it["ERROR"]) 
  echo '<p><input name="PIADD_'.$numblock.'" type="checkbox" /> '.GetMessage("QTEAM_PARSER_SOHRANITQ_STATQU").'</p>';
else
  echo '<p><input name="PIADD_'.$numblock.'" type="checkbox" checked="checked" /> '.GetMessage("QTEAM_PARSER_SOHRANITQ_STATQU").'</p>';
 



if((!$it["FPDATE"])&&($it["DATE"])) $it["FPDATE"]=$it["DATE"];
if($it["FPDATE"]) 
{
 echo '<p style="text-align:right; font-size:11px;">';
 echo $it["FPDATE"];
 echo '</p>';
 echo '<div style="display:none;"><textarea id="PARS_FPDATE_'.$it["NUMART"].'" name="PARS_FPDATE_'.$it["NUMART"].'">'.$it["FPDATE"].'</textarea></div>';
 }
 
 
echo'<p>';


if($it["FPNAME"])
{
// echo '“екст ссылки первой страницы: <br> <strong>'.$it["FPNAME"].'</strong><br />';
 echo '<strong>'.$it["FPNAME"].'</strong>';

// echo '<a id="parslnk'.$numblock.'" href="javascript: void(0); return false;">'.iconv('UTF-8', 'windows-1251', $it->FPNAME).'</a>';
// echo '<strong><span class="parslnk'.$numblock.'">'.iconv('UTF-8', 'windows-1251', $it->FPNAME).'</span></strong>';
//  echo '<input name="FPNAME" type="hidden" value="'.$ttxtv.'" />';
 echo '<div style="display:none;"><textarea id="PARS_FPNAME_'.$it["NUMART"].'" name="PARS_FPNAME_'.$it["NUMART"].'">'.$it["FPNAME"].'</textarea></div>';

}

if((!$it["FPIMAGE"])&&($it["IMAGE"])) $it["FPIMAGE"]=$it["IMAGE"];
if(($it["FPIMAGE"]) && ($arParams["IMG_ANONS_ADD"]=='Y'))
{
// echo '—сылка на изображение к ссылки первой страницы: ';
	echo '<img src="'.$it["FPIMAGE"].'" border="0" align="left" height="80" style="margin-right:7px;" />'; 
    echo '<br />';
//  echo '<input name="FPIMAGE" type="hidden" value="'.$ttxtv.'" />';
 echo '<div style="display:none;"><textarea id="PARS_FPIMAGE_'.$it["NUMART"].'" name="PARS_FPIMAGE_'.$it["NUMART"].'">'.$it["FPIMAGE"].'</textarea></div>';

 }



echo'</p>';



if((!$it["FPANONS"])&&($it["ANONS"])) $it["FPANONS"]=$it["ANONS"];
if($it["FPANONS"]) 
{
 echo '<p>';
 echo $it["FPANONS"];
 echo '</p>';
 echo '<div style="display:none;"><textarea id="PARS_FPANONS_'.$it["NUMART"].'" name="PARS_FPANONS_'.$it["NUMART"].'">'.$it["FPANONS"].'</textarea></div>';

//  echo '<input name="FPANONS" type="hidden" value="'.$ttxtv.'" />';
 }



if($it["ERROR"]) 
{
echo '<p><span style="color:#ff0000;"><b>'.GetMessage("QTEAM_PARSER_OSIBKA_ZAGRUZKI_STRA").'</b><br />';
echo $it["ERROR"];
echo '</span></p>';

//  echo '<input name="ERROR" type="hidden" value="'.$ttxtv.'" />';
}




echo '<p align="right"><a id="parslnk'.$numblock.'"  class="parslnk" style="cursor:pointer;"><em>'.GetMessage("QTEAM_PARSER_PODROBNOE_OPISANIE").'</em></a></p>';



echo'<p align="center">'.GetMessage("QTEAM_PARSER_RODITELQSKIY_RAZDEL").'<br /><select id="PARENTSECT_'.$it["NUMART"].'" name="PARENTSECT_'.$it["NUMART"].'" >';

                echo'<option  value="0" '.(($arParams["PARENT_SECTION"]=="0")? 'selected="selected"' : '').'>'.GetMessage("QTEAM_PARSER_VERHNIY_UROVENQ").'</option>';




   $sarFilter = array('IBLOCK_ID'=>$arParams["IBLOCK_ID"]);
	    $sdb_list = CIBlockSection::GetList(array('NAME'=>'ASC'), $sarFilter, true, array("ID", "NAME", "CODE", "DEPTH_LEVEL"));
	    while($sar_result = $sdb_list->GetNext())
	    {

//              $SECTarResult[$sar_result['ID']]=str_repeat(".", ($sar_result['DEPTH_LEVEL'])*3).$sar_result['NAME'];


                echo'<option  value="'.$sar_result['ID'].'" '.(($sar_result['ID']==$arParams["PARENT_SECTION"])? 'selected="selected"' : '').'>'.str_repeat(".", ($sar_result['DEPTH_LEVEL'])*3).$sar_result['NAME'].'</option>';


/*
	        $SECTarResult[] = array(
	                    "ID" => $sar_result['ID'],
	                    "CODE" => $sar_result['CODE'],
	                    "DEPTH_LEVEL" => $sar_result['DEPTH_LEVEL'],
	                    "NAME" => $sar_result['NAME'],
	                    "ELEMENT_CNT" => $sar_result['ELEMENT_CNT']
	                   );
*/
	    }
////	 echo '<pre>'; print_r($SECTarResult); echo '</pre>';




echo '</select>';

if($it["FPHREF"]) 
{
// echo 'Cсылка первой страницы: ';
 echo '<br /><span style="color:#999999;font-size:11px;">'.$it["FPHREF"].'</span>';
 echo '<div style="display:none;"><textarea id="PARS_FPHREF_'.$it["NUMART"].'" name="PARS_FPHREF_'.$it["NUMART"].'">'.$it["FPHREF"].'</textarea></div>';

// echo '<br />';
}

echo'</p></div>';



 
echo'<div id="parsblk'.$numblock.'" class="parsblk"><table style="width:98%;"><tr><td>';

if(($it["FPDATE"])&&(!$it["DATE"])) $it["DATE"]=$it["FPDATE"];
if($it["DATE"]) 
{
 echo '<p style="text-align:right; font-size:11px;">'.$it["DATE"].'</p>';
 echo '<div style="display:none;"><textarea id="PARS_DATE_'.$it["NUMART"].'" name="PARS_DATE_'.$it["NUMART"].'">'.$it["DATE"].'</textarea></div>';

// echo '<input name="DATE" type="hidden" value="'.$ttxtv.'" />';
 }


 
if(!$it["HEADPAGE"]) $it["HEADPAGE"]=$it["FPNAME"];  
if($it["HEADPAGE"]) 
{
 echo '<h1>'.$it["HEADPAGE"].'</h1>';
// echo '<input type="hidden" id="PARS_HEADPAGE_'.$it["NUMART"].'" name="PARS_HEADPAGE_'.$it["NUMART"].'" value="'.$it["HEADPAGE"].'" />';
 echo '<div style="display:none;"><textarea id="PARS_HEADPAGE_'.$it["NUMART"].'" name="PARS_HEADPAGE_'.$it["NUMART"].'">'.$it["HEADPAGE"].'</textarea></div>';

//  echo '<input name="HEADPAGE" type="hidden" value="'.$ttxtv.'" />';
 }


echo'<p>';

if(($it["FPIMAGE"])&&(!$it["IMAGE"])) $it["IMAGE"]=$it["FPIMAGE"];
if(($it["IMAGE"]) && ($arParams["IMG_DETAIL_ADD"]=='Y'))
{
// echo '—сылка на изображение второй страницы: <br>';
	echo '<table><tr><td><img src="'.$it["IMAGE"].'" border="0" align="center" /></td></tr><tr><td align="center"><small>'.GetMessage("QTEAM_PARSER_IZOBRAJENIE_DETALQNO").'</small></td></tr></table>';
	echo '<br />'; 
    echo '<div style="display:none;"><textarea id="PARS_IMAGE_'.$it["NUMART"].'" name="PARS_IMAGE_'.$it["NUMART"].'">'.$it["IMAGE"].'</textarea></div>';

 }



if($it["ANONS"]) 
{
// echo $it["ANONS"];
 echo '<div style="display:none;"><textarea id="PARS_ANONS_'.$it["NUMART"].'" name="PARS_ANONS_'.$it["NUMART"].'">'.$it["ANONS"].'</textarea></div>';
//  echo '<input name="ANONS" type="hidden" value="'.$ttxtv.'" />';
 }

echo'</p><hr />';







if($it["DETAIL"]) 
{
echo'<table style="width:98%;"><tr><td>';

	   $ittxtv=str_replace('http://', '', $it["SOURCE"]); 
	   $idmn=explode('/', $ittxtv); 


 $it["DETAIL"]=str_replace(' src="//', ' src=#', $it["DETAIL"]);
 $it["DETAIL"]=str_replace(' SRC="//', ' SRC=#', $it["DETAIL"]);

 $it["DETAIL"]=str_replace(' src="/', ' src="http://'.trim($idmn[0]).'/', $it["DETAIL"]);
 $it["DETAIL"]=str_replace(' SRC="/', ' SRC="http://'.trim($idmn[0]).'/', $it["DETAIL"]);

 $it["DETAIL"]=str_replace(' src="http://', ' src=+', $it["DETAIL"]);
 $it["DETAIL"]=str_replace(' SRC="http://', ' SRC=+', $it["DETAIL"]);

 $it["DETAIL"]=str_replace(' src="', ' src="http://'.trim($idmn[0]).'/', $it["DETAIL"]);
 $it["DETAIL"]=str_replace(' SRC="', ' SRC="http://'.trim($idmn[0]).'/', $it["DETAIL"]);

 $it["DETAIL"]=str_replace(' src=+', ' src="http://', $it["DETAIL"]);
 $it["DETAIL"]=str_replace(' SRC=+', ' SRC="http://', $it["DETAIL"]);

 $it["DETAIL"]=str_replace(' src=#', ' src="//', $it["DETAIL"]);
 $it["DETAIL"]=str_replace(' SRC=#', ' SRC="//', $it["DETAIL"]);

 
 echo '<p>'.$it["DETAIL"].'</p>';
 echo '<div style="display:none;"><textarea id="PARS_DETAIL_'.$it["NUMART"].'" name="PARS_DETAIL_'.$it["NUMART"].'">'.$it["DETAIL"].'</textarea></div>';

//  echo '<input name="DETAIL" type="hidden" value="'.$ttxtv.'" />';
echo'</td></tr></table>';
 }






if($it["SOURCE"]) 
{
 echo '<p style="color:#999999;font-size:11px;">'.GetMessage("QTEAM_PARSER_ISTOCNIK").$it["SOURCE"].'</p>';
//  echo '<input name="SOURCE" type="hidden" value="'.$ttxtv.'" />';
 echo '<div style="display:none;"><textarea id="PARS_SOURCE_'.$it["NUMART"].'" name="PARS_SOURCE_'.$it["NUMART"].'">'.$it["SOURCE"].'</textarea></div>';
 }





echo'</td></tr></table></div>';

//echo'<p><hr  style="background-color: #DDDDDD; border: medium none; color: #DDDDDD; height: 1px;">  </p>';
echo'<p>&nbsp;</p>';


/*
if($it->HEADPAGE) 
{
 echo '«аголовок второй страницы: ';
 echo iconv('UTF-8', 'windows-1251', $it->HEADPAGE);
 echo '<br />';
 }
*/








//echo'<pre>'; print_r(iconv('UTF-8', 'windows-1251', $it->FPNAME)); echo'</pre>';

//echo'UID:'.$sort->userid.' Language:'.$sort->language.'<BR>';
//echo'UID:'.$sort->userid.' Language:'.utf_win($sort->language, "w").'<BR>';


echo'</td></tr></table>';
}





?>
<script language="javascript">
$(document).ready(function(){
<?
for ($tmpnumblock=1; $tmpnumblock<=$numblock; $tmpnumblock++)
{
?>

 $("#parslnk<?=$tmpnumblock ?>").click(function () {  if($(this).html()=='<em><?=GetMessageJS("QTEAM_PARSER_SVERNUTQ_TEKST")?></em>') $(this).html('<em><?=GetMessageJS("QTEAM_PARSER_PODROBNOE_OPISANIE")?></em>'); else $(this).html('<em><?=GetMessageJS("QTEAM_PARSER_SVERNUTQ_TEKST")?>');   $("#parsblk<?=$tmpnumblock ?>").slideToggle("slow"); });
 $("#parsblk<?=$tmpnumblock ?>").slideToggle(1);
 
<?
 }
 
?>
 });
</script>



<p>&nbsp;</p>

<p align="center"><input type="submit" value="<?=GetMessage("QTEAM_PARSER_SOHRANITQ")?>" /></p>

</form>






<?

 } // if($arResult["ERROR"])





/*
<div class="news-list">
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>



<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
 	<p class="news-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">


		<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
			<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img class="preview_picture" border="0" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" style="float:left" /></a>
			<?else:?>
				<img class="preview_picture" border="0" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" style="float:left" />
			<?endif;?>
		<?endif?>
		<?if($arParams["DISPLAY_DATE"]!="N" && $arItem["DISPLAY_ACTIVE_FROM"]):?>
			<span class="news-date-time"><?echo $arItem["DISPLAY_ACTIVE_FROM"]?></span>
		<?endif?>
		<?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
			<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
				<a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><b><?echo $arItem["NAME"]?></b></a><br />
			<?else:?>
				<b><?echo $arItem["NAME"]?></b><br />
			<?endif;?>
		<?endif;?>
		<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
			<?echo $arItem["PREVIEW_TEXT"];?>
		<?endif;?>
		<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
			<div style="clear:both"></div>
		<?endif?>
		<?foreach($arItem["FIELDS"] as $code=>$value):?>
			<small>
			<?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?=$value;?>
			</small><br />
		<?endforeach;?>
		<?foreach($arItem["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
			<small>
			<?=$arProperty["NAME"]?>:&nbsp;
			<?if(is_array($arProperty["DISPLAY_VALUE"])):?>
				<?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
			<?else:?>
				<?=$arProperty["DISPLAY_VALUE"];?>
			<?endif?>
			</small><br />
		<?endforeach;?>
	</p>
    
    
<?endforeach;?>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
</div>
*/


 } //  if(CModule::IncludeModule("iblock"))


//echo'+++++++++++++++++++++++++++++++++++++';
//echo'<pre>'; print_r($_SESSION['PARSDATA']); echo'</pre>';


?>