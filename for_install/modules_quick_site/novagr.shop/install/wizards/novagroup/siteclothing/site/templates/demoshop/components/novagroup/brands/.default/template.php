<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//$GLOBALS['APPLICATION']->AddHeadScript( SITE_TEMPLATE_PATH . '/js/history.js');
$GLOBALS['APPLICATION']->AddHeadString('<script src="' . SITE_TEMPLATE_PATH . '/js/scrollTo.js"></script>', true);



if ($arParams['AJAX'] != "Y") {
	//deb($arParams);
	
	?>
	<script type="text/javascript">

		function GetElementsBrands(nPageSize, iNumPage)
		{
			$('#elements-brands').html('<p align="center"><img src="<?=SITE_TEMPLATE_PATH;?>/images/loading.png" /></p>');
			if (nPageSize == "") nPageSize = "<?=$arParams['nPageSize']?>";
			if (iNumPage == "") iNumPage = "<?=$arParams['iNumPage']?>";
			
			var arFilter = {};
						
			$.ajax({
				type: "POST",
				url: "<?=$templateFolder;?>/ajax/getElements.php",
				data: {
					'URL'					: "<?=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];?>",
					'AJAX'					: "Y",
					'arFilter'				: arFilter,
					'BRANDS_IBLOCK_CODE'	: "<?=$arParams['BRANDS_IBLOCK_CODE'];?>",
					'CACHE_TYPE'			: "<?=$arParams['CACHE_TYPE'];?>",
					'CACHE_TIME'			: "<?=$arParams['CACHE_TIME'];?>",
					'nPageSize'				: nPageSize,
					'PAGEN_1'				: iNumPage,
					<?php
					$let = htmlspecialcharsbx($_REQUEST['let']);
					if (!empty($let)) {
						?>
					'let'				: '<?=$let?>',
						<?php 
					}
					?>
					'USER_FILTER'			: '<?=$arParams['USER_ID']?>'
				},
				dataType:"html",
				success: function(data){
					$('#elements-brands').html(data);					
				}
			});
		}
		$(document).ready(function(){
			// hack for chrome for wori of popstate
			var initialFire = document.location.href;
			
			/**
			* ajax-pagination
			*/

			$(".brands-nav a").live('click', function(){
					$.scrollTo("#canvas", 500);		
					var browserName = navigator.appName;
					if (browserName != "Microsoft Internet Explorer")
					{
						// if we can get the number of records per page of the attribute we do it
						if ($(this).attr('nPageSize')) {
							curPageSize = $(this).attr('nPageSize');
						}							
						GetElementsBrands(curPageSize, $(this).attr('data-inumpage'));

						initialFire = false;
						history.pushState( null, null, this.href );			
						return false;				
					}
			});
			
			
			
			$( window ).bind( "popstate", function( e ) {
				// hack for chrome for wori of popstate
				if ( initialFire === document.location.href ) {
					return initialFire = false;
				}
				initialFire = false;

                //var returnLocation = history.location || document.location;

             	// by clicking on the return we get the right brand
				var browserName = navigator.appName;

				if (browserName != "Microsoft Internet Explorer")
				{
					var inumpage = 1;
					// get numpage from url params
					var $_GET = parseGetParams(window);

					for (var i in $_GET) {

						var regexp = /PAGEN_(\d+)/gi;
						var match = regexp.exec(i);
						if (match != null) {
							inumpage = $_GET[i];
							break;
						}
					}

					$.scrollTo("#canvas", 500);		
					GetElementsBrands(false, inumpage);			
					return false;				
				}
            });			
		});	
	</script>	
	<div class="col3-list brand">
		<?php 		
}	


