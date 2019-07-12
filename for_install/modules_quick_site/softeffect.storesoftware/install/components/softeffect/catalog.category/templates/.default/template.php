<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<h1><?=$arResult['H1']?></h1>
<ul class="tabs" style="margin-left: 5px!important;">
	<? foreach (array_keys($arResult['TABS']) as $key => $value) { ?>
		<li><a href="#<?=str_replace(' ', '_', $value)?>"><?=$value?></a></li>
	<? } ?>
</ul>
<div class="panes" style="border-right: 0; border-left: 0; border-bottom: 0;">
	<? if (count($arResult['TABS'])>0) { ?>
		<? foreach ($arResult['TABS'] as $key => $value) { ?>
			<div class="contentclose" id="brandcatblock">
				<?
				$row=1;
				$i=1;
				ksort($value);
				foreach ($value as $cat=>$item) {
					sort($item);
					if ($i==1) { ?><div class="firstrow clearfix" id="row<?=$row?>"><? } ?>
						<div class="brandcategory<? if ($i==2) { ?> nextcol<? } ?>">
							<?if($cat=='PROP'){$cat='';}?>
							<h3><?=$cat?></h3>
							<ul>
								<? foreach ($item as $key => $value) { ?>
									<li><a title="<?=$value['NAME']?>" href="<?=$value['URL']?>"><?=$value['NAME']?></a></li>
								<? } ?>
							</ul>
						</div>
					<? if ($i==2) { $i=1; $row++; ?></div><? } else { $i++; }
				}
				if ($i==2) {
					echo '<div class="brandcategory nextcol"></div></div>';
				}
				?>
			</div>
		<? } ?>
	<? } ?>
</div>