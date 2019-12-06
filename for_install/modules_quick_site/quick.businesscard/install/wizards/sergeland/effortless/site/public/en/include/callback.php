<div class="section parallax parallax-bg-3 <?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["CALLBACK_BG"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["CALLBACK_BG"] : COption::GetOptionString("effortless", "QUICK_THEME_CALLBACK_BG", "light-translucent-bg", SITE_ID))?>">
	<div class="container">
		<div class="call-to-action">
			<div class="row">
				<div class="col-md-8">
					<h1 class="title text-center">Consultation services</h1>
					<p class="text-center">Employees will gladly answer all your questions, will calculate the cost of services and prepare a commercial offer for free.</p>
				</div>
				<div class="col-md-4">
					<div class="text-center">
						<a href="#" class="btn btn-lg <?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["CALLBACK_BUTTON"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["CALLBACK_BUTTON"] : COption::GetOptionString("effortless", "QUICK_THEME_CALLBACK_BUTTON", "btn-default", SITE_ID))?>" data-toggle="modal" data-target=".FEEDBACK">Ask question</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>