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
//$this->setFrameMode(true);
$this->setFrameMode(true);

if(empty($arResult["ITEMS"]))
	return;
	
CJSCore::Init(array("fx"));
$randID = $this->randString();
$strContID = 'jd_slider_'.$randID;
$itemsCount = count($arResult["ITEMS"]);
$arRowIDs = array();
$boolFirst = true;
$strContWidth = 100*$itemsCount;
$strItemWidth = 100/$itemsCount;
$interval = $arParams["INTERVAL"] * 1000;
?>
<div id="<? echo $strContID; ?>" class="bx_slider_section" >
    <div  class="bx_slider_container"  style="width:<? echo $strContWidth; ?>%;" id="bx_catalog_slider_cont_<?=$randID?>">
<?
	foreach ($arResult["ITEMS"] as $id => $arItem)
	{
		$strRowID = 'cat-top-'.$id.'_'.$randID;
		$arRowIDs[] = $strRowID;?>
		<div id="<? echo $strRowID; ?>" class="bx_slider_block<?echo ($boolFirst ? ' active' : ''); ?>" style="width:<? echo $strItemWidth; ?>%;">
			
		<?if(!empty($arItem['BTNAME']))
		{?>
			
		  <img src="<?echo htmlspecialcharsbx($arItem['PICT']['SRC']);?>"  title="<?echo htmlspecialcharsbx($arItem['HEAD']);?>" style="width:100%;" class="bxhtmled-surrogate">
		  
		
		<div class="container">
			<div class="carousel-caption">
				<?if(!empty($arItem['HEAD']))
				{?>
				  <h1><?echo htmlspecialcharsbx($arItem['HEAD']);?></h1>
			   <?}?>
				  
				<?if(!empty($arItem['DESC']))
				{?>
				  <p><? echo $arItem['DESC'];?></p>
			  <?}?>
			  <p>
				<a class="btn btn-lg btn-primary" href="<?echo htmlspecialcharsbx($arItem['LINK'])?>" role="button"><? echo $arItem['BTNAME'];?></a>
			  </p>
			</div>
		  </div>
		<?}
		  else
		  {?>
		  <a href="<?echo htmlspecialcharsbx($arItem['LINK'])?>">
			  <img src="<?echo htmlspecialcharsbx($arItem['PICT']['SRC']);?>" title="<?echo htmlspecialcharsbx($arItem['HEAD']);?>" style="width:100%;" class="bxhtmled-surrogate">
				<div class="carousel-caption">
			   <?if(!empty($arItem['HEAD']))
			{?>
			  <h1><?echo htmlspecialcharsbx($arItem['HEAD']);?></h1>
		   <?}?>
			  
			<?if(!empty($arItem['DESC']))
			{?>
			  <p><? echo $arItem['DESC'];?></p>
		  <?}?>
				   
				</div>
		 </a>
		<?}?>
		
		</div>
			
		<?$boolFirst = false;
	}?>
	</div>
</div>
<?
	if (1 < $itemsCount)
	{
		$arJSParams = array(
			'cont' => $strContID,
			'arrows' => array(
				'id' => $strContID.'_arrows',
				'class' => 'bx_slider_controls'
			),
			'left' => array(
				'id' => $strContID.'_left_arr',
				'class' => 'bx_slider_arrow_left'
			),
			'right' => array(
				'id' => $strContID.'_right_arr',
				'class' => 'bx_slider_arrow_right'
			),
			'pagination' => array(
				'id' => $strContID.'_pagination',
				'class' => 'bx_slider_pagination'
			),
			'items' => $arRowIDs,
			'rotate' => (0 < $interval),
			'rotateTimer' => $interval
		);
	?>
	<script type="text/javascript">
		var ob<? echo $strContID; ?> = new JDBannerList(<? echo CUtil::PhpToJSObject($arJSParams, false, true); ?>);
	</script>
	<?
	}

?>