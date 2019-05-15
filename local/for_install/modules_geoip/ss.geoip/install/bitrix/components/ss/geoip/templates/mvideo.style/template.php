<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if($arParams["CONNECT_YMAP_API"] == "Y")
{
	$APPLICATION->AddHeadScript("http://api-maps.yandex.ru/2.1/?lang=ru_RU");
}

if($arParams["CONNECT_JQUERY"] == "Y")
{
	CJSCore::Init(array("jquery"));
}

$this->setFrameMode(true);
$schemeColor = "#".str_replace("#", "", $arParams["SCHEME_COLOR"]);

if(!empty($arResult["ITEMS"])):?>

	<style type="text/css">
		#sec-header-wrap #sec-header-regions{background-color: <?=$schemeColor?>;}
		#sec-header-wrap .sec-header-section .form-input #sec-autocomplete{background-color: <?=$schemeColor?>;}
	</style>

	<div id="sec-header-wrap">
		<div id="sec-header-regions">
			<div class="sec-header-section">
				<h2 class="sec-region-heading"><?=GetMessage('SS_GEOIP_ENTER_CITY');?></h2>
				<div class="sec-header-map-section">
					<div class="sec-header-region-map">
						<div id="sec-ymaps-city" style="width: 350px; height: 200px"></div>
					</div>
					<div class="sec-header-primary-cities">
						<ul>
							<?foreach($arResult["ITEMS"] as $key => $arItems)
							{
								if($key%5 == 0)
								{
									echo "</ul><ul>";
								}

								if($arItems["MAIN"] == "Y")
								{
									echo "<li class='sec-your-city'>";
								}

								else
								{
									echo "<li>";
								}

								echo "<a href='javascript:void(0);' class='sec-cities-link' onclick='SetCoordinate(".$arItems["LONGITUDE"].", ".$arItems["LATITUDE"].")' data-id='{$arItems["ID"]}'>
										<span>{$arItems["NAME"]}</span>
									</a>
								</li>";
							}
							?>
						</ul>
					</div>
					<h2 class="sec-region-heading"><?=GetMessage("SS_GEOIP_ENTER_INPUT_CITY");?></h2>
				</div>
				<div class="sec-popup-form">
					<form action="">
						<div class="form-input">
							<input type="text" placeholder="<?=GetMessage('SS_GEOIP_PLACEHOLDER')?>" />
							<div id="sec-autocomplete"></div>
						</div>
					</form>
				</div>
				<a id="sec-region-close"><?=GetMessage("SS_GEOIP_POPUP_CLOSE")?></a>
			</div>
		</div>
		<div class="sec-bottom-line"></div>
		<div id="sec-region-open">
			<?
			if(!empty($arResult['CITY_MAIN']))
			{
				echo $arResult['CITY_MAIN']["NAME"];
			}
			else
			{
				echo GetMessage('SS_GEOIP_ENTER_CITY');
			}
			?>
			<i></i>
		</div>

		<?if(!empty($arResult['CITY_MAIN']["NAME"]) && $arResult["CONFIRM_POPUP"] == "Y"):?>
			<div class="sec-city-popup">
				<h3 class="sec-city-popup-title">
					<button class="sec-city-popup-close">x</button>
				</h3>
				<div class="sec-city-popup-content">
					<p><?=GetMessage("SS_GEOIP_THIS_YOUR_CITY")?></p>
					<strong><?=$arResult['CITY_MAIN']["NAME"]?></strong>
					<a class="sec-city-popup-enter btn" href="javascript:void(0)"><?=GetMessage("SS_GEOIP_POPUP_BUTTON_YES")?></a>
					<a class="sec-city-popup-other-city btn" href="javascript:void(0)"><?=GetMessage("SS_GEOIP_POPUP_BUTTON_NO")?></a>
				</div>
			</div>

			<script type="text/javascript">
				$(document).ready(function(){
					setTimeout(function(){
						$('#sec-header-wrap .sec-city-popup').fadeIn(300);
					}, 3000);
				});
			</script>
		<?endif;?>
	</div>

	<script type="text/javascript">
		ymaps.ready(init);

		function init()
		{
			myMap = new ymaps.Map(
				'sec-ymaps-city',
				{
					center: [<?=$arResult["CITY_MAIN"]["LONGITUDE"]?>, <?=$arResult["CITY_MAIN"]["LATITUDE"]?>],
					zoom: 8,
					controls: []
				}
			);

			myGeoObject = new ymaps.Placemark([<?=$arResult["CITY_MAIN"]["LONGITUDE"]?>, <?=$arResult["CITY_MAIN"]["LATITUDE"]?>], {}, {
	            preset: 'islands#dotIcon',
	            iconColor: '#333'
	        });

			myMap.geoObjects.add(myGeoObject);
		};
	</script>
<?endif;?>