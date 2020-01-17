<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */
use Bitrix\Main\Loader;

Loader::IncludeModule("iblock");
Loader::IncludeModule("sale");
Loader::IncludeModule("highloadblock");
require_once(dirname(__FILE__) . '/phpQuery.php');

$POST_RIGHT = $APPLICATION->GetGroupRight("main");
if ($POST_RIGHT == "D")
    $APPLICATION->AuthForm("Доступ запрещен");


/*************************************************************************
 * Processing of received parameters
 *************************************************************************/
$url = $arParams['URL'];


$arTranslit = array("replace_space" => "-", "replace_other" => "-");
$container = $arParams['CONTAINER'];
$element = $arParams['ELEMENT'];
$name = $arParams['NAME'];
$date = $arParams['DATE'];
$anounce = $arParams['TEXT'];
$html = file_get_contents($url);
$urlscheme = parse_url($url)['scheme'];
$urlhost = parse_url($url)['host'];
$num = 20;
$elementCounter = 0;

$sectionID = 13; //ID аздела для выгрузки товаров
$i = 0;

$bool = 1; // для скрипта
$tmp = array();
$el = new CIBlockElement;
$elSect = new CIBlockSection;
$PROP = array();
$productNum = 0;

function formatPeriod($endtime, $starttime)
{

    $duration = $endtime - $starttime;

    $hours = (int) ($duration / 60 / 60);

    $minutes = (int) ($duration / 60) - $hours * 60;

    $seconds = (int) $duration - $hours * 60 * 60 - $minutes * 60;

    return ($hours == 0 ? "00":$hours) . ":" . ($minutes == 0 ? "00":($minutes < 10? "0".$minutes:$minutes)) . ":" . ($seconds == 0 ? "00":($seconds < 10? "0".$seconds:$seconds));
}
function multi_implode($glue, $array) {
    $_array=array();
    foreach($array as $val)
        $_array[] = is_array($val)? multi_implode($glue, $val) : $val;
    return implode($glue, $_array);
}


$res = CIBlockElement::GetList(false, array("IBLOCK_ID" => "1", "IBLOCK_SECTION_ID" => $sectionID), false, false, array("ID", "NAME", "CODE")); //получаем одинаковые елементы
while ($arRes = $res->Fetch()) {
    $arResult['ITEMS'][] = $arRes['CODE'];
    $arResult['ID'][] = $arRes['ID'];
}

