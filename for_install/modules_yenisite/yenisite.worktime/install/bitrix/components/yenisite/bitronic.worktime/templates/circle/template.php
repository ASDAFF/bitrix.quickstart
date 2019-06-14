<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<? $arDays = array('MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY');

foreach ($arDays as $day): ?>
	<div id='ys-timeline'>
		<div
			<? 	if ($arParams[$day]=='Y'):?>
					class="ys-work_circle" 
				<? else: ?>
					class="ys-weekend_circle"
				<?endif?> >	
		</div>
	</div>
<?endforeach; ?>

<script type="text/javascript">
	$(document).ready(function(){
		$('#ys-time-work-circle, #ys-time-weekend-circle').mouseover(function(){
			if($('#ys-lunch').css('display')=="none"){
				$('#ys-lunch').fadeIn('normal');
			}
		});

		$('#ys-time-work-circle, #ys-time-weekend-circle').mouseout(function(){
			if($('#ys-lunch').css('display')=="block"){
				$('#ys-lunch').fadeOut('normal');
			}
		});
	});
</script>

<div id='ys-time-work-circle' > <?=$arParams["TIME_WORK"] ?> </div>
<div id='ys-time-weekend-circle' > <?=$arParams["TIME_WEEKEND"] ?> </div>

<div id='ys-lunch' ><?=$arParams["LUNCH"] ?></div>

