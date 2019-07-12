<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

$this->SetViewTarget('sidebarClass');
echo ' sidebar-print';
$this->SetViewTarget('sidebar');
require(__DIR__ . '/sidebar.php');
$this->EndViewTarget();


$userFields = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("IBLOCK_" . $arResult["IBLOCK_ID"] . "_SECTION", $arResult["IBLOCK_SECTION_ID"], LANGUAGE_ID);
$obEnum = new CUserFieldEnum();
if ($userFields["UF_TYPE"]["VALUE"] && ($enum = $obEnum->GetList(array(), array("ID" => $userFields["UF_TYPE"]["VALUE"]))->Fetch()))
	$ufType = $enum["XML_ID"];
else
	$ufType = false;

$component->setResultCacheKeys(array("IBLOCK_SECTION_ID"));

if ($ufType === 'cards')
{
	require(__DIR__ . "/template_cards.php");
	return;
}

// nook add image title from SEO module
$addImage = function($id, $title='') use (&$images, $arResult)
{
	$path = CFile::GetPath($id);
	if ($path)
	{
		$preview = CFile::ResizeImageGet($id, Array('width' => 296, 'height' => 221), BX_RESIZE_IMAGE_EXACT, $bInitSizes = true);
		$thumb = CFile::ResizeImageGet($id, Array('width' => 79, 'height' => 60), BX_RESIZE_IMAGE_EXACT, $bInitSizes = true);
		$images['img' . $id] = array(
			'src' => CFile::GetPath($id),
			'preview' => $preview,
			'thumb' => $thumb,
			'title' => strlen($title) ? $title : $arResult['NAME'],
		);
	}
};

$images = array();
if (is_array($arResult["DETAIL_PICTURE"]))
	$addImage($arResult["DETAIL_PICTURE"]["ID"], $arResult["NAME"]);
elseif (is_array($arResult["PREVIEW_PICTURE"]))
	$addImage($arResult["PREVIEW_PICTURE"]["ID"], $arResult["NAME"]);
if (is_array($arResult["PROPERTIES"]["photo"]['VALUE']))
	foreach ($arResult["PROPERTIES"]["photo"]['VALUE'] as $id)
		$addImage($id);

$detailText = strlen(trim(strip_tags($arResult["DETAIL_TEXT"]))) ? $arResult["DETAIL_TEXT"] : (strlen(trim(strip_tags($arResult["PREVIEW_TEXT"]))) ? $arResult["PREVIEW_TEXT"] : false);

?>
<div class="content-detail">
<div class="content-left">
	<?
	if (!empty($images))
	{
		?>
		<div id="slider-carousel">
			<div id="carousel-wrapper">
				<div id="carousel">
					<?
					foreach ($images as $id=>$image)
					{
						?><a id="<?=$id?>" href="<?=$image['src']?>" title="<?=$image['title']?>" target="_blank" class="fancybox" rel="offer"><img src="<?=$image['preview']['src']?>" alt="<?=$image['title']?>" width="<?=$image['preview']['width']?>" height="<?=$image['preview']['height']?>"></a><?
					}
					?>
				</div>
			</div>
			<?
			if (count($images) > 1)
			{
				?>
				<div id="thumbs-wrapper">
					<div id="thumbs">
						<?
						foreach ($images as $id => $image)
						{
							?><a href="#<?=$id?>"><img src="<?=$image['thumb']['src']?>" alt="<?=$image['title']?>" width="<?=$image['thumb']['width']?>" height="<?=$image['thumb']['height']?>"></a><?
						}
						?>
					</div>
					<a id="prev" href="#"></a>
					<a id="next" href="#"></a>
				</div>
				<?
			}
			?>
		</div>
		<?
	}

	if (isset($arResult["PROPERTIES"]['address']) && $arResult["PROPERTIES"]['address']["VALUE"])
	{
		?>
		<?$APPLICATION->IncludeComponent("citrus:realty.address", "", array(
			"INIT_MAP_TYPE" => "MAP",
			"NAME" => $arResult["NAME"],
			//"BODY" => "!",
			"ADDRESS" => $arResult["PROPERTIES"]['address']["VALUE"],
			"OPEN_BALOON" => "N",
			"MAP_WIDTH" => "297",
			"MAP_HEIGHT" => "252",
			"CONTROLS" => array(
				0 => "SMALLZOOM",
			),
			"OPTIONS" => array(
				0 => "ENABLE_DBLCLICK_ZOOM",
				1 => "ENABLE_DRAGGING",
			),
			"MAP_ID" => "map" . $arResult["ID"],
		),
		$component
	);?>
		<div class="content-map-scale print-hidden"><a href="javascript:void(0);" class="map-link"
										  data-address="<?= $arResult["PROPERTIES"]["address"]["VALUE"] ?>"><?=GetMessage("CITRUS_REALTY_ZOOM_MAP")?></a></div>
	<?
	}
	?>
