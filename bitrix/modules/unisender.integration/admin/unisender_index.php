<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
$APPLICATION->SetTitle("UniSender");
//$APPLICATION->AddHeadScript('http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js');
//$APPLICATION->AddHeadScript('/bitrix/js/unisender/js.js');

require_once ($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

require_once $_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/unisender.integration/include.php";

echo "<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js\"></script>";
echo "<script type=\"text/javascript\" src=\"/bitrix/js/unisender/js.js\"></script>";

IncludeModuleLangFile(__FILE__);

$API_KEY = COption::GetOptionString($module_id, "UNISENDER_API_KEY");
if (empty($API_KEY))
{
	echo "<span class=\"errortext\">".GetMessage("UNI_API_KEY_EMPTY", array("#MODULE_ID#"=>$module_id))."</span>";
}
else
{	
	$API = new UniAPI($API_KEY);
	if (($lists = $API->getLists())!==false)
	{
		if (count($lists)>0)
		{
			if (($fields = $API->getFields(array('string', 'text')))!==false)
			{
				?>
				<div class="uni_logo">UniSender</div>
				<div class="uni_export_form">
				<form method="post" id="export_form" action="unisender_export.php">
					<fieldset>
						<legend><?=GetMessage("UNI_GROUPS")?></legend>
						<div class="uni_fieldset_content">
						<?php
						$filter = Array
						(
							"ACTIVE"         => "Y",
							"USERS_1"	=> 1
						);
						$rsGroups = CGroup::GetList(($by="c_sort"), ($order="asc"), $filter, "Y"); // ???????? ??????
						$i = 1;
						while($group = $rsGroups->Fetch())
						{
							//print_r($group);
							if ($i==2) $checked = "checked=\"checked\""; else $checked = "";
							?>
							<input name="groups[]" id="group<?=$group['ID']?>" <?=$checked?>type="checkbox" class="groups" value="<?=$group['ID']?>" />
							<label for="group<?=$group['ID']?>"><?=$group['NAME']?> (<a href="<?=BX_ROOT?>/admin/user_admin.php?lang=ru&find_group_id[]=<?=$group['ID']?>&set_filter=Y" title="<?=GetMessage("UNI_USERS_LINK_TITLE")?>" target="_blank"><?=$group['USERS']?></a>)</label><br/>
							<?
							$i++;
						}
						?>
						</div>
					</fieldset><br/>
					<fieldset>
						<legend><?=GetMessage("UNI_DATA")?></legend>
						<div class="uni_fieldset_content">
						<table class="uni_fields_table">
						<tr>
							<td width="200px"><?=GetMessage("UNI_FIELD_EMAIL")?></td>
							<td>
							<select name="email" disabled="disabled">
								<option value="1"><?=GetMessage("UNI_FIELDS_INVARIABLE")?></option>
							</select>
							</td>
						</tr>
						<tr>
							<td><?=GetMessage("UNI_FIELD_MOBILE")?></td>
							<td>
							<select name="phone">
								<option value="0"><?=GetMessage("UNI_FIELDS_NOTIMPORT")?></option>
								<option value="1"><?=GetMessage("UNI_FIELDS_IMPORT")?></option>
							</select>
							</td>
						</tr>
						<tr>
							<td><?=GetMessage("UNI_FIELD_NAME")?></td>
							<td>
							<select name="name" class="fields_group" id="name">
								<option value="0"><?=GetMessage("UNI_FIELDS_NOTIMPORT")?></option>
								<?php foreach ($fields as $field): ?>
								<option value="<?=$field['id']?>"><?=$field['name']?></option>
								<?php endforeach; ?>
							</select>
							</td>
						</tr>
						<tr>
							<td><?=GetMessage("UNI_FIELD_FAM")?></td>
							<td>
							<select name="fam" class="fields_group" id="fam">
								<option value="0"><?=GetMessage("UNI_FIELDS_NOTIMPORT")?></option>
								<?php foreach ($fields as $field): ?>
								<option value="<?=$field['id']?>"><?=$field['name']?></option>
								<?php endforeach; ?>
							</select>
							</td>
						</tr>
						<tr>
							<td><?=GetMessage("UNI_FIELD_OTCH")?></dtd>
							<td>
							<select name="otch" class="fields_group" id="otch">
								<option value="0"><?=GetMessage("UNI_FIELDS_NOTIMPORT")?></option>
								<?php foreach ($fields as $field): ?>
								<option value="<?=$field['id']?>"><?=$field['name']?></option>
								<?php endforeach; ?>
							</select>
							</td>
						</tr>
						</table>
						<span class="uni_notetext"><?=GetMessage("UNI_FIELDS_LINK")?></span>
						</div>
					</fieldset>
					<br>

					<fieldset>
						<legend><?=GetMessage("UNI_FS_LISTS")?></legend>
						<div class="uni_fieldset_content">
        					<table class="uni_fields_table">
        					<tr>
        						<td width="200px">
        							<?=GetMessage("UNI_LIST")?>
        						</td>
        						<td>
        							<select name="list_id">
        								<?php foreach ($lists as $list): ?>
        								<option value="<?=$list['id']?>"><?=$list['title']?></option>
        								<?php endforeach; ?>
        							</select>
        						</td>
        					</tr>
        					</table>
						<span class="uni_notetext"><?=GetMessage("UNI_LISTS_LINK")?></span>
        					</div>
					</fieldset>

					<dl class="submit_bt">
						<dt><input type="submit" name="export" value="<?=GetMessage("UNI_IMPORT_BT")?>" /></dt>
						<dd>
							<!--input name="open_unisender" id="open_unisender" checked="checked" type="checkbox" value="1" />
							<label for="open_unisender">??????? UniSender ????? ???????? ??? ???????? ????????</label><br/-->
						</dd>
					</dl>
					<input type="hidden" name="API_KEY" value="<?=$API_KEY?>" />
				</form>
				</div>
				<?
			}
			else
			{
				$API->showError();
			}
		}
		else
			echo "<span class=\"errortext\">".GetMessage("UNI_LISTS_NOTFOUND")."</span>";
	}
	else
	{
		$API->showError();
	}
}

require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
?>