if ($arResult['ORDABC'] > 0 ){

	$fst = array_shift($arResult['BRANDS']);
	$memLET = mb_substr($fst['NAME'], 0, 1);
	$range = array(GetMessage("NOVAGR_SHOP_A"),GetMessage("NOVAGR_SHOP_B"),GetMessage("NOVAGR_SHOP_V"),GetMessage("NOVAGR_SHOP_G"),GetMessage("NOVAGR_SHOP_D"),GetMessage("NOVAGR_SHOP_E"),GetMessage("NOVAGR_SHOP_J"),GetMessage("NOVAGR_SHOP_Z"),GetMessage("NOVAGR_SHOP_I"),GetMessage("NOVAGR_SHOP_K"),GetMessage("NOVAGR_SHOP_L"),GetMessage("NOVAGR_SHOP_M"),GetMessage("NOVAGR_SHOP_N"),GetMessage("NOVAGR_SHOP_O"),GetMessage("NOVAGR_SHOP_P"),GetMessage("NOVAGR_SHOP_R"),GetMessage("NOVAGR_SHOP_S"),GetMessage("NOVAGR_SHOP_T"),GetMessage("NOVAGR_SHOP_U"),GetMessage("NOVAGR_SHOP_F"),GetMessage("NOVAGR_SHOP_H"),GetMessage("NOVAGR_SHOP_C"),GetMessage("NOVAGR_SHOP_C1"),GetMessage("NOVAGR_SHOP_S1"),GetMessage("NOVAGR_SHOP_S2"),GetMessage("NOVAGR_SHOP_E1"),GetMessage("NOVAGR_SHOP_U1"),GetMessage("NOVAGR_SHOP_A1"));
	?>
	<div id="buttons_all">
		<a href="" class="btnall"><?=GetMessage("B_BY_LIST")?></a>
		<a href="?abc=1" class="btnall select"><?=GetMessage("B_BY_ALFAVIT")?></a>
	</div>
	<hr>
	<div class="col3-list stuff-box alfavit">
		<div>
			<span><?=$memLET;?></span>
			<ul>
				<li>
				<?php
                $FilterURL = SITE_DIR."catalog/?iNumPage=1&nPageSize=". N_PAGE_SIZE_1 ."&arFilter[0][PROPERTY_VENDOR]=".$val['ID'];
				?><a href="<?=$FilterURL;?>"><?=$fst['NAME']?></a><?php
				?>
			</li>
	<?
		$ctr = 0; $rus = 0;
		foreach($arResult['BRANDS'] as $val)
		{
			$curLET = mb_substr($val['NAME'], 0, 1);
			if($memLET != $curLET)
			{
				$memLET = $curLET;
				$ctr++;
				?>
			</ul>
		</div>
		
			<?
				if( $rus == 0 && in_array($curLET, $range))
				{
					$rus = 1;
			?>
	</div>
	<div class="col3-list stuff-box alfavit otherlanguage" style="clear:both;">
			<?
				}
			?>
		
		<div<? if($ctr == 4){echo' style="clear:both;"'; $ctr = 0;}?>>
			<span><?=$memLET;?></span>
			<ul>
				<?
			}

            $FilterURL = SITE_DIR."catalog/?iNumPage=1&nPageSize=". N_PAGE_SIZE_1 ."&arFilter[0][PROPERTY_VENDOR]=".$val['ID'];

				?><li><a href="<?=$FilterURL;?>"><?=$val['NAME']?></a></li><?php
			
		}
			
	?>
			</ul>
		</div>
	</div>
<?
} elseif( empty($arResult['BRAND_CODE']) ) {
	
	if ($arParams['AJAX'] != "Y") {
	
			?>
			<div id="buttons_all"> 	
				<a href="" class="btnall select"><?=GetMessage("B_BY_LIST")?></a>
				<a href="?abc=1" class="btnall" ><?=GetMessage("B_BY_ALFAVIT")?></a>
			</div>
			<hr>
			<div id="alfavit">
				<ul><?
				foreach($arResult['LAT'] as $val)
					echo'<li><a href="./?let='.$val.'">'.$val.'</a></li>';
				?>
				</ul>
				<ul>
				<?
				foreach($arResult['RUS'] as $val)
					echo'<li><a href="./?let='.$val.'">'.$val.'</a></li>';
				?>
				</ul>
			</div>
			
			
			
			<?php 
		
		?>
		<div id="elements-brands" class="brand"  >
			<?
	}// close if 	if($arParams['AJAX'] != "Y") {
	
		foreach($arResult['BRANDS'] as $val)
		{
			
			if (!empty($val['CODE'])) $brandURL = SITE_DIR."brands/" . $val['CODE']."/";
			else $brandURL = '';
			
			$FilterURL = SITE_DIR."catalog/?iNumPage=1&nPageSize=". N_PAGE_SIZE_1 ."&arFilter[0][PROPERTY_VENDOR]=".$val['ID'];
			?>
			<div class="list py">
				<div class="itemsall clearfix brands-list">
					<div class="item itemsall_op">
			
						<div class="brand-lf">
							<div class="title">
							<?php
								echo $val['NAME'];
							?>							
							</div>						
							<div class="clear"></div>
							<a href="<?=$FilterURL;?>" class="btn"><?=GetMessage("B_PRODUCTS")?></a>
						</div>
						
						<div class="itemsall_img">
						<?php 
						if (!empty($brandURL)) {
							?><a href="<?=$brandURL;?>">
							<img height="auto" src="<?=$arResult['PREVIEW_PICTURE'][$val['PREVIEW_PICTURE']];?>" alt="" />
							</a>
							<?php 
						} else {
							?>
							<img height="auto" src="<?=$arResult['PREVIEW_PICTURE'][$val['PREVIEW_PICTURE']];?>" alt="" />
							<?php
						}
						?>
						</div>
						<div class="personal_notes"><?=$val['DETAIL_TEXT'];?></div>					
				</div>
			</div>			
			<hr>
			</div>	
		<?
		}
		
		?>
		<div class="brands-nav">
		
		<?=$arResult["NAV_STRING"];?>
		</div>
		<div class="clear"></div>
		<?php 
	if ($arParams['AJAX'] != "Y") {
			?>
			</div>
			
	<?php
	}
	
} else {
	foreach($arResult['BRANDS'] as $val) {
		$brandURL = SITE_DIR."brands/" . $val['CODE']."/";
		$FilterURL = SITE_DIR."catalog/?iNumPage=1&nPageSize=". N_PAGE_SIZE_1 ."&arFilter[0][PROPERTY_VENDOR]=".$val['ID'];
	?>
	<div class="list ol">
		<div class="itemsall clearfix brands-list">
			<div class="item itemsall_op">
				<div class="brand-lf">
				
					<div class="title">
						<a href="<?=$brandURL;?>"><?=$val['NAME'];?></a>
					</div>
					
					<div class="clear"></div>
					<a href="<?=$FilterURL?>" class="btn"><?=GetMessage("B_PRODUCTS")?></a>
					
				</div>
				
				<div class="itemsall_img">
				<a href="<?=$brandURL?>"><img width="140" alt="" src="<?=$arResult['PREVIEW_PICTURE'][$val['PREVIEW_PICTURE']];?>"></a>
				
				</div>
				
				<div class="clear"></div>
				<div class="personal_notes">
					<?=$val['DETAIL_TEXT'];?>
				</div>
				
						
			</div>
		</div>					
	
	</div>
	<p class="back-demo">&#8592; <a class="lsnn" href="<?=SITE_DIR?>brands/"><?=GetMessage("B_BACK_TO_LIST")?></a></p>

<?
	}
}
if($arParams['AJAX'] != "Y") {
	echo "</div>";	
}
?>