if ($_REQUEST['work_start'] && check_bitrix_sessid()) {
    $start = microtime(true);

    if ($_REQUEST['innerLimit'])
        $innerLimit = $_REQUEST['innerLimit']; // берем значение с степа 2

    if ($_REQUEST['lastPage'])
        $i = intval($_REQUEST["lastPage"]); // берем значение с степа 2

    if ($i == 0) {
        $html = file_get_contents($url);
    } elseif ($i == 1) {
        $newhtml = $url . 'offset' . $num . '/'; // если 2 страница то добавляем в урл просто 20
        $html = file_get_contents($newhtml);
    } elseif ($i > 1) {
        $newhtml = $url . 'offset' . $num * $i . '/'; // если не 2 страница то добавляем в урл 20 * i
        $html = file_get_contents($newhtml);
    }

    $document = phpQuery::newDocument($html);

    $links = $document->find($container)->find($element);

    $pageCount = $document->find('.no_underline')->get(pq('.no_underline')->length-2);
    $pageCount = intval($pageCount->textContent);

    if ($i == 0) {
        $innerLimit = $pageCount * $num; //всего елементов для обсчета страниц
    }

    $elLimit = count($links);
    $elementCounter = $_REQUEST['elementCounter'] += intval(count($links));

    if ($i >= $pageCount - 1) //если текущий I равен колличсеству страниц - 1 для скрипта ниже нужно так, так как он работает на перед
        $innerLimit = $elLimit;

    foreach ($links as $key => $link) {

        $link = pq($link);
        $img = $link->find('img')->attr('src');
        $articule = $link->find('.productCode')->text();
        $price = str_replace(' ', '', explode(" руб", $link->find('.totalPrice_2')->text())[0]);
        $nameElement = $link->find($name)->text();
        $picture = array("SRC" => $urlscheme . '://' . $urlhost . $img);
        $previewText = $link->find($anounce)->text();
        $detailPage = $link->find('a')->attr("href");

        $innerLimit--; //минусуем текущие елементы которые уже занесли в массив

        $trans = Cutil::translit($nameElement, "ru", $arTranslit); //создаем символьный код елемента из имени

        if (in_array($trans . $key, $arResult['ITEMS']))
            continue;

        $JSParam[$key]['NAME'] = str_replace('\'', '"', $nameElement);

        if (stripos($detailPage, 'http') === false)
            $detailUrl = $urlscheme . '://' . $urlhost . $detailPage;
        else
            $detailUrl = $detailPage;

        $docDetails = file_get_contents($detailUrl); //получаем страницу
        $docDetail = phpQuery::newDocument($docDetails);
        $linksDetail = $docDetail->find('.qwes2');
        $brandsDetail = $linksDetail->find('.pqwes3')->text();
        $detail = pq('.product_block');
        $detailText = $detail->find('.cpt_product_description')->html();
        $brandsTrans = Cutil::translit($brandsDetail, "ru", $arTranslit); //создаем символьный код елемента из имени

        $nameSection = $docDetail->find('.cat_info')->children('a')->get(2)->textContent;
        $transSect = Cutil::translit($nameSection, "ru", $arTranslit); //создаем символьный код елемента из имени
        $resSect = CIBlockSection::GetList(false, array("IBLOCK_ID" => "1", "CODE" => $transSect), false, false, array("ID", "NAME", "CODE"))->fetch();
        if(!$resSect > 0) {
            $arLoadSect = Array(
                "MODIFIED_BY" => "1", //айди пользователя для елемента
                "IBLOCK_SECTION_ID" => $sectionID, //айди раздела
                "IBLOCK_ID" => "1",
                "ACTIVE" => "Y",
                "NAME" => $nameSection,
                "CODE" => $transSect,
                "DATE_ACTIVE_FROM" => date('d.m.Y h:m', time())
            );
            $sectID = $elSect->Add($arLoadSect);
        }else{
            $sectID = $resSect['ID'];
        }

        $PROP[4] = $articule; //артикул товара
        $PROP[1] = array("VALUE" => $brandsTrans); //хайлоадблок с брендами
        //$PROP[6] = array("VALUE" => 15);
        $arLoadProductArray = Array(
            "MODIFIED_BY" => "1", //айди пользователя для елемента
            "IBLOCK_SECTION_ID" => $sectID, //айди раздела
            "IBLOCK_ID" => "1",
            "ACTIVE" => "Y",
            "NAME" => $nameElement,
            "CODE" => $trans . $key,
            'PROPERTY_VALUES' => $PROP,
            "DATE_ACTIVE_FROM" => date('d.m.Y h:m', time()),
            'PREVIEW_TEXT' => $previewText,
            'DETAIL_TEXT' => $detailText,
            'DETAIL_TEXT_TYPE' => 'html',
            "PREVIEW_PICTURE" => CFile::MakeFileArray($picture['SRC'])//обязательно через мейкфайл иначе будет пусто
        );//создаем массив с данными которые нужно занести в инфоблок


        if ($PRODUCT_ID = $el->Add($arLoadProductArray)) { //добавляем елемент в инфоблок
            $JSParam[$productNum]['ID'] = $PRODUCT_ID;
            echo 'Длбален ID #' .$PRODUCT_ID;
            $arFields = array( //массив для торгового преложения
                "ID" => $PRODUCT_ID,
                "VAT_INCLUDED" => "Y"
            );
            if (CCatalogProduct::Add($arFields)) { //добавляем в торговый каталог
                $arFields = Array( // массив для прайса торгового каталога
                    "PRODUCT_ID" => $PRODUCT_ID,
                    "CATALOG_GROUP_ID" => 1,
                    "PRICE" => $price,
                    "CURRENCY" => "RUB",
                );
                CPrice::Add($arFields);//добавляем прайс в торговой каталог
            }
            $productNum++;
        } else {
            echo "Error: " . $error = $el->LAST_ERROR; //ошибки
            $JSParam[$productNum]['ERROR'] = $error;
            $productNum++;
        }
    }

    $p = round(100 - (100 * ($innerLimit / ($pageCount * $num))), 2); //считаем проценты
    if ($innerLimit <= 0) {//бывает такое что колличество меньше то скрипт отваливаелся - нужно на всякий случай
        $p = 100;
        $bool = 0;
    }
    if ($elLimit > 0) { // если текущий I меньше числа страниц то передаем параметры в следующий шаг
        ?>
        <script>
            SecondStep('<?=$_REQUEST['microtime'] != "" ? $_REQUEST['microtime'] + round(microtime(true) - $start, 2) : round(microtime(true) - $start, 2)?>', <?=$elementCounter?>, <?=$bool?>, <?=$p?>, <?=intval($i + 1)?>, <?=$innerLimit?>, '<hr><?=$p?>, Текущая страница - <?=$i + 1?> из <?=$pageCount?>, Товаров перенесено - <?=$elementCounter?> осталось <?=$innerLimit?>, Всего за: <?=formatPeriod($_REQUEST['microtime'] + microtime(true), $start)?> сек. <hr><br><b>Запись</b> - <?= multi_implode('<br><b>Запись</b> - ', $JSParam)?>');
        </script>
        <?
    }
    die();// убиваем скрипт что бы перейти на следующий шиг
}


