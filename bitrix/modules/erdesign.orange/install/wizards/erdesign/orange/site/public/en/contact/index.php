<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "Ключевые слова");
$APPLICATION->SetPageProperty("description", "Описание страницы");
$APPLICATION->SetTitle("Контакты");
?>
    
   </section><!-- /slider -->
		<section id="content-header">
	    	<div class="container">
	    		<div class="row-fluid">
	    			<div class="span12">
				    	<hgroup class="content-title contact">
				    		<h1><?$APPLICATION->ShowTitle()?></h1>
				    		<h2><?=$APPLICATION->GetProperty("description");?></h2>
				    	</hgroup>
	    			</div>
	    		</div>
	    	</div>
	    </section><!-- /content-header -->	
	    <section id="content-container" class="container">

	    	<div class="row-fluid">


			    <section id="content" class="span12 blog posts">

				    <article class="post single">
				    	<div class="post-offset">
				    	
				    	<div class="row-fluid">
				    		<div class="span5">
				    			<h2>Напишите нам</h2>
				    			
				    			                               
								 <?$APPLICATION->IncludeComponent("bitrix:main.feedback", "feedback", Array(
									"USE_CAPTCHA" => "N",	// Использовать защиту от автоматических сообщений (CAPTCHA) для неавторизованных пользователей
									"OK_TEXT" => "Спасибо, ваше сообщение принято.",	// Сообщение, выводимое пользователю после отправки
									"EMAIL_TO" => "admin@mail.ru",	// E-mail, на который будет отправлено письмо
									"REQUIRED_FIELDS" => array(	// Обязательные поля для заполнения
										0 => "NONE",
									),
									"EVENT_MESSAGE_ID" => array(	// Почтовые шаблоны для отправки письма
										0 => "7",
									)
									),
									false
								);?>
				    		</div>
				    		<div class="span7">
					    		<h2>Название компании</h2>
						    	<p>Рыбным текстом называется текст,
						    	 служащий для временного наполнения макета в публикациях или производстве веб-сайтов.
						    	 </p>
						    	<div class="vcard">
									<span class="fn"><?echo "#SITE_NAME#";?></span><br>
									<div class="adr">
										<span class="street-address"><?echo "#SITE_HOME#";?></span>
										<span class="extended-address"><?echo "#SITE_OFIC#";?></span><br>
										<span class="locality"><?echo "#SITE_COUNTRY#";?></span>,
										<abbr class="region"><?echo "#SITE_SITY#";?></abbr>
										<span class="postal-code"><?echo "#SITE_INDEX#";?></span>
									</div>
									<div class="tel"><?echo "#SITE_TELEFON#";?></div>
									<div><a href="mailto:<?echo "#SITE_EMAIL#";?>" class="email"><?echo "#SITE_EMAIL#";?></a></div>
								</div>
				    			<div id="map_canvas"></div>
				    			
											<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
											<script type="text/javascript">
												var myOptions = {
													zoom: 16,
													center: new google.maps.LatLng(59.93498036638156,30.370874681129283),
													mapTypeId: google.maps.MapTypeId.ROADMAP
												}
												var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
												var myLatLng = new google.maps.LatLng(59.93498036638156,30.370874681129283);
												var beachMarker = new google.maps.Marker({
													position: myLatLng,
													map: map,
													icon: image
												});
											</script>
				    		</div>
				    	</div>
				    	
				    	<div class="post-options">
				    		<ul class="social">
				    			<li><a href="#" class="share">Share</a></li>
				    			<li><a href="#" class="facebook-share">Share on Facebook</a></li>
				    			<li><a href="#" class="twitter-share">Share on Twitter</a></li>
				    		</ul>
				    	</div>
				    	</div>
				    </article><!-- /post -->
				    
			    </section><!-- /content -->
			    
	    	</div>
	    </section><!-- content-container -->
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>