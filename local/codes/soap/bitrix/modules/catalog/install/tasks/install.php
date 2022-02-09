<?
// *******************************************************************************************************
// Install new right system: operation and tasks
// *******************************************************************************************************
// ############ CATALOG MODULE OPERATION ###########
$arFOp = Array();
$arFOp[] = Array('catalog_read', 'catalog', '', 'module');
$arFOp[] = Array('catalog_settings', 'catalog', '', 'module');
$arFOp[] = Array('catalog_price', 'catalog', '', 'module');
$arFOp[] = Array('catalog_group', 'catalog', '', 'module');
$arFOp[] = Array('catalog_discount', 'catalog', '', 'module');
$arFOp[] = Array('catalog_vat', 'catalog', '', 'module');
$arFOp[] = Array('catalog_export_edit', 'catalog', '', 'module');
$arFOp[] = Array('catalog_export_exec', 'catalog', '', 'module');
$arFOp[] = Array('catalog_import_edit', 'catalog', '', 'module');
$arFOp[] = Array('catalog_import_exec', 'catalog', '', 'module');
$arFOp[] = Array('catalog_store', 'catalog', '', 'module');


// ############ CATALOG MODULE TASKS ###########
$arTasksF = Array();
$arTasksF[] = Array('catalog_denied', 'D', 'catalog', 'Y', '', 'module');
$arTasksF[] = Array('catalog_read', 'R', 'catalog', 'Y', '', 'module');
$arTasksF[] = Array('catalog_price_edit', 'T', 'catalog', 'Y', '', 'module');
$arTasksF[] = Array('catalog_export_import', 'U', 'catalog', 'Y', '', 'module');
$arTasksF[] = Array('catalog_full_access', 'W', 'catalog', 'Y', '', 'module');


//Operations in Tasks
$arOInT = Array();
//CATALOG: module
$arOInT['catalog_read'] = Array(
	'catalog_read',
);

$arOInT['catalog_price_edit'] = Array(
	'catalog_read',
	'catalog_price',
	'catalog_group',
	'catalog_discount',
	'catalog_vat',
	'catalog_store',
);

$arOInT['catalog_export_import'] = Array(
	'catalog_read',
	'catalog_export_edit',
	'catalog_export_exec',
	'catalog_import_edit',
	'catalog_import_exec',
);

$arOInT['catalog_full_access'] = Array(
	'catalog_read',
	'catalog_settings',
	'catalog_price',
	'catalog_group',
	'catalog_discount',
	'catalog_vat',
	'catalog_store',
	'catalog_export_edit',
	'catalog_export_exec',
	'catalog_import_edit',
	'catalog_import_exec',
);

foreach($arFOp as $ar)
	$DB->Query("
		INSERT INTO b_operation
		(NAME,MODULE_ID,DESCRIPTION,BINDING)
		VALUES
		('".$ar[0]."','".$ar[1]."','".$ar[2]."','".$ar[3]."')
	", false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

foreach($arTasksF as $ar)
	$DB->Query("
		INSERT INTO b_task
		(NAME,LETTER,MODULE_ID,SYS,DESCRIPTION,BINDING)
		VALUES
		('".$ar[0]."','".$ar[1]."','".$ar[2]."','".$ar[3]."','".$ar[4]."','".$ar[5]."')
	", false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
// ############ b_group_task ###########
$sql_str = "
	INSERT INTO b_group_task
	(GROUP_ID,TASK_ID)
	SELECT MG.GROUP_ID, T.ID
	FROM
		b_task T
		INNER JOIN b_module_group MG ON MG.G_ACCESS = T.LETTER
	WHERE
		T.SYS = 'Y'
		AND T.BINDING = 'module'
		AND MG.MODULE_ID = 'catalog'
		AND T.MODULE_ID = MG.MODULE_ID
";
$z = $DB->Query($sql_str, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

// ############ b_task_operation ###########
foreach($arOInT as $tname => $arOp)
{
	$sql_str = "
		INSERT INTO b_task_operation
		(TASK_ID,OPERATION_ID)
		SELECT T.ID, O.ID
		FROM
			b_task T
			,b_operation O
		WHERE
			T.SYS='Y'
			AND T.NAME='".$tname."'
			AND O.NAME in ('".implode("','", $arOp)."')
	";
	$z = $DB->Query($sql_str, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
}
?>