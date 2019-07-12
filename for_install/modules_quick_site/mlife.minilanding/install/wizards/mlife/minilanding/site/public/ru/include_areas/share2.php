<?$date = COption::GetOptionString("mlife.minilanding", "datecounter", "");?>
<div class="wrapshare">
					<?$APPLICATION->IncludeComponent("mlife:mlife.sharecount", "top", array(
	"CACHE_TYPE" => "Y",
	"CACHE_TIME" => "1200",
	"SHAREDATE" => $date,
	"SHARE_DESC" => "первое занятие  бесплатно!",
	"IMG_DESC" => "Оставьте заявку прямо сейчас и получите"
	),
	false
);?> 
					<div class="formShare"></div>
				</div>