</div>
<div class="content-right">
<?
$arProperties = array_diff_key($arResult["DISPLAY_PROPERTIES"], Array('photo' => 1, 'contact' => 1));
$skipProperties = array();
if (count($arProperties) > 0)
{
	?>
	<table>
	<tbody>
		<?
		foreach($arProperties as $pid=>$arProperty)
		{
			if (array_key_exists($pid, $skipProperties))
				continue;

			if ($arProperty["PROPERTY_TYPE"] == 'F')
			{
				if (!is_array($arProperty['VALUE'])) {
					$arProperty['VALUE'] = array($arProperty['VALUE']);
					$arProperty['DESCRIPTION'] = array($arProperty['DESCRIPTION']);
				}
				$arProperty["DISPLAY_VALUE"] = Array();
				foreach ($arProperty["VALUE"] as $idx=>$value) {
					$path = CFile::GetPath($value);
					$desc = strlen($arProperty["DESCRIPTION"][$idx]) > 0 ? $arProperty["DESCRIPTION"][$idx] : bx_basename($path);
					if (strlen($path) > 0)
					{
						$ext = pathinfo($path, PATHINFO_EXTENSION);
						$fileinfo = '';
						if ($arFile = CFile::GetByID($value)->Fetch())
							$fileinfo .= ' (' . $ext . ', ' . round($arFile['FILE_SIZE']/1024) . GetMessage('FILE_SIZE_Kb') . ')';
						$arProperty["DISPLAY_VALUE"][] = "<a href=\"{$path}\" class=\"file file-{$ext}\">" . $desc . "</a>" . $fileinfo;
					}
				}
				$val = is_array($arProperty["DISPLAY_VALUE"]) ? implode(', ', $arProperty["DISPLAY_VALUE"]) : $arProperty['DISPLAY_VALUE'];
			}
			else
			{
				$arProperty["DISPLAY_VALUE"] = strip_tags($arProperty["DISPLAY_VALUE"]);
				switch ($arProperty["CODE"])
				{
					case "cost":
						$arProperty["DISPLAY_VALUE"] = number_format($arProperty["VALUE"], 0, ',', ' ') . GetMessage("CITRUS_REALTY_CURRENCY");
						break;
					case "floor":
						if ($arResult["PROPERTIES"]["floors"]["VALUE"])
							$arProperty["DISPLAY_VALUE"] .= ' / ' . $arResult["PROPERTIES"]["floors"]["VALUE"];
						$skipProperties['floors'] = 1;
						break;
					case "address":
						$arProperty["DISPLAY_VALUE"] .= '<div class="on-map"><a href="javascript:void(0)" class="map-link" data-address="' . $arProperty["VALUE"] . '">' . GetMessage("CITRUS_REALTY_ON_MAP") . '</a></div>';
						break;
				}

				if (!is_array($arProperty["DISPLAY_VALUE"]))
					$arProperty["DISPLAY_VALUE"] = Array($arProperty["DISPLAY_VALUE"]);

				// добавим обознвчение единиц для площади
				if (stripos($pid, '_area') !== false)
					foreach ($arProperty["DISPLAY_VALUE"] as &$val)
						$val .= GetMessage("CITRUS_REALTY_SQR_METERS");

				$ar = '';
				foreach ($arProperty["DISPLAY_VALUE"] as $idx=>$value)
					$ar[] = $value . (strlen($arProperty["DESCRIPTION"][$idx]) > 0 ? ' (' . $arProperty["DESCRIPTION"][$idx] . ')': '');

				$val = implode('<br>', $ar);
			}

			?>
			<tr>
				<td class="td-title"><?=$arProperty["NAME"]?></td>
				<td><?=$val?></td>
			</tr>
			<?
		}
		?>
	</dl>
<?
}
?>
	</tbody>
	</table>
<?
if ($detailText)
{
	?>
	<div class="offer-detail-text">
		<?=$detailText?>
	</div>
	<?
}
?>
</div>
</div>