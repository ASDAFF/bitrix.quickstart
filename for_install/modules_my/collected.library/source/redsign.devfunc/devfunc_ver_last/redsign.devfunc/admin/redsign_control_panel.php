<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('RS_CONTROL_PANEL_TITLE'));
?>
<script>
function onIFrameResize(event) {
    var iFrame = BX('rs_cp_frame');
    iFrame.style.height = event.data + "px";
    iFrame.style.display = 'block';
}

if (window.addEventListener) {
    window.addEventListener("message", onIFrameResize, false);
} else if (window.attachEvent) {
    window.attachEvent("onmessage", onIFrameResize);
}
</script>
<div>
<style>
#rs_cp_frame {
    width: 100%;
    max-width:100%;
    border:0;
    display: none;
}
</style>
<iframe id="rs_cp_frame" src="https://www.redsign.ru/control-panel/main.php?911"></iframe>

</div>
<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");