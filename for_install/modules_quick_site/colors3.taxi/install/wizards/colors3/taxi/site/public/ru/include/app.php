<script src="<?= SITE_TEMPLATE_PATH ?>/js/ru/top-panel.js" type="text/javascript"></script>
<?php if (isset($_COOKIE['top-panel-closed']) && $_COOKIE['top-panel-closed'] == 1): ?>
    <div id="top-panel">
    <?php else: ?>
        <div id="top-panel" class="opened">
        <?php endif; ?>
        <style type="text/css">
            #top-panel {margin-top: -76px}
            #top-panel.opened {margin-top: 0;}
            .close-app {display: none;}
            .opened .close-app {display:block; float: right;margin-top: 8px;}
            .content-app {margin: 20px 0 5px;}
            h4.head-app {font-family: 'PT Sans Narrow','Arial Narrow',Arial,Helvetica,FreeSans,"Liberation Sans","Nimbus Sans L",sans-serif; font-weight: bold; margin: 9px 0 0 0;line-height: 1.2;}
            .open-app {background: url('<?= SITE_TEMPLATE_PATH ?>/i/ar-app.png') no-repeat; width: 27px; height: 13px;display: block;position: absolute;margin-left: 45px;margin-top: 67px;z-index: 10000000;}
            .opened .open-app {display: none;}
            .open-app:hover {background-position: 0 -13px;} 
            @media (min-width: 981px) and (max-width: 1199px) {
                .open-app {margin-left: 36px;}
            }
            @media (min-width: 768px) and (max-width: 980px) {
                .container .app-icon {width: 372px}
                .container .block-close-app {margin-left: 0;}
                .open-app {margin-left: 18px;}
            }
            @media (max-width: 767px) {
                .container .span1.block-close-app {width: 20px;padding: 0;margin: 0 0 0 10px;position: absolute;top: 13px;right: 20px;}
                .content-app {margin: 10px 0 5px;}
                h4.head-app {padding-bottom: 10px; padding-right: 30px;}
                #top-panel {margin-top: -106px}
                #top-panel.opened {margin-top: 0;}
                .open-app {margin-left: -65px;margin-top: 3px;}
            }
            @media (max-width: 420px) {
                .content-app .app-icon a img {width: 28%;}
            }
        </style>
        <div class="container"> 
            <div class="row-fluid content-app"> 
                <div class="span5">
                    <h4 class="head-app">Скачайте наше приложение для&nbsp;заказа&nbsp;такси</h4>
                </div>
                <div class="span6 app-icon">
                    <a style="margin-right: 10px;" href="https://play.google.com/store/apps/details?id=com.colors.taxi" target="_blank" rel="nofollow"><img src="<?= SITE_TEMPLATE_PATH ?>/i/google.png" alt="Доступно в Google Play" style="margin-bottom: 15px;"></a>
                    <!--<a style="margin-right: 10px;" href="https://play.google.com/store/apps/details?id=com.colors.taxi" target="_blank" rel="nofollow"><img src="<?= SITE_TEMPLATE_PATH ?>/i/google.png" alt="Доступно в Google Play" style="margin-bottom: 15px;"></a>
                    <a href="https://play.google.com/store/apps/details?id=com.colors.taxi" target="_blank" rel="nofollow"><img src="<?= SITE_TEMPLATE_PATH ?>/i/google.png" alt="Доступно в Google Play" style="margin-bottom: 15px;"></a>-->
                </div>
                <div class="span1 block-close-app">
                    <a class="close-app" href="" style=""><img alt="Закрыть" src="<?= SITE_TEMPLATE_PATH ?>/i/close-app.png"></a>
                    <a class="open-app" href="" style=""></a>
                </div>
            </div>
        </div>
    </div>