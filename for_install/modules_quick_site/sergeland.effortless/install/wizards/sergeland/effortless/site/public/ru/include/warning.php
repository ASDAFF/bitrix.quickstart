<div class="page-top <?=(!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["WARNING_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["WARNING_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_WARNING_BG", "white-bg", SITE_ID))?>">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="call-to-action">
					<h1 class="title">Бесплатная консультация специалиста</h1>
					<p class="col-md-8 col-md-offset-2">Сотрудники компании с радостью <strong>ответят</strong> на все ваши вопросы и произведут расчет стоимости услуг совершенно <strong>бесплатно</strong>.</p>
					<div class="clearfix"></div>
					<a class="btn btn-white more" data-toggle="modal" data-target=".FEEDBACK">Задать вопрос<i class="pl-10 fa fa-info"></i></a>
					<a href="callto:+74954567890" class="btn btn-default contact">+7(495) 456 7890<i class="pl-10 fa fa-phone"></i></a>
				</div>
			</div>
		</div>
	</div>
</div>