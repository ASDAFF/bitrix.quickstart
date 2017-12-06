<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php"); ?>
<? IncludeTemplateLangFile(__FILE__); ?>
<!DOCTYPE html>
<html>
<head>
	<title>Заказ успешно оплачен</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0" />
	<? $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/reset.css"); ?>
	<? $APPLICATION->AddHeadString('<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,700,300&amp;subset=latin,latin-ext,cyrillic" />',true); ?>
	<? $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/template_styles.css"); ?>
	<? $APPLICATION->ShowHead(); ?>
	<style>
		#promo {
			height: 100vh;
			background: url('/bitrix/templates/sportprognoz/img/fail-success-pages-bg.jpg') center no-repeat;
			background-size: cover;
			-webkit-align-items: center;
			-ms-flex-align: center;
			align-items: center;
		}

		#promo .wrapper {
			width: 100%;
			height: auto;
			background-color: rgba(0,0,0,0.75);
		}

		#promo .row {
			text-align: left;
		}

		.promo-block {
			color: #fff;
			text-align: center;
			padding: 3rem 0;
		}

		.promo-block h2 {
			padding-bottom: 0;
			text-align: center;
		}

		.promo-block .button {
			margin-top: 2rem;
			text-decoration: none;
			display: inline-block;
			line-height: 2.5rem;
		}

		/*====================================================================================================================*/
		/*====================================================================================================================*/
		/*====================================================================================================================*/

		@media (min-width: 48rem) {

		}

		@media (min-width: 62rem) {

		}

		@media (min-width: 75rem) {

		}

		@media (min-width: 88rem) {



		}
	</style>
</head>
<body>
	<? $APPLICATION->ShowPanel(); ?>
	<?

	function get_parameters($string) {
		// Получаем массив параметров
		$parameters = unserialize(base64_decode($string));
		return $parameters;
	}

	$options = get_parameters($_POST["options"]);

	?>
	<section class="fluid flex" id="promo">
		<div class="wrapper">
			<div class="fixed">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 promo-block">
						<h2>Заказ №<?=$_POST['InvId'];?> успешно оплачен</h2> 
						<a class="button some-link" href="/">На главную</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); IncludeTemplateLangFile(__FILE__); ?>
</body>
</html>