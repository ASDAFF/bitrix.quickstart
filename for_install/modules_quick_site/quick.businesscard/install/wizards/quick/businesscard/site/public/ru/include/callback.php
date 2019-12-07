<div class="section parallax parallax-bg-3 <?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["CALLBACK_BG"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["CALLBACK_BG"] : COption::GetOptionString("businesscard", "QUICK_THEME_CALLBACK_BG", "light-translucent-bg", SITE_ID))?>">
	<div class="container">
		<div class="call-to-action">
			<div class="row">
				<div class="col-md-8">
					<h1 class="title text-center">Консультация по услугам</h1>
					<p class="text-center">Сотрудники компании с радостью ответят на все ваши вопросы, произведут расчет стоимости услуг и подготовят коммерческое предложение совершенно бесплатно.</p>
				</div>
				<div class="col-md-4">
					<div class="text-center">
						<a href="#" class="btn btn-lg <?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["CALLBACK_BUTTON"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["CALLBACK_BUTTON"] : COption::GetOptionString("businesscard", "QUICK_THEME_CALLBACK_BUTTON", "btn-default", SITE_ID))?>" data-toggle="modal" data-target=".FEEDBACK">Задать вопрос</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>