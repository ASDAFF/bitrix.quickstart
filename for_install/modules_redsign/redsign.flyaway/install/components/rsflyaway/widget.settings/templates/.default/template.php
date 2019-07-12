<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die(); ?>
<div id="rswidget_setting">
	<div class="shesterenka">
		<div class="descr">
			<span class="settings_show"><?=GetMessage('RS.WIDGET.SHESTERENKA_DESCR_SHOW')?></span>
			<span class="settings_hide"><?=GetMessage('RS.WIDGET.SHESTERENKA_DESCR_HIDE')?></span>
		</div>
	</div>
		<div class="settings clearfix">
			<div class="in">
				<div class="title"><?=GetMessage('RS.WIDGET.SHESTERENKA_DESCR_SHOW')?></div>
				<div class="widget_blocks setting_scroll-pane">
        	<div class="inner_widget_blocks blocks clearfix">
        		<div class="col left">
        			<?php foreach($arResult["LEFT_SETTINGS"] as $key=>$arItem):
  							switch ($arItem["TYPE"]) {
  								case 'RADIO': ?>
                    <div class="border_block">
                      <div class="block_name"><?=GetMessage($arItem["BLOCK_NAME"])?></div>
  										<? foreach($arItem["VAL"] as $kkk=>$arRadio):
  											if(isset($arRadio["NUM"]) && $arRadio["NUM"] == "Y") :?>
	  											<div class="option presets clearfix js-radio">
	  												<?if(isset($arRadio["BLOCK_NAME"]) && strlen($arRadio["BLOCK_NAME"]) > 0) {?>
	  													<div class="name"><?=GetMessage($arRadio["BLOCK_NAME"])?></div>
	  												<?}?>
							        			<div class="body">
							        				<?php
							        				$i = 0;
							        				foreach($arRadio["VALUES"] as $arVal):
							        				$i++;?>
							        					<span data-val="<?=$arVal?>" class="preset_num<?echo ($arVal == $arRadio['CHECKED'] ? ' checked' : '');?>"><?=$i?></span>
							        				<? endforeach;?>
							        			</div>
							        		</div>
  											<? elseif($kkk == "openMenuType"):?>
  												<div class="radioblock option open_menu_type clearfix js-radio">
													<?if(isset($arRadio["BLOCK_NAME"]) && strlen($arRadio["BLOCK_NAME"]) > 0) {?>
													  <div class="name"><?=GetMessage($arRadio["BLOCK_NAME"])?></div>
													<?}?>
													<div class="body">
													  <? foreach($arRadio["VALUES"] as $arVal) :?>
														<button type="button" data-val="<?=$arVal['val']?>" <?echo($arVal["val"] == $arRadio["CHECKED"] ? 'class="checked"' : '')?>>
														  <span class="radio_button">
															<span></span>
														  </span>
														  <span class="radioblock_title">
															<?=GetMessage($arVal["name"]);?><br />
															<span class="radioblock_title_pic <?=$arVal['val']?>">
															</span>
														  </span>
														</button>
													  <? endforeach; ?>
													</div>
												</div>
											<? elseif($kkk == "sidemenuType"): ?>
												<div class="radioblock option sidemenu-type clearfix js-radio">
													<?if(isset($arRadio["BLOCK_NAME"]) && strlen($arRadio["BLOCK_NAME"]) > 0) {?>
													  <div class="name"><?=GetMessage($arRadio["BLOCK_NAME"])?></div>
													<?}?>
													<div class="radioblock js-radio">
                                                        <? foreach($arRadio["VALUES"] as $key6=>$arRadioB): ?>
														  <button type="button" data-val="<?=$arRadioB['val']?>" <?echo($arRadio['CHECKED'] == $arRadioB['val'] ? 'class="checked"' : '');?> >
															<span class="radio_button">
															  <span></span>
															</span>
															<span class="radioblock_title">
															  <?=GetMessage($arRadioB["name"])?>
															</span>
														  </button>
														<? endforeach; ?>
													</div>
												</div>
												<? if(!empty($arRadio['STICKY_HEADER'])): $sh = $arRadio['STICKY_HEADER'] ?>
													<div class="switch header-sticky">
														<div class="body">
															<div class="block_switch">
																<button type="button" data-val="<?$sh['VAL']?>" class="set_sticky_header">
									                              <span class="block_switch_title"><?=GetMessage($sh['BLOCK_NAME'])?></span>
									                              <span class="bkcg_switch">
									                                <span class="bkcg_switch_ball<?echo($sh['VAL'] == 'Y' ? ' checked' : '');?>">
										                            </span>
								                                  </span>
																</button>
															</div>
														</div>
													</div>
												<?php endif; ?>
  											<?elseif($kkk == "menuType"): ?>
  												<div class="option menu_type clearfix js-radio">
							        			<?if(isset($arRadio["BLOCK_NAME"]) && strlen($arRadio["BLOCK_NAME"]) > 0) {?>
	  													<div class="name"><?=GetMessage($arRadio["BLOCK_NAME"])?></div>
	  												<?}?>
							        			<div class="body">
							        				<? foreach($arRadio["VALUES"] as $key1=>$arVal) :?>
								        				<button type="button" data-val="<?=$arVal?>" class="<?=$arVal?><?echo($arVal == $arRadio['CHECKED'] ? ' checked' : '');?>">
								        					<span></span>
								        				</button>
							        				<? endforeach; ?>
							        			</div>
							        		</div>
  											<?php
  											endif;
  										endforeach; ?>
  									</div>
                    <?break;
  								case 'COLOR':
  									$i = 0;?>
  									<div class="colors border_block no_bottom_border">
                      <div class="block_name"><?=GetMessage($arItem["BLOCK_NAME"])?></div>
		        					<div class="name_block_color clearfix">
		  									<?foreach($arItem["VAL"] as $key2=>$arColorHead):?>
		  										<div class="js-select_color rswidget_headers">
					                  <a href="#rsmw_tab<?=$i?>">
					                    <span class="border_color_widget <?echo($i == 0 ? 'active' : '')?>">
					                      <span class="color_widget" style="background-color:#<?=$arColorHead["HEX"]?>"></span>
					                    </span>
					                  <br />
                            <?=GetMessage($arColorHead["NAME"])?>
                            </a>
					                </div>
		  									<?
		  									$i++;
		  									endforeach;?>
		  								</div>
                      <div class="block_with_color">
                        <?php
                        $i = 0;
                        foreach($arItem["VAL"] as $key3=>$arColor) {
                          if ($arColor['HEX'] != "") {
                            list($rr,$gg,$bb) = sscanf($arColor['HEX'], '%2x%2x%2x');
                            $color = $arColor['HEX'];?>
                            <div class="rswidget_content <?echo($i == 0 ? 'show' : '')?>" id="rsmw_tab<?=$i?>" data-id_prop="<?=$arColor['NAME']?>">
                              <div class="rswidget_tab">
                                <div class="rswidget_cp rswidget_colorBlock<?=$i?> clearfix">
                                  <div id="colorpickerHolder<?=$i?>" class="colorpickerHolder" data-dcolor="<?=$color;?>"></div>
                                  <div class="colors">
                                    <table>
                                      <tbody>
                                        <tr class="field r">
                                          <td class="name">R</td><td class="val"><input type="text" value="<?=$rr?>" /></td>
                                        </tr>
                                        <tr class="field g">
                                          <td class="name">G</td><td class="val"><input type="text" value="<?=$gg?>" /></td>
                                        </tr>
                                        <tr class="field b">
                                          <td class="name">B</td><td class="val"><input type="text" value="<?=$bb?>" /></td>
                                        </tr>
                                        <tr class="field hex">
                                          <td class="name">#</td><td class="val"><input type="text" name="color_<?=$arColor['ID']?>" value="<?=$color?>" /></td>
                                        </tr>
                                      </tbody>
                                    </table>
                                  </div>
                                </div>
                              </div>
                            </div>
                          <?
                          $i++;
                          }
                        }?>
                      </div>
		  							</div>
  									<?break;
  								case 'CHECKBOX': ?>
                    <?if(is_array($arItem["VAL"])):?>
                      <div class="border_block no_bottom_border black_mode">
                          <? foreach($arItem["VAL"] as $key3=>$arBut):?>
          									<div class="option checkbox clearfix">
                              <button type="button" class="js-checkbox"><span class="checkbox_img <?echo($arBut['VAL'] == 'Y' ? ' checked' : '');?>"></span><?=GetMessage($arBut["NAME"])?></button>
                            </div>
        									<? endforeach; ?>
                      </div>
                    <?endif;?>
                    <? break; ?>

					<? case 'SWITCH': ?>

						<div class="option main_settings switch clearfix border_block">
						  <div class="block_name"><?=GetMessage($arItem["BLOCK_NAME"])?></div>
						  <div class="body">
							<?foreach($arItem["VAL"] as $nameSwitch=>$arSwitch):?>
							  <div class="block_switch">
								<button type="button" data-val="<?=$nameSwitch?>" class="<?=$nameSwitch?>">
								  <span class="block_switch_title"><?=GetMessage($arSwitch["name"])?></span>
								  <span class="bkcg_switch">
									<span class="bkcg_switch_ball<?echo($arSwitch['val'] == 'Y' ? ' checked' : '');?>">
								  </span>
								</span></button>
							  </div>
							<? endforeach; ?>
						  </div>
						</div>
					<? break;

  								default:
  									# code...
  									break;
  							}
        			endforeach;?>
            </div>
            <div class="col right">
              <?php foreach($arResult["RIGHT_SETTINGS"] as $key4=>$arItem):
                switch ($arItem["TYPE"]) {
                  case 'RADIO': ?>
                    <div class="border_block">
                      <div class="block_name"><?=GetMessage($arItem["BLOCK_NAME"])?></div>
                      <? foreach($arItem["VAL"] as $key5=>$arRadio):
                        if(isset($arRadio["PIC"]) && $arRadio["PIC"] == "Y"): ?>
                          <div class="option banner_type clearfix js-radio">
                            <div class="body">
                              <? foreach($arRadio["VALUES"] as $arVal) :?>
                                <div class="banner_type_block">
                                  <button type="button" data-val="<?=$arVal['val']?>" class="banner_menu_pic <?=$arVal['val']?><?echo($arRadio['CHECKED'] == $arVal['val'] ? ' checked' : '');?>">
                                    <span></span>
                                  </button>
                                  <span class="banner_type_block_name"><?=GetMessage($arVal["name"])?></span>
                                </div>
                              <? endforeach; ?>
                            </div>
                          </div>
                        <? else: ?>
                          <div class="filter_side radioblock js-radio">
                            <? foreach($arRadio["VALUES"] as $key6=>$arRadioB): ?>
                              <button type="button" data-val="<?=$arRadioB['val']?>" <?echo($arRadio['CHECKED'] == $arRadioB['val'] ? 'class="checked"' : '');?> >
                                <span class="radio_button">
                                  <span></span>
                                </span>
                                <span class="radioblock_title">
                                  <?=GetMessage($arRadioB["name"])?>
                                </span>
                              </button>
                            <? endforeach; ?>
                          </div>
                        <?php endif;
                      endforeach; ?>
                    </div>
                    <? break;
                  case 'SWITCH':?>
                    <div class="option main_settings switch clearfix border_block">
                      <div class="block_name"><?=GetMessage($arItem["BLOCK_NAME"])?></div>
                      <div class="body">
                        <?foreach($arItem["VAL"] as $nameSwitch=>$arSwitch):?>
                          <div class="block_switch">
                            <button type="button" data-val="<?=$nameSwitch?>" class="<?=$nameSwitch?>">
                              <span class="block_switch_title"><?=GetMessage($arSwitch["name"])?></span>
                              <span class="bkcg_switch">
                                <span class="bkcg_switch_ball<?echo($arSwitch['val'] == 'Y' ? ' checked' : '');?>">
                              </span>
                            </span></button>
                          </div>
                        <? endforeach; ?>
                      </div>
                    </div>
                    <?break;
                  case 'CHECKBOX': ?>
                    <div class="border_block">
                      <? foreach($arItem["VAL"] as $key3=>$arBut):?>
                        <div class="option checkbox clearfix">
                          <div class="name">
                            <button type="button" class="js-checkbox"><span class="checkbox_img"></span><?=GetMessage($arBut["NAME"])?></button>
                          </div>
                        </div>
                      <? endforeach; ?>
                    </div>
                    <?break;

                  default:
                    # code...
                    break;
                }
              endforeach;?>
	        		<div class="border_block">
                <div class="save_button clearfix"><i class="save_button_pic"></i><?=GetMessage("RS.WIDGET.BTN_APPLY")?><i class="icon_load"></i></div>
                <div class="after_save_button clearfix">
                  <div class="after_save_button_default"><?=GetMessage("RS.WIDGET.BTN_DEFAULT")?></div>
                  <div class="after_save_button_buy"><?=GetMessage("RS.WIDGET.DOCUMENT")?></div>
                </div>
              </div>
              <?/*
              <div class="border_block look_on_bh">
                <a href="http://bighammer.ru"><div class="look_on_bh_button"></div></a>
                <span><?=GetMessage("RS.WIDGET.TEST_ON_BH")?></span>
              </div>
              */?>
	        	</div>

        	</div>
        </div>
			</div>
		</div>
	</div>
</div><?
