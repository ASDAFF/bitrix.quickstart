<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? if (count($arResult['ACTIONS'])>0) { ?>
	<h2><?=GetMessage('SE_CATALOGSECTIONLIST_ACTIONS')?> <?=$_SESSION['arSec']['NAME']?></h2>
	<div class="contentclose" id="relatedblock">
		<?
		$row=1;
		$i=1;
		foreach ($arResult['ACTIONS'] as $key => $value) { ?>
			<? if ($i==1) { ?>
				<div class="clearfix" id="relatedrow1<?=$row?>"><? } ?>
					<div class="related clearfix<? if ($i==2) { ?> nextcol<? } ?>">
						<?if ($value['PICTURE_BREND']){?>
							<a title="<?=$value['NAME']?>" href="<?=$value['URL']?>" >
							<img border="0"  alt="<?=$value['NAME']?>" title="<?=$value['NAME']?>" src="<?=$value['PICTURE_BREND']?>">
							</a>
						<? }  else { ?>
							<a title="<?=$value['NAME']?>" href="<?=$value['URL']?>" ><?=$value['NAME']?></a>
						<? } ?>
					</div>
			<? if ($i==2) { $i=1; $row++; ?>
				</div><? } else { $i++; }
		} 
		if ($i==2) {
			echo '<div class="related clearfix nextcol"></div></div>';
		}
		?>
	</div>
<? } ?>

<h1 class='tit'><?=$arResult['META']['H1']?></h1>
<form enctype="multipart/form-data" method="POST" action="" name="ajaxform3" id="ajaxform3">
	<span id="catalog-ajax-box">
		<?$APPLICATION->IncludeFile($arResult['AJAX_PATH']['COMPONENT'], array('arSec'=>$_SESSION['arSec']['NAME'], 'arSecL2'=>$_SESSION['arSecL2']['NAME'], 'arResultComponent'=>$arResult), array("MODE" => "php"));?>
	</span>
</form>
<script type="text/javascript">
	$('#ajaxform3').submit(function() {
		$.ajax({
			url: "<?=$arResult['AJAX_PATH']['COMPONENT']?>",
			dataType: "html",
			type: "POST",
			data: $(this).serialize(),
			success: function(html) {
				$('#catalog-ajax-box').html(html);
			},
			error: function(response) {
				//console.log(response);
			}
		});

		return false;
	});
</script>