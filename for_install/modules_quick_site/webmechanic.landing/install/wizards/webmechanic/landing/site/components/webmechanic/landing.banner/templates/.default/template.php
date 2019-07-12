<? 
$module_id = "webmechanic.landing";
CModule::IncludeModule($module_id);
$count = count($arResult);
?>

<? if($count > 0): ?>
	<?
		if($count == 1) {
			$class = 'col-sm-12 col-xs-12';
		}
		elseif($count == 2) {
			$class = 'col-sm-6 col-xs-6';
		}
		elseif($count == 3) {
			$class = 'col-sm-4 col-xs-6';
		}
		else {
			$class = 'col-sm-3 col-xs-6';
		}
	?>

	<div class="banners row">
		<? for($i = 0; $i < sizeof($arResult); $i++): ?>
          <div class="text-center <?=$class;?>">
            <img src="<?=$arResult[$i]['DETAIL_PICTURE'];?>" alt="<?=$arResult[$i]['NAME'] ?>" class="img-responsive">
          </div>
        <? endfor ?>
	</div>
<? endif; ?>
