<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();


$current_values_ids = array();
$current_values = array();
if (is_array($arParams["CURRENT_VALUES"])) {
	
	foreach ($arParams["CURRENT_VALUES"] as $key => $value) {
		
		if (is_array($value) && !empty($value["elem_id"])) {
	
			$current_values_ids[] = $value["elem_id"];
			
		}	
	}
}
//deb($current_values_ids);
if ($arParams["AJAX"] != "Y") {
/*?>

<div id="<?=$arParams['DIV_ID']?>" class="modal fade">
<?php*/ 
} 

if ($arParams["AJAX_CONTENT"] != "Y") {
?>
	<div class="modal-header">
		<button data-dismiss="modal" class="close">
			&times;
		</button>
		<h2><?=$arParams['HEADLINE']?></h2>
	</div>
	
    <div id="modal-body2" class="modal-body">

    	<div class="popup_siluet">               
             	<input type="hidden" name="material_name_m" id="material_name_m" value="" />
                <input type="hidden" name="material_id_m" id="material_id_m" value="" />
                
<div class="controls">
	<select iblid="<?=$arParams["STD_SIZE_IBLOCK_ID"]?>" name="size_select" id="size_select" multiple="multiple">
	<?php
	foreach ($arResult["SECTIONS"] as $key => $val) {
		$sel = '';
		if (in_array($key, $arParams['SELECTED_SECTION'])) $sel = 'selected="selected"';
		if ($val["VISIBLE"] == true) {
		?><option <?=$sel?> value="<?=$key?>"><?=$val["NAME"]?></option><?
		}
	}
	?>
	</select>
</div>                
				<div id="way" class="way">                  
<?php 
}
?>
					<table class="table table-bordered table-striped material-t">
				    <thead>
				    <tr>
				    <th>ID</th>
				    <th><?=GetMessage("LABEL_NAME")?></th>
				    <th><?=GetMessage("LABEL_CHOSE")?></th>
				    </tr>
				    </thead>
				    <tbody>
				    <?php
				    foreach ($arResult["CLASSIFICATOR"] as $SubData) {				    	
				    	
				    	if ( in_array($SubData['ID'], $current_values_ids )) {
				    	
				    		$classTd = "class='selected'";
				    		$btnClass=" active";
				    		$btnText = GetMessage("LABEL_RECONSIDER");
				    	} else {
				    		$classTd = "";
				    		$btnClass="";
				    		$btnText = GetMessage("LABEL_CHOSE");
				    	}    	
				    	
				    	?>
                        <tr data-size-id="<?=$SubData['ID']?>" data-size-name='<?=$SubData['NAME']?>'  class="addparam" id="size-tr-<?=$SubData['ID']?>">
                        <td <?=$classTd?>><?=$SubData['ID']?></td>
                        <td <?=$classTd?>><?=$SubData['NAME']?></td>
                        <td <?=$classTd?>>
					    <button data-size-id="<?=$SubData['ID']?>" data-size-name="<?=$SubData['NAME']?>" id="btn<?=$SubData['ID']?>"  class="btn btn-success<?=$btnClass?>"><?=$btnText?></button>
					    </td>                     
                        </tr>
                        <?php
                    }
                    ?>
				    </tbody>
				    </table>
				    <div id="navigate-size">
				    <?php
				    echo $arResult["NAV_STRING"];
                    ?>
                    </div>
                    
    <?php 
	if ($arParams["AJAX_CONTENT"]!="Y") {
	?>                
				</div>
                    <div style="clear: both"></div>
                   
                    <div>
                    	<button  id="btnChoose" class="btn btn-success"><?=$arParams['BUTTON_TITLE']?></button>
                    </div>
                    			
		</div>	
	</div>

	<?php //</div>
	}
	
if ($arParams["AJAX"]=="Y") {
	
	//echo "</div>";
	//die();
}
$buffer = ob_get_contents();
ob_end_clean();

$result['result'] = 'OK';
$result['htmlNewSize'] = $arResult["HTML_NEW_SIZE"];
$result['html'] = $buffer;


$siteUTF8 = true;
$rsSites = CSite::GetByID(SITE_ID);
$arSite = $rsSites->Fetch();

if (strtolower($arSite["CHARSET"]) == "windows-1251") {
	$siteUTF8 = false;
}
	
if ($siteUTF8 == false) {
	foreach ($result as $key => $item) {

		//if (is_string($result[$key])) {
			$result[$key] = iconv('windows-1251', 'UTF-8', $result[$key]);
		/*} elseif (is_array($result[$key])) {
			deb($result[$key]);
			foreach ($result[$key] as $k => $v) {
				if (is_string($v)) {
					$result[$key][$k] = iconv('windows-1251', 'UTF-8', $v);
				} elseif (is_array($v))  {
					//deb($result[$key]);
					
					
				}
			}
		}*/
	}
}

$resultJson = json_encode($result);
die($resultJson);
?>
