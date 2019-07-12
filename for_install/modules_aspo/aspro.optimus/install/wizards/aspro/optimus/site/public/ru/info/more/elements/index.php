<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Элементы");
?>
<div class="tabs_section">
	<ul class="tabs-head">
		<li class=" current">
			<span>Описание</span>
		</li>
		<li class="">
			<span>Характеристики</span>
		</li>
	</ul>
	<ul class="tabs_content tabs-body">
		<li class=" current">
			Lorem ipsum dolor sit amet, consectetur adipiscing elit
		</li>
		<li>
			Lorem ipsum dolor sit amet, consectetur adipiscing elit1
		</li>
	</ul>
</div>
<hr class="long"/>
<div class="jobs_wrapp">
	<div class="item">
		<div class="name">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tbody>
					<tr>
						<td class="title">
							<h4><span class="link">Финансовый управляющий</span></h4>
							<div class="salary">
																		Зарплата 40 000 р.																	</div>
						</td>
						<td class="salary_wrapp">
							<div class="salary">
																		Зарплата 40 000 р.																	</div>
						</td>
						<td class="icon">
							<span class="slide opener_icon no_bg"><i></i></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="description_wrapp">
			<div class="description">В обязанности данного специалиста входит анализ текущей ситуации на финансовом рынке и управление инвестиционными средствами.</div>
		</div>
	</div>
	<div class="item">
		<div class="name">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tbody>
					<tr>
						<td class="title">
							<h4><span class="link">Финансовый управляющий2</span></h4>
							<div class="salary">
																		Зарплата 40 000 р.																	</div>
						</td>
						<td class="salary_wrapp">
							<div class="salary">
																		Зарплата 50 000 р.																	</div>
						</td>
						<td class="icon">
							<span class="slide opener_icon no_bg"><i></i></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="description_wrapp">
			<div class="description">В обязанности данного специалиста входит анализ текущей ситуации на финансовом рынке.</div>
		</div>
	</div>
</div>
<hr class="long"/>
<h2></h2>
<div class="form inline FEEDBACK">
	<!--noindex-->
		<div class="form_head"><h4>Форма в 2 ряда</h4></div>
		<form name="FEEDBACK" action="<?=$APPLICATION->GetCurPage()?>" method="POST" enctype="multipart/form-data" novalidate="novalidate">
			<div class="form_body">
				<div class="form_left">
					<div class="form-control">
						<label><span>Сообщение&nbsp;<span class="star">*</span></span></label>
										<textarea data-sid="POST" required="" name="form_textarea_14" cols="40" rows="5" left=""></textarea>			</div>
																					</div>
						<div class="form_right">
																											<div class="form-control">
						<label><span>Ваше имя&nbsp;<span class="star">*</span></span></label>
										<input type="text" class="inputtext" data-sid="CLIENT_NAME" required="" name="form_text_11" value="" size="0">			</div>
																																					<div class="form-control">
						<label><span>Контактный телефон&nbsp;<span class="star">*</span></span></label>
										<input type="text" class="phone" data-sid="PHONE" required="" name="form_text_12" value="" size="0">			</div>
																																					<div class="form-control">
						<label><span>E-mail</span></label>
										<input type="email" placeholder="mail@domen.com" class="inputtext" data-sid="EMAIL" name="form_email_13" value="" size="0">			</div>
						</div>
									<div class="clearboth"></div>
							<div class="form-control captcha-row clearfix">
						<label><span>Введите текст с картинки&nbsp;<span class="star">*</span></span></label>
						<div class="captcha_image">
							<img src="/bitrix/tools/captcha.php?captcha_sid=0586a44eee9423f529fb58936c8212e8" border="0">
							<input type="hidden" name="captcha_sid" value="0586a44eee9423f529fb58936c8212e8">
							<div class="captcha_reload"></div>
						</div>
						<div class="captcha_input">
							<input type="text" class="inputtext captcha" name="captcha_word" size="30" maxlength="50" value="" required="">
						</div>
					</div>
						<div class="clearboth"></div>
			</div>
			<div class="form_footer">
				<button type="submit" class="button medium" value="submit" name="web_form_submit"><span>Отправить</span></button>
				<button type="reset" class="button medium transparent" value="reset" name="web_form_reset"><span>Отменить</span></button>
				
			</div>
		</form>
	<!--/noindex-->
</div>
<hr class="long" />
<div class="form inline FEEDBACK">
	<!--noindex-->
		<div class="form_head"><h4>Форма в ряд</h4></div>
		<form name="FEEDBACK" action="<?=$APPLICATION->GetCurPage()?>" method="POST" enctype="multipart/form-data" novalidate="novalidate">
			<div class="form_body">
				<div class="form-control">
					<label><span>Сообщение&nbsp;<span class="star">*</span></span></label>
					<textarea data-sid="POST" required="" name="form_textarea_14" cols="40" rows="5" left=""></textarea>
				</div>
				<div class="form-control">
					<label><span>Ваше имя&nbsp;<span class="star">*</span></span></label>
					<input type="text" class="inputtext" data-sid="CLIENT_NAME" required="" name="form_text_11" value="" size="0">
				</div>
				<div class="form-control">
					<label><span>Контактный телефон&nbsp;<span class="star">*</span></span></label>
					<input type="text" class="phone" data-sid="PHONE" required="" name="form_text_12" value="" size="0">			
				</div>
																																				<div class="form-control">
				<label><span>E-mail</span></label>
				<input type="email" placeholder="mail@domen.com" class="inputtext" data-sid="EMAIL" name="form_email_13" value="" size="0">			</div>

				<div class="form-control captcha-row clearfix">
					<label><span>Введите текст с картинки&nbsp;<span class="star">*</span></span></label>
					<div class="captcha_image">
						<img src="/bitrix/tools/captcha.php?captcha_sid=0586a44eee9423f529fb58936c8212e8" border="0">
						<input type="hidden" name="captcha_sid" value="0586a44eee9423f529fb58936c8212e8">
						<div class="captcha_reload"></div>
					</div>
					<div class="captcha_input">
						<input type="text" class="inputtext captcha" name="captcha_word" size="30" maxlength="50" value="" required="">
					</div>
				</div>
				<div class="clearboth"></div>
			</div>
			<div class="form_footer">
				<button type="submit" class="button medium" value="submit" name="web_form_submit"><span>Отправить</span></button>
				<button type="reset" class="button medium transparent" value="reset" name="web_form_reset"><span>Отменить</span></button>
				
			</div>
		</form>
	<!--/noindex-->
</div>
<hr class="long" />
<div class="bottom_nav block">
	<div class="ajax_load_btn">
		<span class="more_text_ajax">Показать еще</span>
	</div>
	<div class="module-pagination">
		<span class="nums">
			<ul class="flex-direction-nav">
				<li class="flex-nav-prev  disabled"><a href="<?=$APPLICATION->GetCurPage()?>" class="flex-prev"></a></li>
				<li class="flex-nav-next "><a href="<?=$APPLICATION->GetCurPage()?>" class="flex-next"></a></li>
			</ul>
			<span class="cur">1</span>
			<a href="<?=$APPLICATION->GetCurPage()?>">2</a>
			<a href="<?=$APPLICATION->GetCurPage()?>">3</a>
			<span class="point_sep"></span>
			<a href="<?=$APPLICATION->GetCurPage()?>">26</a>
		</span>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>