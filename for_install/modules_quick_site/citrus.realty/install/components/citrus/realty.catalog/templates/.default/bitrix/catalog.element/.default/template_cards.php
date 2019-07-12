<?

// nook add image title from SEO module
$addImage = function($id, $title='', $size = '300x225') use (&$images, $arResult)
{
	$path = CFile::GetPath($id);
	if ($path)
	{
		$sizes = array_combine(array('width', 'height'), explode('x', $size));
		$preview = CFile::ResizeImageGet($id, $sizes, BX_RESIZE_IMAGE_EXACT, $bInitSizes = true);
		$file = CFile::GetByID($id)->GetNext(true, false);
		return array(
			'src' => CFile::GetPath($id),
			'preview' => $preview,
			'title' => strlen($title) ? $title : (strlen($file["DESCRIPTION"]) ? $file["DESCRIPTION"] : pathinfo($file["ORIGINAL_NAME"], PATHINFO_FILENAME)),
			'filename' => $file["ORIGINAL_NAME"],
		);
	}
};

$images = $layouts = array();
if (is_array($arResult["DETAIL_PICTURE"]))
	$images[] = $addImage($arResult["DETAIL_PICTURE"]["ID"], $arResult["NAME"]);
elseif (is_array($arResult["PREVIEW_PICTURE"]))
	$images[] = $addImage($arResult["PREVIEW_PICTURE"]["ID"], $arResult["NAME"]);
if (is_array($arResult["PROPERTIES"]["photo"]['VALUE']))
	foreach ($arResult["PROPERTIES"]["photo"]['VALUE'] as $id)
		$images[] = $addImage($id, '', '100x100');
if (is_array($arResult["PROPERTIES"]["layouts"]['VALUE']))
	foreach ($arResult["PROPERTIES"]["layouts"]['VALUE'] as $id)
		$layouts[] = $addImage($id, '', '312x312');
?>
<div class="detail">
	<?
	if (!empty($images))
	{
		$image = array_shift($images);
		?><a href="<?=$image['src']?>" title="<?=$image['title']?>" target="_blank" class="fancybox" rel="offer"><img src="<?=$image['preview']['src']?>" alt="<?=$image['title']?>" width="<?=$image['preview']['width']?>" height="<?=$image['preview']['height']?>"></a><?;
	}

	if (!empty($arResult["PREVIEW_TEXT"]))
	{
		?><div class="detail-description"><?=$arResult["PREVIEW_TEXT"]?></div><?
	}
	?>
</div>
<?
$tabs = array();

if (!empty($arResult["DETAIL_TEXT"]))
	$tabs["about"] = array(
		"title" => GetMessage("CITRUS_REALTY_TAB_ABOUT"),
		"html" => $arResult["DETAIL_TEXT"],
	);

if (is_array($prop = $arResult["PROPERTIES"]['address']) && !empty($prop["VALUE"]))
{
	ob_start();

	if (is_array($text = $arResult["PROPERTIES"]["text_location"]))
	{
        if (is_array($text["VALUE"])) {
            $value = $text["VALUE"]["TYPE"] != 'text' ? $text["~VALUE"]["TEXT"] : $text["VALUE"]["TEXT"];
        } else {
            $value = $text["VALUE"];
        }
		?><div class="detail-location-description"><?=$value?></div><?
	}

	?>
	<div id="detailMap" data-address="<?=$prop["VALUE"]?>" style="width: 100%; height: 400px;"></div>
	<script>
		$(function () {
			var mapCreated = false;
			$('.detail-menu a[href=#location]').parents('li')
				.on("tabShow", function () {
					if (!mapCreated)
					{
						var address = $('#detailMap').data('address');
						mapCreated = $().citrusRealtyAddress({
							id: 'detailMap',
							address: address,
							header: '<?=CUtil::JSEscape($arResult["NAME"])?>',
							body: address,
							footer: '',
							controls: ['mediumMapDefaultSet'],
							openBaloon: true,
							onError: function () {
								$('#detailMap').height('auto').html('<h2><?=GetMessage("CITRUS_REALTY_ADDRESS_NOT_FOUND")?></h2>');
							}
						})
					}
				})
				.on("tabHide", function () { console.log('hide'); } );
		});
	</script>
	<?
	$tabs["location"] = array(
		"title" => GetMessage("CITRUS_REALTY_TAB_LOCATION"),
		"html" => ob_get_contents(),
	);
	ob_end_clean();
}

