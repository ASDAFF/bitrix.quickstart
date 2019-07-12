<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');
CHTTP::SetStatus('404 Not Found');
@define('ERROR_404','Y');
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle('');?>

<div class="row erorpage">
	<div class="col col-md-12 text-center">
		<div class="errorpagein">
			<div class="aprimary errorcode robotolight">404</div>
			<div class="errortext robotolight">page not found</div>
			<div class="errorbutton"><a class="btn btn-primary" href="#SITE_DIR#">Home page</a></div>
		</div>
	</div>
</div>

<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>