$clean_test_table = '<table id="result_table" cellpadding="0" cellspacing="0" border="0" width="100%" class="internal">'.
    '<tr class="heading">'.
    '<td>Текущее действие</td>'.
    '<td width="1%">&nbsp;</td>'.
    '</tr>'.
    '</table>';


?>
<script type="text/javascript">

    function SecondStep(microtime, elementCounter, boolStep, iPercent, strNextRequest, innerLimit, strCurrentAction)
    {
        try
        {
            document.getElementById('percent').innerHTML = iPercent + '%';
            document.getElementById('indicator').style.width = iPercent + '%';

            document.getElementById('status').innerHTML = 'Работаю...';

            if (strCurrentAction != null)
            {
                oTable = document.getElementById('result_table');
                oRow = oTable.insertRow(0);
                oCell = oRow.insertCell(-1);
                oCell.innerHTML = strCurrentAction;
                oCell = oRow.insertCell(-1);
                oCell.innerHTML = '';
            }

            if (boolStep && document.getElementById('work_start').disabled)
            {
                CHttpRequest.Send('<?= $_SERVER["PHP_SELF"]?>?work_start=Y&lang=<?=LANGUAGE_ID?>&<?=bitrix_sessid_get()?>&lastPage=' + strNextRequest + '&innerLimit=' + innerLimit + '&elementCounter=' + elementCounter + '&microtime=' + microtime);
            }
            else
            {
                set_start(0);
                bWorkFinished = true;
            }
        }
        catch(e)
        {
            CloseWaitWindow();
            document.getElementById('work_start').disabled = '';
            alert('Сбой в получении данных');
        }
    }

    var bWorkFinished = false;
    var bSubmit;

    function set_start(val)
    {
        document.getElementById('work_start').disabled = val ? 'disabled' : '';
        document.getElementById('work_stop').disabled = val ? '' : 'disabled';
        document.getElementById('progress').style.display = val ? 'block' : 'none';

        if (val)
        {
            ShowWaitWindow();
            document.getElementById('result').innerHTML = '<?=$clean_test_table?>';
            document.getElementById('status').innerHTML = 'Работаю...';

            document.getElementById('percent').innerHTML = '0%';
            document.getElementById('indicator').style.width = '0%';

            //CHttpRequest.Action = SecondStep;
            CHttpRequest.Send('<?= $_SERVER["PHP_SELF"]?>?work_start=Y&lang=<?=LANGUAGE_ID?>&<?=bitrix_sessid_get()?>');
        }
        else
            CloseWaitWindow();
    }
</script>

<form method="post" action="<?echo $APPLICATION->GetCurPage()?>" enctype="multipart/form-data" name="post_form" id="post_form">
    <?echo bitrix_sessid_post(); ?>
    <tr>
        <td colspan="2">
            <input type=button value="Старт" id="work_start" onclick="set_start(1)" />
            <input type=button value="Стоп" disabled id="work_stop" onclick="bSubmit=false;set_start(0)" />
            <div id="progress" style="display:none;" width="100%">
                <br />
                <div id="status"></div>
                <table border="0" cellspacing="0" cellpadding="2" width="100%">
                    <tr>
                        <td height="10">
                            <div style="border:1px solid #B9CBDF">
                                <div id="indicator" style="height:10px; width:0%; background-color:#B9CBDF"></div>
                            </div>
                        </td>
                        <td width=30>&nbsp;<span id="percent">0%</span></td>
                    </tr>
                </table>
            </div>
            <div id="result" style="padding-top:10px"></div>
        </td>
    </tr>
</form>