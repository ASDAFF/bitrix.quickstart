<?

$aMenuLinksExt = array(
	array("Оформление", SITE_DIR."info/more/typograpy/", array(), array("FROM_IBLOCK" => 1, "DEPTH_LEVEL" => 2, "IS_ITEM" => 1)),
	array("Кнопки", SITE_DIR."info/more/buttons/", array(), array("FROM_IBLOCK" => 1, "DEPTH_LEVEL" => 2, "IS_ITEM" => 1)),
	array("Элементы", SITE_DIR."info/more/elements/", array(), array("FROM_IBLOCK" => 1, "DEPTH_LEVEL" => 2, "IS_ITEM" => 1)),
);

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
?>
