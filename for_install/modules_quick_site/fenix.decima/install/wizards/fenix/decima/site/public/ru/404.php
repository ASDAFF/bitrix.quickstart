<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');

CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Страница не найдена");

?>       <div class="full-width four-o-four">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <h1 class="strong-header">
                            Ошибка 404:<br>
                            Страница не найдена
                        </h1>
                        <p>Запрашиваемая страница удалена, перемещена или временно недоступна.</p>
                        <a href="<?=SITE_DIR?>" class="btn btn-primary">Вернуться на главную</a>
                    </div>
                </div>
            </div>
        </div><?

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>