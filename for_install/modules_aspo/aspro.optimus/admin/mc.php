<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
if($_SERVER['REQUEST_METHOD'] === 'GET'){
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
}

IncludeModuleLangFile(__FILE__);
$moduleID = 'aspro.optimus';

if($_SERVER['REQUEST_METHOD'] === 'GET'){
	$GLOBALS['APPLICATION']->SetTitle(GetMessage('OPTIMUS_CONTROL_CENTER_TITLE'));
}

$RIGHT = $GLOBALS['APPLICATION']->GetGroupRight($moduleID);
if($RIGHT >= 'R'){
	if(CModule::IncludeModule($moduleID)){
		if($_SERVER['REQUEST_METHOD'] === 'GET'){
			CJSCore::Init(array('jquery'));
			?>
			<link href="/bitrix/css/<?=$moduleID?>/style.css" type="text/css" rel="stylesheet" />
			<div id="aspro_admin_area">
				<div class="aspro-admin-waiter"></div>
			</div>
			<script type="text/javascript">
			$.ajax({
				type: 'POST',
				dataType: 'html',
				success: function(html){
					$('#aspro_admin_area').append(html);
				},
				error: function(){
					$('#aspro_admin_area').addClass('aspro-admin-ready');
					$('#aspro_admin_area').append('<div class="adm-info-message-wrap adm-info-message-red"><div class="adm-info-message"><div class="adm-info-message-title"><?=GetMessage('OPTIMUS_MODULE_CONTROL_CENTER_ERROR')?></div><div class="adm-info-message-icon"></div></div></div>');
				}
			});
			</script>
			<?
		}
		else{
			$m = COptimusTools::___1595018847();
			?>
			<iframe src="https://aspro.ru/mc/?<?=$m?>"></iframe>
			<script type="text/javascript">
			$(document).ready(function() {
				var asproAdminArea = $('#aspro_admin_area');
				var asproIframe = $('#aspro_admin_area iframe');
				if(asproIframe.length){
					_checkAdminAreaResized = function() {
						asproAdminArea.removeAttr('style');
						var wh = $('#adm-workarea').height();
						var bh = $('#main_navchain').outerHeight();
						var th = $('#adm-title').outerHeight() + parseInt($('#adm-title').css('margin-top')) + parseInt($('#adm-title').css('margin-bottom'));
						asproAdminArea.height(wh-bh-th);

						return $('#menucontainer').height();
					}

					asproIframe.load(function() {
						asproAdminArea.addClass('aspro-admin-ready');
					});

					var h = 0;
					setInterval(function() {
						if (h !== $('#menucontainer').height()) {
							h = _checkAdminAreaResized();
						}
					}, 200);
					$(window).resize(function() {
						h = _checkAdminAreaResized();
					});
				}
			});
			</script>
			<?
		}
	}
	else{
		CAdminMessage::ShowMessage(GetMessage('OPTIMUS_MODULE_NOT_INCLUDED'));
	}
}
else{
	CAdminMessage::ShowMessage(GetMessage('OPTIMUS_NO_RIGHTS_FOR_VIEWING'));
}
?>
<?
if($_SERVER['REQUEST_METHOD'] === 'GET'){
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
}
?>