<?$date = COption::GetOptionString("mlife.minilanding", "datecounter", "");?>
<div class="wrapshare">
					<?$APPLICATION->IncludeComponent("mlife:mlife.sharecount", "top", array(
	"CACHE_TYPE" => "Y",
	"CACHE_TIME" => "1200",
	"SHAREDATE" => $date,
	"SHARE_DESC" => "������ �������  ���������!",
	"IMG_DESC" => "�������� ������ ����� ������ � ��������"
	),
	false
);?> 
					<div class="formShare"></div>
				</div>