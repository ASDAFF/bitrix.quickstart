<?$date = COption::GetOptionString("mlife.minilanding", "datecounter", "");?>
<div class="wrapshare">
					<?$APPLICATION->IncludeComponent("mlife:mlife.sharecount", "top", array(
	"CACHE_TYPE" => "Y",
	"CACHE_TIME" => "1200",
	"SHAREDATE" => $date,
	"SHARE_DESC" => "� �������� ������",
	"IMG_DESC" => "�������� ������ ����� ������",
	"SHARE_DESC3" => "700\$",
	"SHARE_DESC4" => "560\$"
	),
	false
);?> 
					<div class="formShare"></div>
				</div>