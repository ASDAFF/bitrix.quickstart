<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
?>

<div class="main-profile main-profile-full">
	<?ShowError($arResult["strProfileError"])?>
	
	<?if ($arResult['DATA_SAVED'] == 'Y') {
		ShowNote(GetMessage('PROFILE_DATA_SAVED'));
	}?>
	
	<form class="form form-profile" method="post" action="<?=$arResult['FORM_TARGET']?>?" enctype="multipart/form-data" role="profile">
		<?=$arResult['BX_SESSION_CHECK']?>
		<input type="hidden" name="lang" value="<?=LANG?>"/>
		<input type="hidden" name="ID" value="<?=$arResult['ID']?>"/>
		
		<ul class="nav nav-tabs" role="tablist">
			<li class="active">
				<a href="#regular" role="tab" data-toggle="tab"><?=GetMessage('REG_SHOW_HIDE')?></a>
			</li>
			<?if ($arResult['TIME_ZONE_ENABLED']) {
				?>
				<li>
					<a href="#time-zones" role="tab" data-toggle="tab"><?=GetMessage('main_profile_time_zones')?></a>
				</li>
				<?
			}?>
			<?if ($arResult['INCLUDE_PERSONAL'] == 'Y') {
				?>
				<li>
					<a href="#personal" role="tab" data-toggle="tab"><?=GetMessage('USER_PERSONAL_INFO')?></a>
				</li>
				<?
			}?>
			<?if ($arResult['INCLUDE_WORK'] == 'Y') {
				?>
				<li>
					<a href="#work" role="tab" data-toggle="tab"><?=GetMessage('USER_WORK_INFO')?></a>
				</li>
				<?
			}?>
			<?if ($arResult['INCLUDE_FORUM'] == 'Y') {
				?>
				<li>
					<a href="#forum" role="tab" data-toggle="tab"><?=GetMessage('forum_INFO')?></a>
				</li>
				<?
			}?>
			<?if ($arResult['INCLUDE_BLOG'] == 'Y') {
				?>
				<li>
					<a href="#blog" role="tab" data-toggle="tab"><?=GetMessage('blog_INFO')?></a>
				</li>
				<?
			}?>
			<?if ($arResult['INCLUDE_LEARNING'] == 'Y') {
				?>
				<li>
					<a href="#learning" role="tab" data-toggle="tab"><?=GetMessage('learning_INFO')?></a>
				</li>
				<?
			}?>
			<?if ($arResult['IS_ADMIN']) {
				?>
				<li>
					<a href="#admin" role="tab" data-toggle="tab"><?=GetMessage('USER_ADMIN_NOTES')?></a>
				</li>
				<?
			}?>
		</ul>
		
		<div class="tab-content">
			<div class="tab-pane active" id="regular">
				<?if ($arResult['ID']>0) {
					if (strlen($arResult['arUser']['TIMESTAMP_X']) > 0) {
						?>
						<div class="form-group">
							<label><?=GetMessage('LAST_UPDATE')?>:</label>
							<div class="form-control-static"><?=$arResult['arUser']['TIMESTAMP_X']?></div>
						</div>
						<?
					}
					
					if (strlen($arResult['arUser']['LAST_LOGIN']) > 0) {
						?>
						<div class="form-group">
							<label><?=GetMessage('LAST_LOGIN')?>:</label>
							<div class="form-control-static"><?=$arResult["arUser"]["LAST_LOGIN"]?></div>
						</div>
						<?
					}
				}?>
				
				<div class="form-group">
					<label class="control-label" for="profile-name"><?=GetMessage('NAME')?>:</label>
					<input class="form-control" type="text" name="NAME" id="profile-name" value="<?=$arResult['arUser']['NAME']?>" maxlength="50"/>
				</div>
				<div class="form-group">
					<label class="control-label" for="profile-last-name"><?=GetMessage('LAST_NAME')?>:</label>
					<input class="form-control" type="text" name="LAST_NAME" id="profile-last-name" value="<?=$arResult['arUser']['LAST_NAME']?>" maxlength="50"/>
				</div>
				<div class="form-group">
					<label class="control-label" for="profile-second-name"><?=GetMessage('SECOND_NAME')?>:</label>
					<input class="form-control" type="text" name="SECOND_NAME" id="profile-second-name" value="<?=$arResult['arUser']['SECOND_NAME']?>" maxlength="50"/>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label required" for="profile-email"><?=GetMessage('EMAIL')?>:</label>
							<input class="form-control" type="email" name="EMAIL" id="profile-email" value="<?=$arResult['arUser']['EMAIL']?>" maxlength="50" required=""/>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label required" for="profile-login"><?=GetMessage('LOGIN')?>:</label>
							<input class="form-control" type="text" name="LOGIN" id="profile-login" value="<?=$arResult['arUser']['LOGIN']?>" maxlength="50" required=""/>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group has-feedback">
							<label class="control-label" for="profile-new-pass-req"><?=GetMessage('NEW_PASSWORD_REQ')?>:</label>
							<input class="form-control" type="password" name="NEW_PASSWORD" id="profile-new-pass-req" value="" maxlength="50" autocomplete="off"/>
							
							<?if ($arResult['SECURE_AUTH']) {
								?>
								<noscript>
									<span class="glyphicon glyphicon-unlock form-control-feedback" title="<?=GetMessage('AUTH_NONSECURE_NOTE')?>"></span>
								</noscript>
								<script>
									document.write('<span class="glyphicon glyphicon-lock form-control-feedback" title="<?=GetMessage('AUTH_SECURE_NOTE')?>"></span>');
								</script>
								<?
							}?>
							
							<p class="help-block"><?=$arResult['GROUP_POLICY']['PASSWORD_REQUIREMENTS']?></p>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group has-feedback">
							<label class="control-label" for="profile-new-pass-conf"><?=GetMessage('NEW_PASSWORD_CONFIRM')?>:</label>
							<input class="form-control" type="password" name="NEW_PASSWORD_CONFIRM" id="profile-new-pass-conf" value="" maxlength="50" autocomplete="off"/>
						</div>
					</div>
				</div>
				
				<?if ($arResult['USER_PROPERTIES']['SHOW'] == 'Y') {
					foreach ($arResult['USER_PROPERTIES']['DATA'] as $fieldName => $field) {
						$domId = 'profile-prop-' . $field['ID'];
						?>
						<div class="form-group">
							<label class="control-label field-<?=strtolower($fieldName)?><?=$field['MANDATORY'] == 'Y' ? ' required' : ''?>" for="<?=$domId?>"><?=$field['EDIT_FORM_LABEL']?>:</label>
							<?$APPLICATION->IncludeComponent(
								'bitrix:system.field.edit',
								$field['USER_TYPE']['USER_TYPE_ID'],
								array(
									'bVarsFromForm' => $arResult['bVarsFromForm'],
									'arUserField' => $field,
									'domID' => $domId,
								),
								null,
								array(
									'HIDE_ICONS'=>'Y'
								)
							);?>
						</div>
						<?
					}
				}?>
			</div>
			
			<?if ($arResult['TIME_ZONE_ENABLED']) {
				?>
				<div class="tab-pane" id="time-zones">
					<div class="form-group">
						<label class="control-label" for="profile-auto-time-zone"><?=GetMessage('main_profile_time_zones_auto')?>:</label>
						<select class="form-control" name="AUTO_TIME_ZONE" id="profile-auto-time-zone" onchange="this.form.TIME_ZONE.disabled = this.value != 'N'">
							<option value=""><?=GetMessage('main_profile_time_zones_auto_def')?></option>
							<option value="Y"<?=$arResult['arUser']['AUTO_TIME_ZONE'] == 'Y'? ' selected=""' : ''?>><?=GetMessage('main_profile_time_zones_auto_yes')?></option>
							<option value="N"<?=$arResult['arUser']['AUTO_TIME_ZONE'] == 'N'? ' selected=""' : ''?>><?=GetMessage('main_profile_time_zones_auto_no')?></option>
						</select>
					</div>
					<div class="form-group">
						<label class="control-label" for="profile-time-zone"><?=GetMessage('main_profile_time_zones_zones')?>:</label>
						<select class="form-control" name="TIME_ZONE" id="profile-time-zone"<?=$arResult['arUser']['AUTO_TIME_ZONE'] <> 'N' ? ' disabled=""' : ''?>>
							<?foreach($arResult['TIME_ZONE_LIST'] as $tz => $tzName) {
								?>
								<option value="<?=htmlspecialchars($tz)?>"<?=$arResult['arUser']['TIME_ZONE'] == $tz ? ' selected=""' : ''?>>
									<?=htmlspecialchars($tzName)?>
								</option>
								<?
							}?>
						</select>
					</div>
				</div>
				<?
			}?>
			
			<?if ($arResult['INCLUDE_PERSONAL'] == 'Y') {
				?>
				<div class="tab-pane" id="personal">
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="form-group">
								<label class="control-label" for="profile-personal-profession"><?=GetMessage('USER_PROFESSION')?>:</label>
								<input class="form-control" type="text" name="PERSONAL_PROFESSION" id="profile-personal-profession" value="<?=$arResult['arUser']['PERSONAL_PROFESSION']?>" maxlength="255"/>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-personal-www"><?=GetMessage('USER_WWW')?>:</label>
								<input class="form-control" type="text" name="PERSONAL_WWW" id="profile-personal-www" value="<?=$arResult['arUser']['PERSONAL_WWW']?>" maxlength="255"/>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-personal-icq"><?=GetMessage('USER_ICQ')?>:</label>
								<input class="form-control" type="text" name="PERSONAL_ICQ" id="profile-personal-icq" value="<?=$arResult['arUser']['PERSONAL_ICQ']?>" maxlength="255"/>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-personal-gender"><?=GetMessage('USER_GENDER')?>:</label>
								<select class="form-control" name="PERSONAL_GENDER" id="profile-personal-gender">
									<option value=""><?=GetMessage('USER_DONT_KNOW')?></option>
									<option value="M"<?=$arResult['arUser']['PERSONAL_GENDER'] == 'M' ? ' selected=""' : ''?>><?=GetMessage('USER_MALE')?></option>
									<option value="F"<?=$arResult['arUser']['PERSONAL_GENDER'] == 'F' ? ' selected=""' : ''?>><?=GetMessage('USER_FEMALE')?></option>
								</select>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-personal-birthday"><?=GetMessage('USER_BIRTHDAY_DT')?> (<?=$arResult['DATE_FORMAT']?>):</label>
								<input class="form-control widget datepicker" type="date" name="PERSONAL_BIRTHDAY" id="profile-personal-birthday" value="<?=$arResult['arUser']['PERSONAL_BIRTHDAY']?>" maxlength="9"/>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-personal-photo"><?=GetMessage('USER_PHOTO')?>:</label>
								<input class="form-control widget uploadpicker" type="file" name="PERSONAL_PHOTO" id="profile-personal-photo" value=""/>
								<?if (strlen($arResult['arUser']['PERSONAL_PHOTO']) > 0) {
									?>
									<a class="thumbnail">
										<?=$arResult['arUser']['PERSONAL_PHOTO_HTML']?>
									</a>
									<?
								}?>
							</div>
						</div>
					</div>
					
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><?=GetMessage('USER_PHONES')?></h3>
						</div>
						<div class="panel-body">
							<div class="form-group">
								<label class="control-label" for="profile-personal-phone"><?=GetMessage('USER_PHONE')?>:</label>
								<input class="form-control" type="text" name="PERSONAL_PHONE" id="profile-personal-phone" value="<?=$arResult['arUser']['PERSONAL_PHONE']?>" maxlength="255"/>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-personal-fax"><?=GetMessage('USER_FAX')?>:</label>
								<input class="form-control" type="text" name="PERSONAL_FAX" id="profile-personal-fax" value="<?=$arResult['arUser']['PERSONAL_FAX']?>" maxlength="255"/>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-personal-mobile"><?=GetMessage('USER_MOBILE')?>:</label>
								<input class="form-control" type="text" name="PERSONAL_MOBILE" id="profile-personal-mobile" value="<?=$arResult['arUser']['PERSONAL_MOBILE']?>" maxlength="255"/>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-personal-pager"><?=GetMessage('USER_PAGER')?>:</label>
								<input class="form-control" type="text" name="PERSONAL_PAGER" id="profile-personal-pager" value="<?=$arResult['arUser']['PERSONAL_PAGER']?>" maxlength="255"/>
							</div>
						</div>
					</div>
					
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><?=GetMessage('USER_POST_ADDRESS')?></h3>
						</div>
						<div class="panel-body">
							<div class="form-group">
								<label class="control-label" for="PERSONAL_COUNTRY"><?=GetMessage('USER_COUNTRY')?>:</label>
								<?=preg_replace('/class=["\'].+["\']/', 'class="form-control"', $arResult['COUNTRY_SELECT'])?>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-personal-state"><?=GetMessage('USER_STATE')?>:</label>
								<input class="form-control" type="text" name="PERSONAL_STATE" id="profile-personal-state" value="<?=$arResult['arUser']['PERSONAL_STATE']?>" maxlength="255"/>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-personal-city"><?=GetMessage('USER_CITY')?>:</label>
								<input class="form-control" type="text" name="PERSONAL_CITY" id="profile-personal-city" value="<?=$arResult['arUser']['PERSONAL_CITY']?>" maxlength="255"/>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-personal-zip"><?=GetMessage('USER_ZIP')?>:</label>
								<input class="form-control" type="text" name="PERSONAL_ZIP" id="profile-personal-zip" value="<?=$arResult['arUser']['PERSONAL_ZIP']?>" maxlength="255"/>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-personal-street"><?=GetMessage('USER_STREET')?>:</label>
								<input class="form-control" type="text" name="PERSONAL_STREET" id="profile-personal-street" value="<?=$arResult['arUser']['PERSONAL_STREET']?>" maxlength="255"/>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-personal-mailbox"><?=GetMessage('USER_MAILBOX')?>:</label>
								<input class="form-control" type="text" name="PERSONAL_MAILBOX" id="profile-personal-mailbox" value="<?=$arResult['arUser']['PERSONAL_MAILBOX']?>" maxlength="255"/>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-personal-notes"><?=GetMessage('USER_NOTES')?>:</label>
								<textarea class="form-control" name="PERSONAL_NOTES" id="profile-personal-notes"><?=$arResult['arUser']['PERSONAL_NOTES']?></textarea>
							</div>
						</div>
					</div>
				</div>
				<?
			}?>
			
			<?if ($arResult['INCLUDE_WORK'] == 'Y') {
				?>
				<div class="tab-pane" id="work">
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="form-group">
								<label class="control-label" for="profile-work-company"><?=GetMessage('USER_COMPANY')?>:</label>
								<input class="form-control" type="text" name="WORK_COMPANY" id="profile-work-company" value="<?=$arResult['arUser']['WORK_COMPANY']?>" maxlength="255"/>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-work-www"><?=GetMessage('USER_WWW')?>:</label>
								<input class="form-control" type="text" name="WORK_WWW" id="profile-work-www" value="<?=$arResult['arUser']['WORK_WWW']?>" maxlength="255"/>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-work-department"><?=GetMessage('USER_DEPARTMENT')?>:</label>
								<input class="form-control" type="text" name="WORK_DEPARTMENT" id="profile-work-department" value="<?=$arResult['arUser']['WORK_DEPARTMENT']?>" maxlength="255"/>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-work-position"><?=GetMessage('USER_POSITION')?>:</label>
								<input class="form-control" type="text" name="WORK_POSITION" id="profile-work-position" value="<?=$arResult['arUser']['WORK_POSITION']?>" maxlength="255"/>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-work-profile"><?=GetMessage('USER_WORK_PROFILE')?>:</label>
								<textarea class="form-control" name="WORK_PROFILE" id="profile-work-profile"><?=$arResult['arUser']['WORK_PROFILE']?></textarea>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-work-logo"><?=GetMessage('USER_LOGO')?>:</label>
								<input class="form-control widget uploadpicker" type="file" name="WORK_LOGO" id="profile-work-logo" value=""/>
								<?if (strlen($arResult['arUser']['WORK_LOGO']) > 0) {
									?>
									<a class="thumbnail">
										<?=$arResult['arUser']['WORK_LOGO_HTML']?>
									</a>
									<?
								}?>
							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><?=GetMessage('USER_PHONES')?></h3>
						</div>
						<div class="panel-body">
							<div class="form-group">
								<label class="control-label" for="profile-work-phone"><?=GetMessage('USER_PHONE')?>:</label>
								<input class="form-control" type="text" name="WORK_PHONE" id="profile-work-phone" value="<?=$arResult['arUser']['WORK_PHONE']?>" maxlength="255"/>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-work-fax"><?=GetMessage('USER_FAX')?>:</label>
								<input class="form-control" type="text" name="WORK_FAX" id="profile-work-fax" value="<?=$arResult['arUser']['WORK_FAX']?>" maxlength="255"/>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-work-pager"><?=GetMessage('USER_PAGER')?>:</label>
								<input class="form-control" type="text" name="WORK_PAGER" id="profile-work-pager" value="<?=$arResult['arUser']['WORK_PAGER']?>" maxlength="255"/>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-work-pager"><?=GetMessage('USER_POST_ADDRESS')?>:</label>
								<input class="form-control" type="text" name="WORK_PAGER" id="profile-work-pager" value="<?=$arResult['arUser']['WORK_PAGER']?>" maxlength="255"/>
							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><?=GetMessage('USER_POST_ADDRESS')?></h3>
						</div>
						<div class="panel-body">
							<div class="form-group">
								<label class="control-label" for="WORK_COUNTRY"><?=GetMessage('USER_COUNTRY')?>:</label>
								<?=preg_replace('/class=["\'].+["\']/', 'class="form-control"', $arResult['COUNTRY_SELECT_WORK'])?>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-work-state"><?=GetMessage('USER_STATE')?>:</label>
								<input class="form-control" type="text" name="WORK_STATE" id="profile-work-state" value="<?=$arResult['arUser']['WORK_STATE']?>" maxlength="255"/>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-work-city"><?=GetMessage('USER_CITY')?>:</label>
								<input class="form-control" type="text" name="WORK_CITY" id="profile-work-city" value="<?=$arResult['arUser']['WORK_CITY']?>" maxlength="255"/>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-work-zip"><?=GetMessage('USER_ZIP')?>:</label>
								<input class="form-control" type="text" name="WORK_ZIP" id="profile-work-zip" value="<?=$arResult['arUser']['WORK_ZIP']?>" maxlength="255"/>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-work-street"><?=GetMessage('USER_STREET')?>:</label>
								<input class="form-control" type="text" name="WORK_STREET" id="profile-work-street" value="<?=$arResult['arUser']['WORK_STREET']?>" maxlength="255"/>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-work-mailbox"><?=GetMessage('USER_MAILBOX')?>:</label>
								<input class="form-control" type="text" name="WORK_MAILBOX" id="profile-work-mailbox" value="<?=$arResult['arUser']['WORK_MAILBOX']?>" maxlength="255"/>
							</div>
							<div class="form-group">
								<label class="control-label" for="profile-work-notes"><?=GetMessage('USER_NOTES')?>:</label>
								<textarea class="form-control" name="WORK_NOTES" id="profile-work-notes"><?=$arResult['arUser']['WORK_NOTES']?></textarea>
							</div>
						</div>
					</div>
				</div>
				<?
			}?>
			
			<?if ($arResult['INCLUDE_FORUM'] == 'Y') {
				?>
				<div class="tab-pane" id="forum">
					<div class="form-group">
						<label class="checkbox-inline">
							<input type="checkbox" name="forum_SHOW_NAME" value="Y"<?=$arResult['arForumUser']['SHOW_NAME'] == 'Y' ? ' checked=""' : ''?>/>
							<?=GetMessage('forum_SHOW_NAME')?>
						</label>
					</div>
					<div class="form-group">
						<label class="control-label" for="profile-forum-description"><?=GetMessage('forum_DESCRIPTION')?>:</label>
						<input class="form-control" type="text" name="forum_DESCRIPTION" id="profile-forum-description" value="<?=$arResult['arForumUser']['DESCRIPTION']?>" maxlength="255"/>
					</div>
					<div class="form-group">
						<label class="control-label" for="profile-forum-interests"><?=GetMessage('forum_INTERESTS')?>:</label>
						<textarea class="form-control" name="forum_INTERESTS" id="profile-forum-interests"><?=$arResult['arForumUser']['INTERESTS']?></textarea>
					</div>
					<div class="form-group">
						<label class="control-label" for="profile-forum-interests"><?=GetMessage('forum_SIGNATURE')?>:</label>
						<textarea class="form-control" name="forum_SIGNATURE" id="profile-forum-interests"><?=$arResult['arForumUser']['SIGNATURE']?></textarea>
					</div>
					<div class="form-group">
						<label class="control-label" for="profile-forum-avatar"><?=GetMessage('forum_AVATAR')?>:</label>
						<input class="form-control widget uploadpicker" type="file" name="forum_AVATAR" id="profile-forum-avatar" value=""/>
						<?if (strlen($arResult['arForumUser']['AVATAR']) > 0) {
							?>
							<a class="thumbnail">
								<?=$arResult['arForumUser']['AVATAR_HTML']?>
							</a>
							<?
						}?>
					</div>
				</div>
				<?
			}?>
			
			<?if ($arResult['INCLUDE_BLOG'] == 'Y') {
				?>
				<div class="tab-pane" id="blog">
					<div class="form-group">
						<label class="control-label" for="profile-blog-alias"><?=GetMessage('blog_ALIAS')?>:</label>
						<input class="form-control" type="text" name="blog_ALIAS" id="profile-blog-alias" value="<?=$arResult['arBlogUser']['ALIAS']?>" maxlength="255"/>
					</div>
					<div class="form-group">
						<label class="control-label" for="profile-blog-description"><?=GetMessage('blog_DESCRIPTION')?>:</label>
						<input class="form-control" type="text" name="blog_DESCRIPTION" id="profile-blog-description" value="<?=$arResult['arBlogUser']['DESCRIPTION']?>" maxlength="255"/>
					</div>
					<div class="form-group">
						<label class="control-label" for="profile-blog-interests"><?=GetMessage('blog_INTERESTS')?>:</label>
						<textarea class="form-control" name="blog_INTERESTS" id="profile-blog-interests"><?=$arResult['arBlogUser']['INTERESTS']?></textarea>
					</div>
					<div class="form-group">
						<label class="control-label" for="profile-blog-avatar"><?=GetMessage('blog_AVATAR')?>:</label>
						<input class="form-control widget uploadpicker" type="file" name="blog_AVATAR" id="profile-blog-avatar" value=""/>
						<?if (strlen($arResult['arBlogUser']['AVATAR']) > 0) {
							?>
							<a class="thumbnail">
								<?=$arResult['arBlogUser']['AVATAR_HTML']?>
							</a>
							<?
						}?>
					</div>
				</div>
				<?
			}?>
			
			<?if ($arResult['INCLUDE_LEARNING'] == 'Y') {
				?>
				<div class="tab-pane" id="learning">
					<div class="form-group">
						<label class="checkbox-inline">
							<input type="checkbox" name="student_PUBLIC_PROFILE" value="Y"<?=$arResult['arStudent']['PUBLIC_PROFILE'] == 'Y' ? ' checked=""' : ''?>/>
							<?=GetMessage('learning_PUBLIC_PROFILE')?>
						</label>
					</div>
					<div class="form-group">
						<label class="control-label" for="profile-learning-resume"><?=GetMessage('learning_RESUME')?>:</label>
						<textarea class="form-control" name="student_RESUME" id="profile-learning-resume"><?=$arResult['arStudent']['RESUME']?></textarea>
					</div>
					<div class="form-group">
						<label class="control-label" for="profile-blog-alias"><?=GetMessage('learning_TRANSCRIPT')?>:</label>
						<div class="form-control-static"><?=$arResult['arStudent']['TRANSCRIPT']?>-<?=$arResult['ID']?></div>
					</div>
				</div>
				<?
			}?>
			
			<?if ($arResult['IS_ADMIN']) {
				?>
				<div class="tab-pane" id="admin">
					<div class="form-group">
						<label class="control-label" for="profile-admin-notes"><?=GetMessage('USER_ADMIN_NOTES')?>:</label>
						<textarea class="form-control" name="ADMIN_NOTES" id="profile-admin-notes"><?=$arResult['arUser']['ADMIN_NOTES']?></textarea>
					</div>
				</div>
				<?
			}?>
		</div>
		
		<div class="form-group form-toolbar">
			<input type="hidden" name="save" value="y"/>
			<button class="btn btn-default" type="submit"><?=$arResult['ID'] > 0 ? GetMessage('MAIN_SAVE') : GetMessage('MAIN_ADD')?></button>
		</div>
		
		<div class="form-group form-info">
			<p class="help-block">
				<span class="required"></span><?=GetMessage("PROFILE_REQ")?>
			</p>
		</div>
	</form>
</div>