<?$date = COption::GetOptionString("mlife.minilanding", "datecounter", "");?>
<div class="wrapshare">
					<?$APPLICATION->IncludeComponent(
	"mlife:mlife.sharecount",
	"top",
	Array(
		"SHARE_DESC" => "������ 10%",
		"IMG_DESC" => "�������� ������ ����� ������ � ��������",
		"SHAREDATE" => $date,
		"CACHE_TYPE" => "Y",
		"CACHE_TIME" => "1200"
	)
);?> 
					<div class="formShare"></div>
				</div>