<?global $APPLICATION?>
<section class="section-begin-work mt-0">
	<div class="b-begin-work-collection b-begin-work-collection_pattern">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-12 col-lg-6 col-xl-5">
					<div class="b-begin-work-collection__content">
						<h4 class="b-begin-work-collection__title h1 mt-0">Работайте&nbsp;<br class="d-none d-lg-block">с нами</h4>
						<p class="b-begin-work-collection__text"> <?$APPLICATION->IncludeFile(
								SITE_DIR . 'includes/brand/text1.php',
								array(),
								array(
									'MODE' => 'php',
								)
							);?></p>
						<p class="b-begin-work-collection__text"><?$APPLICATION->IncludeFile(
								SITE_DIR . 'includes/brand/text2.php',
								array(),
								array(
									'MODE' => 'php',
								)
							);?></p>
					</div>
				</div>
				<div class="col-12 col-lg-6 col-xl-7">
					<div class="begin-work">
						<div class="begin-work__wrap begin-work__wrap_home">
							<div class="begin-work__bg"></div>
							<div class="begin-work__container">
								<div class="begin-work__content">
									<div class="h4 begin-work__title">Начало работы</div>
									<p class="begin-work__text"><?$APPLICATION->IncludeFile(
											SITE_DIR . 'includes/brand/text3.php',
											array(),
											array(
												'MODE' => 'php',
											)
										);?></p>
									<a class="begin-work__link" href="/partners/#form-partners">Стать партнером</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>