if (!empty($images))
{
	ob_start();
	?><ul class="detail-images"><?
	foreach ($images as $id=>$image)
	{
		?><li class="detail-images-item"><a id="<?=$id?>" href="<?=$image['src']?>" title="<?=$image['title']?>" target="_blank" class="fancybox" rel="offer"><img src="<?=$image['preview']['src']?>" alt="<?=$image['title']?>" width="<?=$image['preview']['width']?>" height="<?=$image['preview']['height']?>"></a></li><?
	}
	?></ul><?
	$tabs["photos"] = array(
		"title" => GetMessage("CITRUS_REALTY_TAB_PHOTO"),
		"html" => ob_get_contents(),
	);
	ob_end_clean();
}

if (!empty($layouts))
{
	ob_start();
	?><ul class="detail-images detail-layouts"><?
	foreach ($layouts as $image)
	{
		?><li class="detail-images-item"><a href="<?=$image['src']?>" title="<?=$image['title']?>" target="_blank" class="layouts-list-image" rel="layouts" data-filename="<?=$image["filename"]?>"><img src="<?=$image['preview']['src']?>" alt="<?=$image['title']?>" width="<?=$image['preview']['width']?>" height="<?=$image['preview']['height']?>"></a></li><?
	}
	?></ul><?
	$tabs["layouts"] = array(
		"title" => GetMessage("CITRUS_REALTY_TAB_LAYOUTS"),
		"html" => ob_get_contents(),
	);
	ob_end_clean();
}

if (is_array($prop = $arResult["PROPERTIES"]["text_prices"]))
    if (is_array($prop["VALUE"])) {
        $value = $prop["VALUE"]["TYPE"] != 'text' ? $prop["~VALUE"]["TEXT"] : $prop["VALUE"]["TEXT"];
    } else {
        $value = $prop["VALUE"];
    }
	$tabs["prices"] = array(
		"title" => GetMessage("CITRUS_REALTY_TAB_PRICES"),
		"html" => $value,
	);

if (is_array($prop = $arResult["PROPERTIES"]["text_mortage"])) {
    if (is_array($prop["VALUE"])) {
        $value = $prop["VALUE"]["TYPE"] != 'text' ? $prop["~VALUE"]["TEXT"] : $prop["VALUE"]["TEXT"];
    } else {
        $value = $prop["VALUE"];
    }
    $tabs["mortage"] = array(
        "title" => GetMessage("CITRUS_REALTY_TAB_MORTAGE"),
        "html" => $value,
    );
}


// remove empty tabs
foreach($tabs as $idx=>$tab)
	if (!array_key_exists('html', $tab) || !strlen(trim($tab['html'])))
		unset($tabs[$idx]);
$selectedTab = array_shift(array_keys($tabs));

// nook переделать вкладки таким образом, чтобы без js вкладки отображались в видимых блоках заголовок->содержимое вкладки
?><div class="detail-menu"><ul><?
foreach ($tabs as $k=>$tab)
{
	?><li<?=($k==$selectedTab ? ' class="selected"' : '')?>><a href="#<?=$k?>"><?=$tab['title']?></a></li><?
}
?></ul></div><?

foreach ($tabs as $k=>$tab)
{
	?><div class="detail-text detail-text-<?=$k?><?=($k==$selectedTab? ' visible' : '')?>"><?=$tab['html']?></div><?
}
