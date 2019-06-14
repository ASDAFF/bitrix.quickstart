<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

?><div id="rsmonopoly_wg"><?
	?><div class="shesterenka"><div class="descr"><?
		?><span class="settings_show"><?=GetMessage('RS.MONOPOLY.SHESTERENKA_DESCR_SHOW')?></span><?
		?><span class="settings_hide"><?=GetMessage('RS.MONOPOLY.SHESTERENKA_DESCR_HIDE')?></span><?
	?></div></div><?
	?><div class="settings clearfix"><?
		?><div class="in"><?
			?><div class="title"><?=GetMessage('RS.MONOPOLY.SHESTERENKA_DESCR_SHOW')?></div><?
			?><div class="blocks clearfix"><?
				?><div class="col left"><?
					?><div class="option menu_type"><?
						?><div class="name"><?
							?><?=GetMessage('RS.MONOPOLY.HEAD_TYPE')?><?
							/*?><div class="help">i<div class="text"><span>Attention</span>Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet. </div></div><?*/
						?></div><?
						?><div class="body"><?
							?><button type="button" data-val="type1" class="type1<?if($arResult['SETTINGS']['headType']=='type1'):?> checked<?endif;?>"><span></span></button><?
							?><button type="button" data-val="type2" class="type2<?if($arResult['SETTINGS']['headType']=='type2'):?> checked<?endif;?>"><span></span></button><?
							?><button type="button" data-val="type3" class="type3<?if($arResult['SETTINGS']['headType']=='type3'):?> checked<?endif;?>"><span></span></button><?
							/*?><button type="button" class="type4<?if($arResult['SETTINGS']['headType']=='type4'):?> checked<?endif;?>"><span></span></button><?*/
						?></div><?
					?></div><?
					?><div class="option menu_style radioblock"><?
						?><div class="name"><?=GetMessage('RS.MONOPOLY.HEAD_STYLE')?></div><?
						?><div class="body"><?
							?><button type="button" data-val="style1" class="style1<?if($arResult['SETTINGS']['headStyle']=='style1'):?> checked<?endif;?>"><span></span><br /><?=GetMessage('RS.MONOPOLY.HEAD_STYLE_1')?></button><?
							?><button type="button" data-val="style2" class="style2<?if($arResult['SETTINGS']['headStyle']=='style2'):?> checked<?endif;?>"><span></span><br /><?=GetMessage('RS.MONOPOLY.HEAD_STYLE_2')?></button><?
							?><button type="button" data-val="style3" class="style3<?if($arResult['SETTINGS']['headStyle']=='style3'):?> checked<?endif;?>"><span></span><br /><?=GetMessage('RS.MONOPOLY.HEAD_STYLE_3')?></button><?
							?><div class="overlay" style="display:<?if($arResult['SETTINGS']['headType']=='type3'):?>block<?else:?>none<?endif;?>;"></div><?
						?></div><?
					?></div><?
					?><div class="option main_color"><?
						?><!--div class="name"><?=GetMessage('RS.MONOPOLY.MAIN_COLOR')?></div--><?
						?><div class="body"><?
							/**/?><div class="rsmonopoly_tabs"><?
								?><div class="rsmonopoly_content"><?
									?><div class="rsmonopoly_tab show" id="rsmw_tab1"><?/**/
										?><div class="rsmonopoly_cp rsmonopoly_colorBlock1"><?
											?><div id="colorpickerHolder1" class="colorpickerHolder" data-dcolor="<?=$arResult['SETTINGS']['GEN_COLOR']['HEX']?>"></div><?
											?><div class="colors"><?
												?><table><?
													?><tbody><?
														?><tr class="field r"><?
															?><td class="name">R</td><td class="val"><input type="text" value="<?=$arResult['SETTINGS']['GEN_COLOR']['RGB']['R']?>" /></td><?
														?></tr><?
														?><tr class="field g"><?
															?><td class="name">G</td><td class="val"><input type="text" value="<?=$arResult['SETTINGS']['GEN_COLOR']['RGB']['G']?>" /></td><?
														?></tr><?
														?><tr class="field b"><?
															?><td class="name">B</td><td class="val"><input type="text" value="<?=$arResult['SETTINGS']['GEN_COLOR']['RGB']['B']?>" /></td><?
														?></tr><?
														?><tr class="field hex"><?
															?><td class="name">#</td><td class="val"><input type="text" value="<?=$arResult['SETTINGS']['GEN_COLOR']['HEX']?>" /></td><?
														?></tr><?
													?></tbody><?
												?></table><?
											?></div><?
										?></div><?
									/**/?></div><?
									?><div class="rsmonopoly_tab" id="rsmw_tab2"><?
										?><div class="rsmonopoly_cp rsmonopoly_colorBlock2"><?
											?><div id="colorpickerHolder2" class="colorpickerHolder" data-dcolor="<?=$arResult['SETTINGS']['TEXT_MENU_COLOR']['HEX']?>"></div><?
											?><div class="colors"><?
												?><table><?
													?><tbody><?
														?><tr class="field r"><?
															?><td class="name">R</td><td class="val"><input type="text" value="<?=$arResult['SETTINGS']['TEXT_MENU_COLOR']['RGB']['R']?>" /></td><?
														?></tr><?
														?><tr class="field g"><?
															?><td class="name">G</td><td class="val"><input type="text" value="<?=$arResult['SETTINGS']['TEXT_MENU_COLOR']['RGB']['G']?>" /></td><?
														?></tr><?
														?><tr class="field b"><?
															?><td class="name">B</td><td class="val"><input type="text" value="<?=$arResult['SETTINGS']['TEXT_MENU_COLOR']['RGB']['B']?>" /></td><?
														?></tr><?
														?><tr class="field hex"><?
															?><td class="name">#</td><td class="val"><input type="text" value="<?=$arResult['SETTINGS']['TEXT_MENU_COLOR']['HEX']?>" /></td><?
														?></tr><?
													?></tbody><?
												?></table><?
											?></div><?
										?></div><?
									?></div><?
								?></div><?
							?></div><?/**/
						?></div><?
					?></div><?
					?><div class="option black_mode"><?
						?><div class="body"><?
							?><button type="button" class="<?if($arResult['SETTINGS']['blackMode']=='Y'):?> checked<?endif;?>"><span></span><?=GetMessage('RS.MONOPOLY.BLACK_MODE')?></button><?
						?></div><?
					?></div><?
				?></div><?
				?><div class="col right"><?
					?><div class="option sidebar_position switcher"><?
						?><div class="name"><?=GetMessage('RS.MONOPOLY.SIDEBAR_POS')?></div><?
						?><div class="body"><?
							?><button type="button" data-val="sidebarPos" class="sidebarPos <?if($arResult['SETTINGS']['sidebarPos']=='pos2'):?>checked<?endif;?>" data-val1="pos1" data-val2="pos2"><?=GetMessage('RS.MONOPOLY.SIDEBAR_POS_1')?><span class="icon"></span><?=GetMessage('RS.MONOPOLY.SIDEBAR_POS_2')?></button><?
						?></div><?
					?></div><?
					?><div class="option main_settings checkboxes"><?
						?><div class="name"><?=GetMessage('RS.MONOPOLY.MAIN_SETTINGS')?></div><?
						?><div class="body"><?
							?><table><?
								?><tbody><?
									?><tr><?
										?><td class="l"><button type="button" data-val="MSFichi" class="MSFichi <?if($arResult['MAIN_SETTINGS']['MSFichi']=='Y'):?>checked<?endif;?>"><span></span><?=GetMessage('RS.MONOPOLY.MS_FICHI')?></button></td><?
										?><td class="r"><button type="button" data-val="MSCatalog" class="MSCatalog <?if($arResult['MAIN_SETTINGS']['MSCatalog']=='Y'):?> checked<?endif;?>"><span></span><?=GetMessage('RS.MONOPOLY.MS_CATALOG')?></button></td><?
									?></tr><?
									?><tr><?
										?><td class="l"><button type="button" data-val="MSService" class="MSService <?if($arResult['MAIN_SETTINGS']['MSService']=='Y'):?> checked<?endif;?>"><span></span><?=GetMessage('RS.MONOPOLY.MS_SERVICE')?></button></td><?
										?><td class="r"><button type="button" data-val="MSAboutAndReviews" class="MSAboutAndReviews <?if($arResult['MAIN_SETTINGS']['MSAboutAndReviews']=='Y'):?> checked<?endif;?>"><span></span><?=GetMessage('RS.MONOPOLY.MS_ABOUT_AND_REVIEWS')?></button></td><?
									?></tr><?
									?><tr><?
										?><td class="l"><button type="button" data-val="MSNews" class="MSNews <?if($arResult['MAIN_SETTINGS']['MSNews']=='Y'):?> checked<?endif;?>"><span></span><?=GetMessage('RS.MONOPOLY.MS_NEWS')?></button></td><?
										?><td class="r"><button type="button" data-val="MSPartners" class="MSPartners <?if($arResult['MAIN_SETTINGS']['MSPartners']=='Y'):?> checked<?endif;?>"><span></span><?=GetMessage('RS.MONOPOLY.MS_PARTNERS')?></button></td><?
									?></tr><?
									?><tr><?
										?><td class="l"><button type="button" data-val="MSGallery" class="MSGallery <?if($arResult['MAIN_SETTINGS']['MSGallery']=='Y'):?> checked<?endif;?>"><span></span><?=GetMessage('RS.MONOPOLY.MS_GALLERY')?></button></td><?
										?><td class="r"><button type="button" data-val="MSSmallBanners" class="MSSmallBanners <?if($arResult['MAIN_SETTINGS']['MSSmallBanners']=='Y'):?> checked<?endif;?>"><span></span><?=GetMessage('RS.MONOPOLY.MS_SMALLBANNERS')?></button></td><?
									?></tr><?
								?></tbody><?
							?></table><?
						?></div><?
					?></div><?
					?><div class="option filter_type radioblock"><?
						?><div class="name"><?=GetMessage('RS.MONOPOLY.FILTER_TYPE')?></div><?
						?><div class="body"><?
							?><button type="button" data-val="ftype1" class="ftype1<?if($arResult['SETTINGS']['filterType']=='ftype1'):?> checked<?endif;?>"><span></span><br /><?=GetMessage('RS.MONOPOLY.FILTER_TYPE_1')?></button><?
							?><button type="button" data-val="ftype2" class="ftype2<?if($arResult['SETTINGS']['filterType']=='ftype2'):?> checked<?endif;?>"><span></span><br /><?=GetMessage('RS.MONOPOLY.FILTER_TYPE_2')?></button><?
							?><button type="button" data-val="ftype0" class="ftype0<?if($arResult['SETTINGS']['filterType']=='ftype0'):?> checked<?endif;?>"><span></span><br /><?=GetMessage('RS.MONOPOLY.FILTER_TYPE_0')?></button><?
						?></div><?
					?></div><?
					?><div class="option apply"><?
						?><div class="body"><?
							?><button type="button"><i></i><span><?=GetMessage('RS.MONOPOLY.BTN_APPLY')?></span></button><?
						?></div><?
					?></div><?
				?></div><?
			?></div><?
		?></div><?
	?></div><?
?></div><?
