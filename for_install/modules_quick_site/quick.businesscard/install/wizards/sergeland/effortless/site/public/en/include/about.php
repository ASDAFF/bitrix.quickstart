<div class="section <?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["ABOUT_BG"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["ABOUT_BG"] : COption::GetOptionString("effortless", "QUICK_THEME_ABOUT_BG", "white-bg", SITE_ID))?> clearfix">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h1 class="text-center">About the company</h1>
				<div class="separator"></div>
				<p class="lead text-center mb-40">Lorem ipsum dolor sit amet laudantium molestias similique.<br> Quisquam incidunt ut laboriosam.</p>
				<div class="row">
					<div class="col-md-6">
						<div class="row">
							<div class="col-md-6">
								<img src="#SITE_DIR#images/content.jpg" alt="Lorem ipsum dolor sit"><br>
							</div>
							<div class="col-md-6">
								<p>Quo soluta provident, quod reiciendis. Dolores nam totam aut illum ex ratione harum molestias maxime minima tempore, possimus, laudantium. Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
							</div>
						</div>
						<p>Esse sequi veniam, assumenda voluptate necessitatibus ipsa dicta vero, minima natus cum cupiditate magnam et placeat quo adipisci.</p>
						<p>Ut wisi enim ad minim veniam, quis nostrud exerci taion ullamcorper suscipit lobortis nisl ut aliquip ex en commodo consequat. Duis te feugifacilisi per suscipit lobortis nisl ut aliquip ex en commodo consequat.Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diem nonummy nibh euismod tincidunt ut lacreet dolore magna aliguam erat volutpat.</p>
						<a href="#SITE_DIR#about/" class="btn btn-white">Read more</a>
						<div class="space hidden-md hidden-lg"></div>
					</div>
					<div class="col-md-6">
						<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array(
								"AREA_FILE_SHOW" => "file",
								"PATH" => "#SITE_DIR#include/".(!empty($_SESSION["QUICK_THEME"][SITE_ID]["ABOUT_VER"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["ABOUT_VER"] : COption::GetOptionString("effortless", "QUICK_THEME_ABOUT_VER", "about-news", SITE_ID)).".php"
							),
							false,
							array("ACTIVE_COMPONENT" => "Y")
						);?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>