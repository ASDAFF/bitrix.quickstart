<?
set_time_limit(0);

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('NO_AGENT_CHECK', true);
define("STATISTIC_SKIP_ACTIVITY_CHECK", true);

$SHOP_ID = 1;
if (isset($_REQUEST['ID']) && IntVal($_REQUEST['ID']) > 0)
    $SHOP_ID = IntVal($_REQUEST['ID']);

$MODULE_ID = "mibix.yamexport";
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../../../../');
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
if (!CModule::IncludeModule($MODULE_ID) || !CModule::IncludeModule("iblock")) return;
CJSCore::Init(array('ajax'));
?>
<html>
<head>
    <?$APPLICATION->ShowHead();?>
    <script>
    BX.ready(function() {

        var cur_step = 0;

        // функция обработки шага
        var ajaxGetStepYML = function() {

            BX.showWait();

            var divNode = BX('mibix_yamexport_info');
            cur_step++;

            var postData = {
                'sessid': BX.bitrix_sessid(),
                'site_id': BX.message('SITE_ID'),
                'action': 'get_step_yml',
                'shop_id': <?=$SHOP_ID?>
            };

            BX.ajax({
                url: '/bitrix/admin/mibix.yamexport_ajax.php',
                method: 'POST',
                data: postData,
                dataType: 'json',
                onsuccess: function (result) {
                    BX.closeWait();

                    // выводим информацию о выгрузке на экран
                    divNode.appendChild(BX.create('p', {
                        html: '[#' + cur_step + '] => ' + result['STEP_TIME']
                    }));

                    // рекурсионный запрос в случае незавершенной выгрузки
                    if(result['CREATE_YML_PROCCESS'] == "Y")
                        ajaxGetStepYML();
                }
            });
        }

        // инициализация
        ajaxGetStepYML();

    });
    </script>
</head>
<body>
<div id="mibix_yamexport_info"></div>
</body>
</html>
<?require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/epilog_after.php");?>
