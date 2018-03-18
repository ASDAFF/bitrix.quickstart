<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if($arParams["CONNECT_JQUERY"] == "Y")
{
	CJSCore::Init(array("jquery"));
}

if(method_exists($this, 'setFrameMode'))
{
	$this->setFrameMode(true);
	$frame = $this->createFrame()->begin(GetMessage('SS_GEOIP_LOADING'));
}
$schemeColor = "#".str_replace("#", "", $arParams["SCHEME_COLOR"]);

if(!empty($arResult["ITEMS"])):
?>
	<style type="text/css">
		#sec-popup .sec-popup-top{background: <?=$schemeColor?>;}
		#sec-popup .sec-popup-bottom #sec-autocomplete{
			border-left: solid 1px <?=$schemeColor?>;
			border-bottom: solid 1px <?=$schemeColor?>;
			border-right: solid 1px <?=$schemeColor?>;
		}
		#sec-popup .form-input input:focus{border: 1px solid <?=$schemeColor?>;}
		#sec-popup .sec-popup-bottom #sec-autocomplete .sec-autocomplete-line strong{color: <?=$schemeColor?>}
	</style>

	<a href="javascript:void(0);" id="sec-popup-link">
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
	</a>

	<div id="sec-popup-bg"></div>
	<div id="sec-popup">
		<div class="sec-popup-top">
			<a href="javascript:void(0);" id="sec-popup-close"></a>
			<div class="sec-popup-header">
				<span class="sec-popup-title">
					<?
					if(!empty($arResult['CITY_MAIN']))
					{
						echo GetMessage("SS_GEOIP_THIS_YOUR_CITY", array("#CITY#" => $arResult['CITY_MAIN']["NAME"]));
					}
					else
					{
						echo GetMessage('SS_GEOIP_POINT_YOUR_CITY');
					}
					?>
				</span>
			</div>
		</div>
		<div class="sec-popup-middle">
			<div class="sec-popup-cities">
				<div class="sec-cities">
					<ul class="sec-cities-list">
					<?foreach($arResult["ITEMS"] as $key => $arItems)
					{
						if($key == 0)
						{
							echo "<li class='sec-cities-item sec-your-city'>
								<span class='sec-cities-text'>{$arItems["NAME"]}</span>
							</li>";
						}
						else
						{

							if($key%5 == 0)
							{
								echo "</ul><ul class='sec-cities-list'>";
							}

							echo "<li class='sec-cities-item'>
								<a href='javascript:void(0);' class='sec-cities-link' data-id='{$arItems["ID"]}'>
									<span class='sec-cities-text'>{$arItems["NAME"]}</span>
								</a>
							</li>";
						}
					}
					?>
					</ul>
				</div>
			</div>
		</div>
		<div class="sec-popup-bottom">
			<div class="sec-popup-form">
				<form action="">
					<div class="form-input">
						<input type="text" placeholder="<?=GetMessage('SS_GEOIP_PLACEHOLDER')?>" />
						<div id="sec-autocomplete"></div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<?if($arResult["CONFIRM_POPUP"] == "Y"):?>
	<script type="text/javascript">
		$(document).ready(function(){
			setTimeout(function(){
				$('#sec-popup, #sec-popup-bg').fadeIn(300);
			}, 3000);
		});
	</script>
	<?endif;?>
<?endif;?>

<?
if(method_exists($this, 'setFrameMode'))
{
	$frame->end();
}
?>