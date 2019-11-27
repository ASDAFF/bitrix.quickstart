<?
$arFOp = Array();
$arFOp[] = Array('byteeightlab_sitemap_view_all_settings', 'byteeightlab.sitemap', '', 'module');
$arFOp[] = Array('byteeightlab_sitemap_view_stats', 'byteeightlab.sitemap', '', 'module');
$arFOp[] = Array('byteeightlab_sitemap_edit_all_settings', 'byteeightlab.sitemap', '', 'module');

$arTasksF = Array();
$arTasksF[] = Array('byteeightlab_sitemap_denied', 'D', 'byteeightlab.sitemap', 'Y', '', 'module');
$arTasksF[] = Array('byteeightlab_sitemap_view_stats', 'L', 'byteeightlab.sitemap', 'Y', '', 'module');
$arTasksF[] = Array('byteeightlab_sitemap_view_settings', 'R', 'byteeightlab.sitemap', 'Y', '', 'module');
$arTasksF[] = Array('byteeightlab_sitemap_edit_settings', 'W', 'byteeightlab.sitemap', 'Y', '', 'module');

$arOInT = Array();
$arOInT['byteeightlab_sitemap_view_stats'] = Array('byteeightlab_sitemap_view_stats');
$arOInT['byteeightlab_sitemap_view_settings'] = Array('byteeightlab_sitemap_view_all_settings');
$arOInT['byteeightlab_sitemap_edit_settings'] = Array('byteeightlab_sitemap_edit_all_settings');

foreach($arFOp as $ar){
	$DB->Query("INSERT INTO b_operation (NAME,MODULE_ID,DESCRIPTION,BINDING) VALUES ('".$ar[0]."','".$ar[1]."','".$ar[2]."','".$ar[3]."')",
		false, "FILE: ".__FILE__."<br> LINE: ".__LINE__
	);
}
foreach($arTasksF as $ar){
	$DB->Query("INSERT INTO b_task (NAME,LETTER,MODULE_ID,SYS,DESCRIPTION,BINDING) VALUES ('".$ar[0]."','".$ar[1]."','".$ar[2]."','".$ar[3]."','".$ar[4]."','".$ar[5]."')",
		false, "FILE: ".__FILE__."<br> LINE: ".__LINE__
	);
}

$sql_str = "
	INSERT INTO b_group_task (GROUP_ID,TASK_ID)
	SELECT MG.GROUP_ID, T.ID
	FROM b_task T
	INNER JOIN b_module_group MG ON MG.G_ACCESS = T.LETTER
	WHERE T.SYS = 'Y' AND T.BINDING = 'module' AND MG.MODULE_ID = 'byteeightlab.sitemap' AND T.MODULE_ID = MG.MODULE_ID";
$z = $DB->Query($sql_str, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

foreach($arOInT as $tname => $arOp){
	$sql_str = "
		INSERT INTO b_task_operation (TASK_ID,OPERATION_ID)
		SELECT T.ID, O.ID
		FROM b_task T,b_operation O
		WHERE T.SYS='Y' AND T.NAME='".$tname."' AND O.NAME in ('".implode("','", $arOp)."')";
	$z = $DB->Query($sql_str, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
}
?>