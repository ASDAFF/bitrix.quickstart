<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<script type="text/javascript">
	//Init block
	pathExec = "<?=$this->GetFolder()?>/req.php";
	imgPath = "<?=$arParams["IMAGE_DIALOG"]?>";
	bGlobal = "<?=$arParams["USE_GLOBAL"]?>";
	
	//Init filter	
	var formName = new Array();
	<?foreach($arParams["FORM_NAME"] as $name):?>
		<?if ($name):?>
			formName.push ("<?=$name?>");
		<?endif?>
	<?endforeach?>
	arr =  new Array("captcha_sid", "captcha_code"); 
	for(var z=0; z<arr.length; z++) {
		items = document.getElementsByName(arr[z]);
		for(var i=0; i<items.length; i++) {
			for (var j=0; j<formName.length || bGlobal;j++){
				if (bGlobal || findParent(items[i], formName[j])){
					addImageObj (items[i].parentNode)
					break;
				}
			}
		}
	}
</script>
