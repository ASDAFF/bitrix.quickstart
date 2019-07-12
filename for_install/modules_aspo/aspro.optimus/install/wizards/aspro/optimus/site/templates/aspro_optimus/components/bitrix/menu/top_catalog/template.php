<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true ); ?>
<?if( !empty( $arResult ) ){
	global $TEMPLATE_OPTIONS;?>
	<ul class="menu top menu_top_block catalogfirst">
		<?foreach( $arResult as $key => $arItem ){?>
			<li class="catalog icons_fa <?=($arItem["CHILD"] ? "has-child" : "");?> <?=($arItem["SELECTED"] ? "current" : "");?>">
				<a class="<?=($arItem["CHILD"] ? "parent" : "");?>" href="<?=$arItem["LINK"]?>" ><?=$arItem["TEXT"]?></a>
				<?if($arItem["CHILD"]){?>
					<ul class="dropdown">
						<?foreach($arItem["CHILD"] as $arChildItem){?>
							<li class="full <?=($arChildItem["CHILD"] ? "has-child" : "");?> <?if($arChildItem["SELECTED"]){?> current <?}?> m_<?=strtolower($TEMPLATE_OPTIONS["MENU_POSITION"]["CURRENT_VALUE"]);?>">
								<a class="icons_fa <?=($arChildItem["CHILD"] ? "parent" : "");?>" href="<?=$arChildItem["SECTION_PAGE_URL"];?>"><?=$arChildItem["NAME"];?></a>
								<?if($arChildItem["CHILD"]){?>
									<ul class="dropdown">
										<?foreach($arChildItem["CHILD"] as $arChildItem1){?>
											<li class="menu_item <?if($arChildItem1["SELECTED"]){?> current <?}?>">
												<?if($arChildItem["IMAGES"]){?>
													<span class="image"><a href="<?=$arChildItem1["SECTION_PAGE_URL"];?>"><img src="<?=$arChildItem1["IMAGES"]["src"];?>" /></a></span>
												<?}?>
												<a class="section" href="<?=$arChildItem1["SECTION_PAGE_URL"];?>"><span><?=$arChildItem1["NAME"];?></span></a>
												<?if($arChildItem1["CHILD"]){?>
													<ul class="dropdown">
														<?foreach($arChildItem1["CHILD"] as $arChildItem2){?>
															<li class="menu_item <?if($arChildItem2["SELECTED"]){?> current <?}?>">
																<a class="section1" href="<?=$arChildItem2["SECTION_PAGE_URL"];?>"><span><?=$arChildItem2["NAME"];?></span></a>
															</li>
														<?}?>
													</ul>
												<?}?>
												<div class="clearfix"></div>
											</li>
										<?}?>
									</ul>
								<?}?>
							</li>
						<?}?>
					</ul>
				<?}?>
			</li>
		<?}?>
	</ul>
<?}?>