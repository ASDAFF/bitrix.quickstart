<?include($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");
$template = '/bitrix/templates/iarga.shopplus100.main';
include($_SERVER['DOCUMENT_ROOT'].$template."/inc/functions.php");
IncludeTemplateLangFile($template.'/header.php');
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
CModule::IncludeModule("sale");
if(BX_UTF!='Y') $_POST['q'] = iconv("utf-8","windows-1251",$_POST['q']);

$_POST['q'] = mb_ucfirst(mb_strtolower(trim($_POST['q'])));

$filter = array("LID" => LANGUAGE_ID);
if($_POST['q']!='') $filter["%CITY_NAME"] = $_POST['q'];
$db_vars = CSaleLocation::GetList(array("SORT"=>"ASC"),$filter);
$db_vars->NavStart(10);
while($city = $db_vars->GetNext()):
	if($db_vars->SelectedRowsCount() ==1 && strtolower($city['CITY_NAME']) == strtolower($_POST['q'])) die();
	if(!preg_match("#^".strtolower($_POST['q'])."#i",strtolower($city['CITY_NAME']))) continue;
	if(trim($city['CITY_NAME']=='')) continue;?>
	<a href='#1' class="unitt"><?=$city['CITY_NAME']?></a>
<?endwhile;?>
