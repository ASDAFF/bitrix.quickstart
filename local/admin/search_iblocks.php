<? require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
$APPLICATION->SetTitle('Переиндексация инфоблоков каталога');

CModule::IncludeModule('search');
CModule::IncludeModule('iblock');

$ob = CIBlock::GetList(array("ID"=>"ASC"), array("TYPE" => "mht_products", "SITE_ID" => "el"));
while($ar = $ob->GetNext()){
	if($ar['INDEX_SECTION'] == 'Y'){
		$res = CSearch::Index(
		    "iblock", // Название "модуля". Произвольный идентификатор группы контента на самом деле.
		    "SI".$ar['ID'], // ID элемента. В рамках "модуля".
		    Array(
		        "DATE_CHANGE"=>ConvertTimestamp(false, "FULL"), // Дата изменения
		        "TITLE"=>$ar['NAME'], // Заголовок контента, не участвует в индексе
		        "SITE_ID"=>array("el"),
		        "PARAM1"=>"mht_products", // Параметр 1 и 
		        "PARAM2"=>"".$ar['ID'], // Параметр 2. Используются для фильтрации результатов
		        "PERMISSIONS"=>array("1", "2"), // Группы, которым доступны результаты. 
		        "URL"=>$ar['LIST_PAGE_URL'], // URL контента
		        "BODY"=>str_repeat($ar['NAME']." ", 10), // Тело поискового индекса. Здесь должно быть всё, что должно попасть в индекс
		        "TAGS"=>""
		    ),
		    true // Переиндексировать
		);
		echo "<p>Корневой раздел ".$ar['NAME']." переиндексирован</p>";
	} else {
		CSearch::DeleteIndex("iblock", "SI".$ar['ID']);
	}
}
CSearch::DeleteIndex("main", "el|/index.php");

?>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>