<?
use \Bitrix\Main\Page\Asset;
if ($arResult['ADD_JQUERY']=='Y'){
	
	Asset::getInstance()->addString('<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>');	
}

Asset::getInstance()->addString('<link rel="stylesheet" href="'.$templateFolder.'/dist/assets/owl.carousel.min.css">');
Asset::getInstance()->addString('<link rel="stylesheet" href="'.$templateFolder.'/dist/assets/owl.theme.default.min.css">');
echo ('<script src="'.$templateFolder.'/dist/owl.carousel.min.js"></script>');

$owl_class_start = 'cust-owl-'.$arResult['IBLOCK_ID'].'-'.$arResult['SECTION_CODE'];
?>
<div class="main-container-owl-module">
	<div class="owl-carousel owl-theme owl-loaded <?=$owl_class_start?>">
		<div class="owl-stage-outer">
			<div class="owl-stage">
				<?
				CModule::IncludeModule("iblock");
				$rsSections = CIBlockSection::GetList(array(),array('IBLOCK_ID' => $arResult['IBLOCK_ID'], '=CODE' => $arResult['SECTION_CODE']));
				if ($arSection = $rsSections->Fetch())
				{
					$parent_sec_id = $arSection['ID'];
				}
				if($arResult['SECTION_ID']>0){
					if(!empty($arParams['SECTION_ID']) || !empty($arParams['SECTION_CODE'])){
						$parent_sec_id = $arResult['SECTION_ID'];
					} else{
						$parent_sec_id = '';						
					}
				}
				if(empty($arParams['SECTION_ID']) && empty($arParams['SECTION_CODE'])){
					$parent_sec_id = '';
				}
				?>

				<?
				$arSelect = Array();
				$arFilter = Array("IBLOCK_ID"=>$arResult['IBLOCK_ID'], "INCLUDE_SUBSECTIONS" => "Y", "ACTIVE"=>"Y");
				if(!empty($parent_sec_id)){
					$arFilter['SECTION_ID'] = $parent_sec_id;
				}
				$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
				while($ob = $res->GetNextElement())
				{
					$arFields = $ob->GetFields();
					
					$db_props = CIBlockElement::GetProperty($arResult['IBLOCK_ID'], $arFields['ID'], "sort", "asc", array("ID" => $arParams['IBLOCK_LINK'])); 
					if($ar_props = $db_props->Fetch()){
						if ($ar_props['ID']==$arParams['IBLOCK_LINK']){
							$link = $ar_props['VALUE'];
						}
					}
					if(empty($link)){
						$link = "javascript:void(0);";
					}
					
					if($arParams['IBLOCK_LINK_URL_DEFAULT']=='Y'){
						$link = $arFields['DETAIL_PAGE_URL'];
					}


					$db_props = CIBlockElement::GetProperty($arResult['IBLOCK_ID'], $arFields['ID'], "sort", "asc", array("ID" => $arParams['IBLOCK_ACT'])); 

					if($ar_props = $db_props->Fetch()){
						$onslider = $ar_props['VALUE_XML_ID'];
						$onsliderval = $ar_props['VALUE'];
					}


					$date_to = strtotime($arFields['ACTIVE_TO']);
					$date = strtotime(date('d-m-Y'));
					if(empty($arFields['DETAIL_PICTURE'])){
						$arFields['DETAIL_PICTURE'] = $arFields['PREVIEW_PICTURE'];
					}
					?>
					<?if(!empty($arResult['IBLOCK_ID'])){?>
						<?if($onslider==$arParams['IBLOCK_ACTVAL'] || $onsliderval==$arParams['IBLOCK_ACTVAL'] || empty($arParams['IBLOCK_ACTVAL'])){?>
							<?if($date_to>$date || empty($arFields['ACTIVE_TO'])){?>
								<?if ($arParams['DONT_USE_BASE']!='Y'){?>
									<div class="owl-item" id="hash_<?=$arFields['ID']?>">
										<a href="<?echo $link;?>" target="_blank" ><img class="img-in-slide" src="<?echo CFile::GetPath($arFields['DETAIL_PICTURE']);?>" alt=""  /></a> 
									</div>
									<?}
								}
							}
						}
					}
					?> 
					<?
					if($arParams['DONT_USE_ADD']=='N'){
						foreach ($arResult['ADD_IBLOCK_ID'] As $addiblockid){

							$rsSections = CIBlockSection::GetList(array(),array('IBLOCK_ID' => $addiblockid));
							$arSelect = Array();
							$arFilter = Array("IBLOCK_ID"=>$addiblockid, "ACTIVE"=>"Y");
							$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
							while($ob = $res->GetNextElement())
							{
								$arFields = $ob->GetFields();
								
								$link = '';
								$db_props = CIBlockElement::GetProperty($addiblockid, $arFields['ID'], "sort", "asc", array("ID" => $arParams['ADD_IBLOCK_LINK'])); 
								if($ar_props = $db_props->Fetch()){									
									if ($ar_props['ID']==$arParams['ADD_IBLOCK_LINK']){
										$link = $ar_props['VALUE'];
									}
								}								
								if(empty($link)){
									$link = "javascript:void(0);";
								}

								if($arParams['ADD_IBLOCK_LINK_URL_DEFAULT']=='Y'){
									$link = $arFields['DETAIL_PAGE_URL'];
								}


								$db_props = CIBlockElement::GetProperty($addiblockid, $arFields['ID'], "sort", "asc", array("ID" => $arParams['ADD_IBLOCK_ACT'])); 

								if($ar_props = $db_props->Fetch()){

									if ($ar_props['CODE']==$arParams['ADD_IBLOCK_ACT']){

									}


									$onslider = $ar_props['VALUE_XML_ID'];
									$onsliderval = $ar_props['VALUE'];


								}
								if(empty($ar_props)){
									$onslider = '';
									$onsliderval = $onslider;
								}
								if(!empty($addiblockid)){
									if($onslider==$arParams['ADD_IBLOCK_ACTVAL'] || $onsliderval==$arParams['ADD_IBLOCK_ACTVAL'] || empty($arParams['ADD_IBLOCK_ACTVAL'])){

										if(empty($arFields['DETAIL_PICTURE'])){
											$arFields['DETAIL_PICTURE'] = $arFields['PREVIEW_PICTURE'];
										}
										$date_to = strtotime($arFields['ACTIVE_TO']);
										$date = strtotime(date('d-m-Y'));
										if(empty($date_to)){$date_to=$date+1;
										}
										?>
										<?if ($arParams['DONT_USE_ADD']!='Y'){?>
											<?if($date_to>$date || empty($arFields['ACTIVE_TO'])){?>
												<div class="owl-item" id="hash_<?=$arFields['ID']?>">
													<a href="<?echo $link;?>" target="_blank" ><img class="img-in-slide" src="<?echo CFile::GetPath($arFields['DETAIL_PICTURE']);?>" alt=""  /></a> 
												</div>
												<?}
											}
										}
									}
								}
							}
						}
						?>

					</div>
				</div>

			</div>

			<?
			function strtobool($txt){
				if($txt=='Y'){
					$res = 'true';
				}

				if($txt=='N'){
					$res = 'false';
				}
				return $res;
			}
			?>

			<?
			$EXPERT_MODE_navText = "['".$arParams['EXPERT_MODE_navText_left']."','".$arParams['EXPERT_MODE_navText_right']."']";

			?>




			<script>


				var owl = $('.<?=$owl_class_start?>');
				owl.owlCarousel({
					<?if($arParams['EXPERT_MODE_responsive']=='Y'){?>

						responsiveClass:true,
						responsive: {
							0: {
								items: <?=$arParams['EXPERT_MODE_responsivemin']?>
							},
							600: {
								items: <?=$arParams['EXPERT_MODE_responsivemed']?>
							},
							1000: {
								items: <?=$arParams['EXPERT_MODE_responsivebig']?>
							}
						},
						<?} else {?>
							items:<?=$arParams['EXPERT_MODE_items']?>,
							<?}?>

							nav:<?=strtobool($arParams['EXPERT_MODE_nav'])?>,
							navText:<?=$EXPERT_MODE_navText?>,									

							<?if($arParams['EXPERT_MODE_ON']=="Y"){?>
								<?if(empty($arParams['EXPERT_MODE_margin'])){
									$arParams['EXPERT_MODE_margin'] = '20';
								}?>
								margin:<?=$arParams['EXPERT_MODE_margin']?>,
								loop:<?=strtobool($arParams['EXPERT_MODE_loop'])?>,
								center:<?=strtobool($arParams['EXPERT_MODE_center'])?>,		  
								mouseDrag:<?=strtobool($arParams['EXPERT_MODE_mouseDrag'])?>,
								touchDrag:<?=strtobool($arParams['EXPERT_MODE_touchDrag'])?>,
								pullDrag:<?=strtobool($arParams['EXPERT_MODE_pullDrag'])?>,
								freeDrag:<?=strtobool($arParams['EXPERT_MODE_freeDrag'])?>,
								stagePadding:<?=$arParams['EXPERT_MODE_stagePadding']?>,
								autoWidth:<?=strtobool($arParams['EXPERT_MODE_autoWidth'])?>,
								autoHeight:<?=strtobool($arParams['EXPERT_MODE_autoHeight'])?>,
								startPosition:<?=$arParams['EXPERT_MODE_startPosition']?>,
								URLhashListener:<?=strtobool($arParams['EXPERT_MODE_URLhashListener'])?>,
								slideTransition:<?=$arParams['EXPERT_MODE_slideTransition']?>,
								dots:<?=strtobool($arParams['EXPERT_MODE_dots'])?>,		  
								dotsEach:<?=strtobool($arParams['EXPERT_MODE_dotsEach'])?>,
								dotsData: <?=strtobool($arParams['EXPERT_MODE_dotsData'])?>,
								lazyLoad: <?=strtobool($arParams['EXPERT_MODE_lazyLoad'])?>,
								autoplay: <?=strtobool($arParams['EXPERT_MODE_autoplay'])?>,
								autoplayTimeout: <?=$arParams['EXPERT_MODE_autoplayTimeout']?>,
								autoplayHoverPause: <?=strtobool($arParams['EXPERT_MODE_autoplayHoverPause'])?>,
								smartSpeed: <?=$arParams['EXPERT_MODE_smartSpeed']?>, 
								fluidSpeed: <?=$arParams['EXPERT_MODE_fluidSpeed']?>,
								autoplaySpeed: <?=$arParams['EXPERT_MODE_autoplaySpeed']?>,
								navSpeed: <?=strtobool($arParams['EXPERT_MODE_navSpeed'])?>,
								dotsSpeed: <?=strtobool($arParams['EXPERT_MODE_dotsSpeed'])?>,
								navContainer: <?=strtobool($arParams['EXPERT_MODE_navContainer'])?>,
								dotsContainer: <?=strtobool($arParams['EXPERT_MODE_dotsContainer'])?>
								<?}?>
							});


						</script>

					</div>

					<style>
						.img-in-slide{
							<?if(!empty($arParams['MAX_HEIGHT_SLIDE'])){?>
								/*max-height:<?=$arParams['MAX_HEIGHT_SLIDE']?>px;	*/							
								<?}?>

								<?if(!empty($arParams['MAX_WIDTH_SLIDE'])){?>
									max-width:<?=$arParams['MAX_WIDTH_SLIDE']?>px;
									<?}?>
								}
								<?if(!empty($arParams['MAX_HEIGHT_SLIDE'])){?>
									.owl-item{
										height: 100vh;
										max-height:<?=$arParams['MAX_HEIGHT_SLIDE']?>px;
										position: relative;
									}
									.owl-item a{
										display: block;
										position: absolute;
										top: 50%;
										left: 50%;
										transform: translate(-50%, -50%);										
										width: 100%;
										height: 100%;
										overflow: hidden;
									}
									.owl-item a img{
										position: absolute;
										top: 50%;
										left: 50%;
										transform: translate(-50%, -50%);										
										width: 100%;
										height: auto;
									}
									<?}?>
								</style>