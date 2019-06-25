<?php
    $bEnabled = false;
    
    $oDate = strtotime($arResult["PROPERTIES"]["CML2_DAY_PROD"]["VALUE"]);
    $oDateCurrent = time();
    
    if ($oDateCurrent < $oDate)
        $bEnabled = true;
    
	$arMonths = array(
		"January" => GetMessage('PRODUCT_OF_DAY_MONTH_JANUARY'), 
		"February" => GetMessage('PRODUCT_OF_DAY_MONTH_FEBRUARY'), 
		"March" => GetMessage('PRODUCT_OF_DAY_MONTH_MARCH'),
		"April" => GetMessage('PRODUCT_OF_DAY_MONTH_APRIL'),
		"May" => GetMessage('PRODUCT_OF_DAY_MONTH_MAY'),
		"June" =>GetMessage('PRODUCT_OF_DAY_MONTH_JUNE'),
		"July" => GetMessage('PRODUCT_OF_DAY_MONTH_JULY'),
		"August" => GetMessage('PRODUCT_OF_DAY_MONTH_AUGUST'),
		"September" => GetMessage('PRODUCT_OF_DAY_MONTH_SEPTEMBER'),
		"October" => GetMessage('PRODUCT_OF_DAY_MONTH_OCTOBER'),
		"November" => GetMessage('PRODUCT_OF_DAY_MONTH_NOVEMBER'),
		"December" => GetMessage('PRODUCT_OF_DAY_MONTH_DECEMBER')							   
	);
     
	$sMonth = $arMonths[date("F", $oDate)];
	$iDay = date("j", $oDate);	
?>
	<div class="row">
		<div class="product-of-day">
			<div class="valign"></div>
			<div class="day">
				<div class="number"><?=$iDay?></div>
				<div class="month"><?=$sMonth?></div>
			</div>
			<div class="title">
				<?if ($bEnabled):?>
					<?=GetMessage('PRODUCT_OF_DAY_BUY')?>
				<?else:?>
					<?=GetMessage('PRODUCT_OF_DAY_COMPLETED')?>
				<?endif;?>
			</div>
			<?if ($bEnabled):?>
				<div class="timer">
					<?$data=date("j F Y",strtotime($arResult["PROPERTIES"]["CML2_DAY_PROD"]["VALUE"]));?>
					<div class="valign"></div>
					<div class="title"><?=GetMessage('PRODUCT_OF_DAY_TIME_TO_END')?> </div>
					<input type="hidden" id="timer" value="<?=$data?>"/>
					<div class="countdown">
						<table id="countdown">
							<tr>
								<td id="pd_num_day" class="pd_td"></td>
								<td class="pd_num_separator">:</td>
								<td id="pd_num_hour" class="pd_td"></td>
								<td class="pd_num_separator">:</td>
								<td id="pd_num_min" class="pd_td"></td>
								<td class="pd_num_separator">:</td>
								<td id="pd_num_sec" class="pd_td"></td>
							</tr>						
						</table>					
					</div>
				</div>
				<script type="text/javascript">
					start_conuntdown();
					function start_conuntdown(){
						var data = document.getElementById("timer").value;
						var today = new Date().getTime();
						var end = new Date(data).getTime();
						var dateX = new Date(end-today);
						var perDays = 60*60*1000*24;

						var pd_num_day = String(Math.floor(dateX/perDays));
						if(pd_num_day){
							pd_num_day=pd_num_day.length;
							if (pd_num_day>1) {
								document.getElementById("pd_num_day").innerHTML = Math.round(dateX/perDays);
							} else {
								document.getElementById("pd_num_day").innerHTML = '0'+Math.round(dateX/perDays);
							}
							
							var pd_num_hour = dateX.getUTCHours().toString();
							pd_num_hour=pd_num_hour.length;
							if (pd_num_hour>1) {
								document.getElementById("pd_num_hour").innerHTML = dateX.getUTCHours().toString();
							} else {
								document.getElementById("pd_num_hour").innerHTML = '0'+dateX.getUTCHours().toString();
							}
							
							var pd_num_min = dateX.getMinutes().toString();
							pd_num_min=pd_num_min.length;
							if (pd_num_min>1) {
								document.getElementById("pd_num_min").innerHTML = dateX.getMinutes().toString();
							} else {
								document.getElementById("pd_num_min").innerHTML = '0'+dateX.getMinutes().toString();
							}
							
							var pd_num_sec = dateX.getSeconds().toString();
							pd_num_sec=pd_num_sec.length;
							if (pd_num_sec>1) {
								document.getElementById("pd_num_sec").innerHTML = dateX.getSeconds().toString();
							} else {
								document.getElementById("pd_num_sec").innerHTML = '0'+dateX.getSeconds().toString();
							}
						}
					}
					setInterval(start_conuntdown, 1000);   /* даем интервал вызова функции в 1 секунду */
				</script>
			<?endif;?>
		</div>
	</div>
	<div class="startshop-indents-vertical indent-30"></div>