<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
##############################################################
// zamenyaem ssylki v opisanii na softeffect.ru dlya DEMO dannyh
// udalit' esli DEMO dannye bolee ne ispol'zuyutsya
preg_match_all('|src=\"([^\"]*)\"|', $arResult['arSec']['DESCRIPTION'], $matches);
foreach ($matches[1] as $value) {
	if (!file_exists($_SERVER['DOCUMENT_ROOT'].$value)) {
		$arResult['arSec']['DESCRIPTION'] = str_replace($value, "http://softeffect.ru".$value, $arResult['arSec']['DESCRIPTION']);
	}
}
##############################################################

if (count($arResult['resActions'])>0) { ?>
	<h2><?=GetMessage('SE_CATALOGSECTION_ACTIONS')?> <?=$arResult['arSec']['NAME']?></h2>
	<div class="contentclose" id="relatedblock">
		<?
		$row=1;
		$i=1;
		foreach ($arResult['resActions'] as $key => $value) { ?>
			<? if ($i==1) { ?>
				<div class="clearfix" id="relatedrow1<?=$row?>">
			<? } // otkryvaem stroku ?>
				<div class="related clearfix<? if ($i==2) { ?> nextcol<? } ?>">
					<? if ($value['PICTURE_BREND']){?>
						<a title="<?=$value['NAME']?>" href="<?=$value['URL']?>">
							<img border="0"  alt="<?=$value['NAME']?>" title="<?=$value['NAME']?>" src="<?=$value['PICTURE_BREND']?>">
						</a>
					<? }  else {?>
						<a title="<?=$value['NAME']?>" href="<?=$value['URL']?>" ><?=$value['NAME']?></a>
					<? } ?>
				</div>
			<? if ($i==2) { $i=1; $row++; ?>
				</div><? } else { $i++; } // zakryvaem stroku
		} 
		if ($i==2) { // esli nechetnoe kol-vo - dobavlyaem kolonku i zakryvaem stroku
			echo '<div class="related clearfix nextcol"></div></div>';
		} ?>
	</div>
<? } ?>
<h1><?=$arResult['h1']?></h1>
<ul class="tabs" style="margin-left: 5px!important;">
	<? foreach ($arResult['RESULT'] as $key => $value) { ?>
		<li><a href="#<?=str_replace(' ', '_', $value['NAME'])?>"><?=$value['NAME']?></a></li>
	<? } ?>
	<li><a href="#<?=GetMessage('SE_CATALOGSECTION_ALL')?>"><?=GetMessage('SE_CATALOGSECTION_ALL')?></a></li>
	<? if (strlen($arResult['arSec']['DESCRIPTION'])>0) { ?><li><a href="#<?=GetMessage('SE_CATALOGSECTION_BRAND')?>"><?=GetMessage('SE_CATALOGSECTION_BRAND')?></a></li><? } ?>
