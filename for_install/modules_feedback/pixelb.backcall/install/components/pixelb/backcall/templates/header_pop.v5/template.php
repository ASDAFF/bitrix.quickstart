<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if($_REQUEST['pb_send_mode'] == 'pb_ajax_get_form'){//msg form?>

	<?$point_count = 1;?>

	<div class="pop_wrapper backcall_form_pop_v5"><!-- root element req-->

			<div class="pop_header">
				<div class="pop_header_wrapper">
					<span class="pop_heading_label">
						<?=$arParams['POP_HEADING_LABEL'] ? $arParams['POP_HEADING_LABEL'] : GetMessage('PBMF_ORDER_CALLBACK_LABEL')?>
					</span>
					<img src="<?=$templateFolder.'/img/pop_close_btn.png'?>" class="pop_close_btn" />
				</div>
			</div>

			<div class="pop_content">

				<div class="comment-form"><?/*--requiered "a" parent--*/?>

					<?if(count($arResult['EMAIL_TO_IBLOCK'])){?>

						<?
						$arEmailElement = reset($arResult['EMAIL_TO_IBLOCK']);
						?>

						<input type="hidden" name="email_to_iblock<?=$arParams['FORM_ID']?>" class="email_to_iblock" value="<?=$arEmailElement['ID']?>" />


						<div class="form_section">
							<div class="section_number">
								<span class="number_value"><?=$point_count?></span>
								<?=$arParams['EMAIL_TO_IBLOCK_LABEL']?>
							</div>
							<div class="section_content">

								<div class="select_wrapper">
									<div class="select_value"></div>
									<div class="select_list">
										<?foreach ($arResult['EMAIL_TO_IBLOCK'] as $email_item){?>
											<div class="select_list_value" rel="<?=$email_item['ID']?>"><?=$email_item['NAME']?></div>
										<?}?>
									</div>
								</div>

								<div class="clear_both"></div>

							</div>
							<div class="clear_both"></div>
						</div>

						<?$point_count++;?>
					<?}?>


					<div class="form_section">
						<div class="section_number">
							<span class="number_value"><?=$point_count > 1 ? $point_count : '' ?></span>
							<?=$arParams['CONTACT_FIELDS_LABEL']?>
						</div>
						<div class="section_content">

							<div class="left_part">

								<?if(in_array('form_client_name',$arParams['ENABLED_FIELDS'])){?>
									<div class="input_holder">
										<label class="input_holder_wrapper">
											<input type="text" class="form_client_name" name="form_client_name<?=$arParams['FORM_ID']?>" placeholder="<?=GetMessage("PBMF_SERVICES_NAME_LABEL");?>" />
										</label>
									</div>
								<?}?>

								<?if(in_array('form_client_phone',$arParams['ENABLED_FIELDS'])){?>
									<div class="input_holder">
										<label class="input_holder_wrapper">
											<input type="text" class="form_client_phone" name="form_client_phone<?=$arParams['FORM_ID']?>" placeholder="<?=GetMessage("PBMF_SERVICES_PHONE_LABEL");?>" />
										</label>
									</div>
								<?}?>

								<?if(in_array('form_email',$arParams['ENABLED_FIELDS'])){?>
									<div class="input_holder">
										<label class="input_holder_wrapper">
											<input type="text" class="form_email" name="form_email<?=$arParams['FORM_ID']?>" placeholder="<?=GetMessage("PBMF_SERVICES_MAIL_LABEL");?>" />
										</label>
									</div>
								<?}?>

								<?if(in_array('form_comment',$arParams['ENABLED_FIELDS'])){?>
									<div class="input_holder">
										<label class="input_holder_wrapper">
											<textarea class="form_comment" placeholder="<?=GetMessage("PBMF_SERVICES_COMMENTS_LABEL");?>"></textarea>
										</label>
									</div>
								<?}?>

								<?if($arResult["CAPTCHA"]){?>
									<div class="input_holder">
										<div class="input_holder_wrapper">
											<label class="capcha_input_holder">
												<img class="c_img" src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA"]?>" alt="CAPTCHA" />
												<input type="hidden" class="captcha_sid" name="captcha_sid<?=$arParams['FORM_ID']?>" value="<?=$arResult["CAPTCHA"]?>"/>
												<input type="text" class="captcha_word" name="captcha_word<?=$arParams['FORM_ID']?>" size="30" maxlength="50" value="" placeholder="<?=GetMessage("PBMF_SERVICES_CAPCHA_LABEL");?>"/>
												<div class="clear_both"></div>
											</label>
										</div>
									</div>
								<?}?>

							</div>

							<div class="right_part">

								<a class="form_v5_trigger" href="#"><?=$arParams['TRIGGER_LABEL'] ? $arParams['TRIGGER_LABEL'] : GetMessage('PBMF_SERVICES_SUBMIT_LABEL')?></a>

							</div>

							<div class="clear_both"></div>


							<?if($arParams['SHOW_FORM_RULES'] == 'Y' && $arParams['FORM_RULES_ADDRESS']){?>
								<div class="pb_form_rules_wrapper">
									<label>
										<input type="checkbox" class="pb_form_rules" name="pb_form_rules<?=$arParams['FORM_ID']?>"  value="1" checked="checked" />
									<?=GetMessage('PB_FORM_RULES_LABEL_1')?> <?=$arParams['TRIGGER_LABEL'] ? $arParams['TRIGGER_LABEL'] : GetMessage('PBMF_SERVICES_SUBMIT_LABEL')?> <?=GetMessage('PB_FORM_RULES_LABEL_2')?> <a target="_blank" href="<?=$arParams['FORM_RULES_ADDRESS']?>"><?=GetMessage('PB_FORM_RULES_LABEL_3')
										?></a>
									</label>
								</div>
							<?}?>

						</div>
						<div class="clear_both"></div>
					</div>



					<input type="hidden" name="pb_form_id[]" class="pb_form_id" value="<?=$arParams['FORM_ID']?>" />

				</div>
			</div>

			<div class="pop_footer"></div>

		</div>


<?}else{//trigger form?>

	<?if($arParams['USE_ICON'] == 0){?>

		<span class="form_v5_callback_element" id="pb_form_trigger_<?=$arParams['FORM_ID'];?>"><!-- parent required --><img class="first_img" src="<?=$templateFolder.'/img/top_phone_btn_v3_hover
	.png'?>" alt="<?=$arParams['TRIGGER_LABEL']?>" /><img class="second_img" src="<?=$templateFolder.'/img/top_phone_btn_v3.png'?>" alt="<?=$arParams['TRIGGER_LABEL']?>"><span class="header_top_back_call_trigger"><?=$arParams['TRIGGER_LABEL']?></span><input type="hidden" name="pb_form_id[]" class="pb_form_id" value="<?=$arParams['FORM_ID']?>" /></span>

	<?}else{?>

		<span class="form_v5_callback_element" id="pb_form_trigger_<?=$arParams['FORM_ID'];?>"><!-- parent required --><img class="first_img" src="<?=$templateFolder.'/img/msg_btn_v3_hover.png'?>" alt="<?=$arParams['TRIGGER_LABEL']?>" /><img class="second_img" src="<?=$templateFolder.'/img/msg_btn_v3.png'?>" alt="<?=$arParams['TRIGGER_LABEL']?>"><span class="header_top_back_call_trigger"><?=$arParams['TRIGGER_LABEL']?></span><input type="hidden" name="pb_form_id[]" class="pb_form_id" value="<?=$arParams['FORM_ID']?>" /></span>

	<?}?>
<?}?>