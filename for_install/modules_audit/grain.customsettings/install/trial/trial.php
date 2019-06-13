<?
IncludeModuleLangFile(__FILE__);
?>
<?
$now=time();
?>
<?if(defined("grain_customsettings_OLDSITEEXPIREDATE")):?>
	<?if($now<grain_customsettings_OLDSITEEXPIREDATE):?>
		<?echo BeginNote();?>
			<?
    		$expire_arr = getdate(grain_customsettings_OLDSITEEXPIREDATE);
    		$expire_date = gmmktime($expire_arr["hours"],$expire_arr["minutes"],$expire_arr["seconds"],$expire_arr["mon"],$expire_arr["mday"],$expire_arr["year"]);
			$now_arr = getdate($now);
    		$now_date = gmmktime($expire_arr["hours"],$expire_arr["minutes"],$expire_arr["seconds"],$now_arr["mon"],$now_arr["mday"],$now_arr["year"]);
    		$days=($expire_date-$now_date)/86400; 
			?>
			<?=GetMessage("GCUSTOMSETTINGS_TRIAL_MESSAGE_BEFORE",Array("#DAYS#"=>$days))?>
		<?echo EndNote();?>
	<?else:?>
		<?echo BeginNote();?>
			<?=GetMessage("GCUSTOMSETTINGS_TRIAL_MESSAGE_AFTER")?>
		<?echo EndNote();?>
	<?endif?>
<?else:?>
	<?echo BeginNote();?>
		<?=GetMessage("GCUSTOMSETTINGS_TRIAL_MESSAGE")?>
	<?echo EndNote();?>
<?endif?>
