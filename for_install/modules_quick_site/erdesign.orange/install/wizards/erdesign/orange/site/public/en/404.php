<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');

CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("404 ошибка");?>


	
 
 <section id="content-header">
	    	<div class="container">
	    		<div class="row-fluid">
	    			<div class="span12">
				    	<hgroup class="content-title page">
				    		<h1>404 ошибка</h1>
				    		<h2>Страница не найдена</h2>
				    	</hgroup>
	    			</div>
	    		</div>
	    	</div>
	    </section><!-- /content-header -->	    

	    
 
 
    
				    


<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>