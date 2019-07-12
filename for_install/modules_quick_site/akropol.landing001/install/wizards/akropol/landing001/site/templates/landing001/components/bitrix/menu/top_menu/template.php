<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>

<?if (!empty($arResult)):?>
	<div id="navbar" class="navbar-collapse collapse">
		<div class="navbar-right">
			<ul class="nav navbar-nav">	  
				<!-- MAIN MENU POSITIONS -->
				<?
				foreach($arResult as $arItem):
					if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
						continue;
				?>

						<li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
					
				<?endforeach?>
			</ul>
		</div>
	</div>
<?endif?>