</ul>
<div class="panes" style="border-right: 0; border-left: 0; border-bottom: 0;">
<? if (count($arResult['RESULT'])>0) { ?>
	<? foreach ($arResult['RESULT'] as $key => $value) { ?>
		<div style="background-image: url(<?=CFile::GetPath($arResult['arSec']['DETAIL_PICTURE']);?>);" class="contentclose" id="brandcatblock">
			<?
			$row=1;
			$i=1;
			ksort($value);
			foreach ($value as $cat=>$item) {
				if ($cat=='SORT' || $cat=='NAME') continue;
				if ($i==1) { ?><div class="firstrow clearfix" id="row<?=$row?>"><? } // otkryvaem stroku ?>
					<div class="brandcategory<? if ($i==2) { ?> nextcol<? } ?>">
						<?if($cat=='PROP'){$cat='';}?>
						<h3><?=$cat?></h3>
						<ul>
							<? foreach ($item as $key => $value) { ?>
								<li><a title="<?=$value['NAME']?>" href="<?=$value['URL']?>"><?=$value['NAME']?></a></li>
							<? } ?>
						</ul>
					</div>
				<? if ($i==2) { $i=1; $row++; ?></div><? } else { $i++; } // zakryvaem stroku
			}
			if ($i==2) { // esli nechetnoe kol-vo - dobavlyaem kolonku i zakryvaem stroku
				echo '<div class="brandcategory nextcol"></div></div>';
			}
			?>
		</div>
	<? } ?>
<? } ?>
<div style="background-image: url(<?=CFile::GetPath($arResult['arSec']['DETAIL_PICTURE']);?>);" class="contentclose" id="brandcatblock">
	<?
	$row=1;
	$i=1;
	ksort($arResult['RESULT_ALL']);
	foreach ($arResult['RESULT_ALL'] as $cat=>$item) {
		if ($i==1) { ?><div class="firstrow clearfix" id="row<?=$row?>"><? } // // otkryvaem stroku ?>
		<div class="brandcategory<? if ($i==2) { ?> nextcol<? } ?>">
			<h3><?=$cat?></h3>
			<ul>
				<? foreach ($item as $key => $value) { ?>
					<li><a title="<?=$value['NAME']?>" href="<?=$value['URL']?>"><?=$value['NAME']?></a></li>
				<? } ?>
			</ul>
		</div>
		<? if ($i==2) { $i=1; $row++; ?></div><? } else { $i++; } // zakryvaem stroku
	}
	if ($i==2) { // esli nechetnoe kol-vo - dobavlyaem kolonku i zakryvaem stroku
		echo '<div class="brandcategory nextcol"></div></div>';
	}
	?>
</div>
<? if (strlen($arResult['arSecL2']['DESCRIPTION'])>0) { ?>
	<div>
		<h2><?=GetMessage('SE_CATALOGSECTION_DESCR')?> <?=$arResult['arSec']['NAME'].' &laquo;'.$arResult['arSecL2']['NAME']?>&raquo;</h2><br />
		<?=htmlspecialchars_decode($arResult['arSecL2']['DESCRIPTION'])?>
	</div>
	<? } ?>
	<? if (strlen($arResult['arSec']['DESCRIPTION'])>0) { ?>
	<div>
		<h2><?=$arResult['arSec']['NAME']?></h2><br />
		<?=htmlspecialchars_decode($arResult['arSec']['DESCRIPTION'])?>
	</div>
	<? } ?>
</div>
<!-- brandcatblock -->
<? if (count($arResult['resTop'])>0) { ?>
	<h2><?=GetMessage('SE_CATALOGSECTION_BESTGOODS')?> <?=$arResult['arSec']['NAME']?></h2>
	<div class="contentclose" id="relatedblock">
		<?
		$row=1;
		$i=1;
		foreach ($arResult['resTop'] as $key => $value) { ?>
			<? if ($i==1) { ?><div class="clearfix" id="relatedrow1<?=$row?>"><? } // otkryvaem stroku ?>
				<div class="related clearfix<? if ($i==2) { ?> nextcol<? } ?>">
					<a title="<?=$value['NAME']?>" href="<?=$value['URL']?>" class="prdimagebox"><img width="50" alt="<?=$value['NAME']?>" src="<?=$value['PICTURE']?>"></a>
					<p class="item"><a title="<?=$value['NAME']?>" href="<?=$value['URL']?>"><?=$value['NAME']?>
					<? if ($value['TYPE_LIC']&$value['USERS_QUANTITY']) {echo (" (".$value['TYPE_LIC']." ".$value['USERS_QUANTITY'].") ");}
					    elseif ($value['TYPE_LIC']) {echo (" (".$value['TYPE_LIC'].") ");} ?></a></p>
					<p class="pricebox"><?=GetMessage('SE_CATALOGSECTION_PRICE')?>: <span class="pricenovat"><?=SaleFormatCurrency($value['PRICE'],'RUB')?></span></p>
				</div>
			<? if ($i==2) { $i=1; $row++; ?></div><? } else { $i++; } // zakryvaem stroku
		} 
		if ($i==2) { // esli nechetnoe kol-vo - dobavlyaem kolonku i zakryvaem stroku
			echo '<div class="related clearfix nextcol"></div></div>';
		}
		?>
	</div>
	<?
} ?>