<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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

if (empty($arResult))
	return;

$bOpenCol = false;
?>
<?//print_R($arResult);?>
<?
$i=-1;
foreach($arResult as $elemMenu)
{
	if($elemMenu['DEPTH_LEVEL']==1)
	{
		$i++;
		$newmenu[$i]=$elemMenu;
	}
	else
	{
		$newmenu[$i]['SUB'][]=$elemMenu;
	}
}

?>
<?$c=count($newmenu); $C_rows=ceil(count($newmenu));?>

<?for($j=0;$j<$C_rows;$j++):?>
<div class="row">
	<?$i=0;?>
	<div class="col-md-3 col-sm-6">
		<?if($newmenu[$j*4+$i]['TEXT']):?>
		<h3><a href="<?=$newmenu[$j*4+$i]['LINK']?>"><?=$newmenu[$j*4+$i]['TEXT']?></a></h3>
		<?if(count($newmenu[$j*4+$i]['SUB'])>0)?>
		<menu>
			<?foreach($newmenu[$j*4+$i]['SUB'] as $elemMenu1):?>
			<li><a href="<?=$elemMenu1['LINK']?>"><?=$elemMenu1['TEXT']?></a></li>
			<?endforeach?>
		</menu>
		<?endif;?>
	</div>	
	
	<?$i=1;?>
	<div class="col-md-3 col-sm-6">
		<?if($newmenu[$j*4+$i]['TEXT']):?>
		<h3><a href="<?=$newmenu[$j*4+$i]['LINK']?>"><?=$newmenu[$j*4+$i]['TEXT']?></a></h3>
		<?if(count($newmenu[$j*4+$i]['SUB'])>0)?>
		<menu>
			<?foreach($newmenu[$j*4+$i]['SUB'] as $elemMenu1):?>
			<li><a href="<?=$elemMenu1['LINK']?>"><?=$elemMenu1['TEXT']?></a></li>
			<?endforeach?>
		</menu>
		<?endif;?>
	</div>	
	
	<?$i=2;?>
	<div class="col-md-3 col-sm-6">
		<?if($newmenu[$j*4+$i]['TEXT']):?>
		<h3><a href="<?=$newmenu[$j*4+$i]['LINK']?>"><?=$newmenu[$j*4+$i]['TEXT']?></a></h3>
		<?if(count($newmenu[$j*4+$i]['SUB'])>0)?>
		<menu>
			<?foreach($newmenu[$j*4+$i]['SUB'] as $elemMenu1):?>
			<li><a href="<?=$elemMenu1['LINK']?>"><?=$elemMenu1['TEXT']?></a></li>
			<?endforeach?>
		</menu>
		<?endif;?>
	</div>	
	
	<?$i=3;?>
	<div class="col-md-3 col-sm-6">
		<?if($newmenu[$j*4+$i]['TEXT']):?>
		<h3><a href="<?=$newmenu[$j*4+$i]['LINK']?>"><?=$newmenu[$j*4+$i]['TEXT']?></a></h3>
		<?if(count($newmenu[$j*4+$i]['SUB'])>0)?>
		<menu>
			<?foreach($newmenu[$j*4+$i]['SUB'] as $elemMenu1):?>
			<li><a href="<?=$elemMenu1['LINK']?>"><?=$elemMenu1['TEXT']?></a></li>
			<?endforeach?>
		</menu>
		<?endif;?>
	</div>

</div>
<?//$i++;?>
<?endfor?>
	
	