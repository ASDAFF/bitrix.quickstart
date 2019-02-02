<?
	/*
		Bitrix Cleaner v2.1 - https://github.com/creadome/bitrixcleaner
		Быстрая очистка 1С-Битрикс 		 				

		(c) 2015 Станислав Васильев - http://creado.me
		creadome@gmail.com
	*/

	if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

	CJSCore::Init('jquery');
?>

<div id="bitrix-cleaner"></div>

<style>
	#bitrix-cleaner { background: #f5f9f9; border-top: 1px solid #d7e0e8; }

	#bitrix-cleaner table { width: 100%; border-collapse: collapse; }
	#bitrix-cleaner th,
	#bitrix-cleaner td { vertical-align: top; text-align: left; padding: 10px; border-bottom: 1px solid #fff; }

	#bitrix-cleaner td.clean { text-align: right; }
	#bitrix-cleaner td.clean span { color: #f00; border-bottom: 1px dotted #f00; cursor: pointer; }

	#bitrix-cleaner input { margin: 10px; }
</style>

<script>
	var cleaner = '<?=$arGadget['PATH_SITEROOT']?>/cleaner.php';

	$('#bitrix-cleaner').load(cleaner);

	$(document).on('click', '#bitrix-cleaner .action-clean', function(){
		$.get(cleaner, {clean: $(this).data('clean')}, function(data){
			$('#bitrix-cleaner').html(data);
		});
	});
</script>