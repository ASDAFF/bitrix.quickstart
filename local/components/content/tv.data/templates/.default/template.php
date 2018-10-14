<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(method_exists($this, 'setFrameMode')) $this->setFrameMode(true);?>

<div class='ys-timeline'>
<? $arDays = array('MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY');
foreach ($arDays as $day): ?>
	<div
		<? 	if ($arParams[$day]=='Y'):?>
				class="ys-work"
			<? else: ?>
				class="ys-weekend"
			<?endif?> >	
	</div>
<?endforeach; ?>

<div class='ys-time-work' > <?=$arParams["TIME_WORK"] ?> </div>
<div class='ys-time-weekend' > <?=$arParams["TIME_WEEKEND"] ?> </div>

<div class='ys-lunch' ><?=$arParams["LUNCH"] ?></div>

</div>