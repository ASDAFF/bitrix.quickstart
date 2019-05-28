<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>

<div class="phone-menu">
	<div class="wrapper">
		<div class="menu">
			<?
				if($arResult['logged']){
					?>
						<a href="/personal/" class="person">
							<?=$arResult['name']?>
						</a>
					<?
				}
				else{
					?>
						<a href="/personal/auth/" class="person gray" data-hayhop="#auth_holder" data-title="Войти">
							Войти
						</a>
					<?
				}
			?>
		</div>
		<ul class="links js-links">
			<?
				if($arResult['logged']){
					?>
						<li class="exit">
							<a href="#" class="js-unlog-button">
								Выход
							</a>
						</li>
					<?
				}
			?>
		</ul>
		<div class="location">
			<div class="name">
				Москва
			</div>
		</div>
	</div>
</div>