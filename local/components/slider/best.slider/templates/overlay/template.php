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
										<?
										$file_arr = CFile::GetFileArray($arFields['DETAIL_PICTURE']);
										?>
										<a href="<?echo $link;?>" target="_blank" style="display: block; background-image: url(<?echo CFile::GetPath($arFields['DETAIL_PICTURE']);?>); 
										<?if(!empty($arParams['MAX_HEIGHT_SLIDE'])){?>											<?if($file_arr['HEIGHT']>=$file_arr['WIDTH']){?>
											background-size: contain;
											<?} else {
												?>
												background-size: cover;
												<?
											}} else {?>
												
												<?if($file_arr['HEIGHT']>=$file_arr['WIDTH']){?>
													max-height: <?=$file_arr['HEIGHT']?>px;
													height: <?=$file_arr['HEIGHT']?>px;
													<?} else{?>
														padding-bottom: <? echo ($file_arr['HEIGHT']/$file_arr['WIDTH'])*100?>%;
														<?}}?>
														" >
														<style>
															@media (max-width: <?=$file_arr['WIDTH']?>px) {
																#hash_<?=$arFields['ID']?> a{
																	-webkit-background-size: cover!important;
																	background-size: cover!important;
																	min-height: <?=$file_arr['HEIGHT']?>px;
																}
															}
														</style>
														<div class="container-fluid-0 cover-bg-owl">
															<div class="container-bs4" style="height: 100%">
																<div class="row-bs4" style="height: 100%">
																	<div class="col-xl-5-bs4 col-lg-7-bs4 col-md-9-bs4 col-12-bs4 black-cover-block">
																		<div class="block-text">
																			<div class="slider-title">
																				<?=$arFields['NAME']?>
																			</div>
																			<div class="slider-preview-text">
																				<?=$arFields['PREVIEW_TEXT']?>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>

													</a> 
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
																<?
																$file_arr = CFile::GetFileArray($arFields['DETAIL_PICTURE']);
																?>
																<a href="<?echo $link;?>" target="_blank" style="display: block; background-image: url(<?echo CFile::GetPath($arFields['DETAIL_PICTURE']);?>); 

																<?if(!empty($arParams['MAX_HEIGHT_SLIDE'])){?>
																	<?if($file_arr['HEIGHT']>=$file_arr['WIDTH']){?>
																		background-size: contain;
																		<?} else {
																			?>
																			background-size: cover;
																			<?
																		}} else {?>

																			<?if($file_arr['HEIGHT']>=$file_arr['WIDTH']){?>
																				max-height: <?=$file_arr['HEIGHT']?>px;
																				height: <?=$file_arr['HEIGHT']?>px;
																				<?} else{?>
																					padding-bottom: <? echo ($file_arr['HEIGHT']/$file_arr['WIDTH'])*100?>%;
																					<?}}?>

																					" >
																					<style>
																						@media (max-width: <?=$file_arr['WIDTH']?>px) {
																							#hash_<?=$arFields['ID']?> a{
																								-webkit-background-size: cover!important;
																								background-size: cover!important;
																								min-height: <?=$file_arr['HEIGHT']?>px;
																							}
																						}
																					</style>
																					<div class="container-fluid-0 cover-bg-owl">
																						<div class="container-bs4" style="height: 100%">
																							<div class="row-bs4" style="height: 100%">
																								<div class="col-xl-5-bs4 col-lg-7-bs4 col-md-9-bs4 col-12-bs4 black-cover-block">
																									<div class="block-text">
																										<div class="slider-title">
																											<?=$arFields['NAME']?>
																										</div>
																										<div class="slider-preview-text">
																											<?=$arFields['PREVIEW_TEXT']?>
																										</div>
																									</div>
																								</div>
																							</div>
																						</div>
																					</div>

																				</a>  
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



										<div class="owl-nav-cust"></div>
										<script>


											var owl = $('.<?=$owl_class_start?>');
											owl.owlCarousel({
												navContainer: '.owl-nav-cust',
												<?if($arParams['EXPERT_MODE_responsive']=='Y'){?>

													responsiveClass:true,
													responsive: {
														0: {
															items: 1
														},
														600: {
															items: 1
														},
														1000: {
															items: 1
														}
													},
													<?} else {?>
														items:1,
														<?}?>

														nav:<?=strtobool($arParams['EXPERT_MODE_nav'])?>,
														navText:<?=$EXPERT_MODE_navText?>,									

														<?if($arParams['EXPERT_MODE_ON']=="Y"){?>

															margin:<?=$arParams['EXPERT_MODE_margin']?>,
															loop:<?=strtobool($arParams['EXPERT_MODE_loop'])?>,
															center:<?=strtobool($arParams['EXPERT_MODE_center'])?>,		  
															mouseDrag:<?=strtobool($arParams['EXPERT_MODE_mouseDrag'])?>,
															touchDrag:<?=strtobool($arParams['EXPERT_MODE_touchDrag'])?>,
															pullDrag:<?=strtobool($arParams['EXPERT_MODE_pullDrag'])?>,
															freeDrag:<?=strtobool($arParams['EXPERT_MODE_freeDrag'])?>,
															stagePadding:<?=$arParams['EXPERT_MODE_stagePadding']?>,
															/*autoWidth:<?=strtobool($arParams['EXPERT_MODE_autoWidth'])?>,*/
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

															dotsContainer: <?=strtobool($arParams['EXPERT_MODE_dotsContainer'])?>
															<?}?>
														});


													</script>

												</div>

												<style>
													.owl-item a{
														text-decoration: none;
														/*min-height: 300px;*/
														background-size: contain; 
														background-position: center; 
														background-repeat: no-repeat;
													}
													<?if(!empty($arParams['MAX_HEIGHT_SLIDE'])){?>
														.owl-item a{
															padding-bottom: <?=$arParams['MAX_HEIGHT_SLIDE']?>px!important;
														}
														<?} else {
															?>
															.owl-item a{
																/*padding-bottom: 18.75%;*/
															}
															<?
														}?>

														.img-in-slide{
															<?if(!empty($arParams['MAX_HEIGHT_SLIDE'])){?>
																max-height:<?=$arParams['MAX_HEIGHT_SLIDE']?>px;
																<?}?>

																<?if(!empty($arParams['MAX_WIDTH_SLIDE'])){?>
																	max-width:<?=$arParams['MAX_WIDTH_SLIDE']?>px;
																	<?}?>
																}
																.main-container-owl-module{
																	position: relative;
																}
																.owl-dots{
																	position: absolute;
																	bottom: 15px; 
																	left: 50%;
																	transform: translate(-50%, 0%);
																	width: fit-content;
																}
																.owl-theme .owl-dots .owl-dot span{
																	width: 15px;
																	height: 15px;
																	background-color: #fff;
																	border: solid 1px #fff;
																	transition: .3s;
																	max-width: 1vw;
																	max-height: 1vw;
																}
																.owl-theme .owl-dots .owl-dot.active span, .owl-theme .owl-dots .owl-dot:hover span{
																	background-color: #ef7f1a;
																	border: solid 1px #fff;
																}
																.owl-item{
																	position: relative;
																}
																.cover-bg-owl{										
																	<?
																	if(!empty($arParams['MAX_HEIGHT_SLIDE']) && $arParams['MAX_HEIGHT_SLIDE']>0){	
																		?>

																		<?}?>
																		top: 0;
																		left: 0;
																		width: 100%;

																		position: absolute;
																		height: 100%;

																	}
																	.black-cover-block{
																		background-color: #00000080;
																		display: -webkit-flex;
																		display: -moz-flex;
																		display: -ms-flex;
																		display: -o-flex;
																		display: flex;
																		padding: 30px;
																		justify-content: center;
																	}
																	.slider-title{
																		font-size: <?=$arParams['FONT_SIZE_TITLE']?>;
																		color: #fff;
																		margin-bottom: 15px;
																		text-transform: uppercase;
																		line-height: 1;
																		font-family: 'Arial';
																		word-break: break-all;
																		text-decoration: none;
																	}
																	.slider-preview-text{
																		font-size: <?=$arParams['FONT_SIZE_DESC']?>;
																		color: #fff;
																		font-family: 'Arial';
																	}
																	@media (max-width: 767px) {
																		.slider-preview-text{
																			font-size: <?=$arParams['FONT_SIZE_DESC_MOB']?>;
																		}
																		.slider-title{
																			line-height: 1;
																			font-size: <?=$arParams['FONT_SIZE_TITLE_MOB']?>;
																		}
																	}
																	.block-text{
																		display: flex;
																		flex-direction: column;
																		justify-content: center;
																		word-wrap: break-word;
																		width: 100%;
																	}
																	.owl-nav-cust {										
																		position: absolute;
																		top: 0;
																		left: 0;
																		width: 100%;
																		height: 100%;
																		margin-top: 0;
																	}

																	.owl-nav-cust button{
																		position: absolute;
																		top: 0;
																		height: 100%;
																		background-color: #fff!important;
																		opacity: 0.7;
																		z-index: 3;
																		padding-left: 15px!important;
																		padding-right: 15px!important;
																		display: -webkit-flex!important;
																		display: -moz-flex!important;
																		display: -ms-flex!important;
																		display: -o-flex!important;
																		display: flex!important;
																		align-items: center;
																		margin: 0;
																		z-index: 1;
																		border: none;
																		cursor: pointer;
																		font-size: 22px;
																		color: #333;
																	}
																	.owl-nav-cust .owl-prev{
																		left: 0;
																	}
																	.owl-nav-cust .owl-next{
																		right: 0;
																	}


																	.container-bs4 {
																		width: 100%;
																		padding-right: 15px;
																		padding-left: 15px;
																		margin-right: auto;
																		margin-left: auto;
																	}

																	@media (min-width: 576px) {
																		.container-bs4 {
																			max-width: 540px;
																		}
																	}

																	@media (min-width: 768px) {
																		.container-bs4 {
																			max-width: 720px;
																		}
																	}

																	@media (min-width: 992px) {
																		.container-bs4 {
																			max-width: 960px;
																		}
																	}

																	@media (min-width: 1200px) {
																		.container-bs4 {
																			max-width: 1140px;
																		}
																	}
																	.row-bs4 {
																		display: -ms-flexbox;
																		display: flex;
																		-ms-flex-wrap: wrap;
																		flex-wrap: wrap;
																		margin-right: -15px;
																		margin-left: -15px;
																	}

																	.col-1-bs4, .col-2-bs4, .col-3-bs4, .col-4-bs4, .col-5-bs4, .col-6-bs4, .col-7-bs4, .col-8-bs4, .col-9-bs4, .col-10-bs4, .col-11-bs4, .col-12-bs4, .col-bs4,
																	.col-auto-bs4, .col-sm-1-bs4, .col-sm-2-bs4, .col-sm-3-bs4, .col-sm-4-bs4, .col-sm-5-bs4, .col-sm-6-bs4, .col-sm-7-bs4, .col-sm-8-bs4, .col-sm-9-bs4, .col-sm-10-bs4, .col-sm-11-bs4, .col-sm-12-bs4, .col-sm-bs4,
																	.col-sm-auto-bs4, .col-md-1-bs4, .col-md-2-bs4, .col-md-3-bs4, .col-md-4-bs4, .col-md-5-bs4, .col-md-6-bs4, .col-md-7-bs4, .col-md-8-bs4, .col-md-9-bs4, .col-md-10-bs4, .col-md-11-bs4, .col-md-12-bs4, .col-md-bs4,
																	.col-md-auto-bs4, .col-lg-1-bs4, .col-lg-2-bs4, .col-lg-3-bs4, .col-lg-4-bs4, .col-lg-5-bs4, .col-lg-6-bs4, .col-lg-7-bs4, .col-lg-8-bs4, .col-lg-9-bs4, .col-lg-10-bs4, .col-lg-11-bs4, .col-lg-12-bs4, .col-lg-bs4,
																	.col-lg-auto-bs4, .col-xl-1-bs4, .col-xl-2-bs4, .col-xl-3-bs4, .col-xl-4-bs4, .col-xl-5-bs4, .col-xl-6-bs4, .col-xl-7-bs4, .col-xl-8-bs4, .col-xl-9-bs4, .col-xl-10-bs4, .col-xl-11-bs4, .col-xl-12-bs4, .col-xl-bs4,
																	.col-xl-auto {
																		position: relative;
																		width: 100%;
																		min-height: 1px;
																		padding-right: 15px;
																		padding-left: 15px;										
																	}
																	.col-12-bs4 {
																		-ms-flex: 0 0 100%;
																		flex: 0 0 100%;
																		max-width: 100%;
																	}
																	/*col-xl-5-bs4 */
																	@media (min-width: 768px) {
																		.col-md-9-bs4{
																			-ms-flex: 0 0 75%;
																			flex: 0 0 75%;
																			max-width: 75%;
																		}
																	}
																	@media (min-width: 992px) {
																		.col-lg-7-bs4 {
																			-ms-flex: 0 0 58.333333%;
																			flex: 0 0 58.333333%;
																			max-width: 58.333333%;
																		}
																	}
																	@media (min-width: 1200px) {
																		.col-xl-5-bs4 {
																			-ms-flex: 0 0 41.666667%;
																			flex: 0 0 41.666667%;
																			max-width: 41.666667%;
																		}
																	}
																</style>
																<?if($arParams['EXPERT_MODE_nav']=='Y'){
																	?>
																	<style>
																		.col-sm-12-bs4{
																			padding-left: 40px;
																			padding-right: 40px;
																		}
																	</style>
																	<?

																}?>
																<script>
																	var bw = $('.owl-nav-cust button').outerWidth();
																	$('.black-cover-block').css('padding-left', bw+5);
																	$('.black-cover-block').css('padding-right', bw+5);
																</script>