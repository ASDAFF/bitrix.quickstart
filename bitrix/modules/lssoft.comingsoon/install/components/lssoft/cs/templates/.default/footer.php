<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
					
					<div class="shadow"></div>
                </div>

				<?php if (isset($arParams['TIMER']) and isset($arParams['TIMER_DATE']) and $arParams['TIMER_DATE'] and $arParams['TIMER']=='Y') { 
				$sDateTime=strtotime($arParams['TIMER_DATE']);
				?>
				<script type="text/javascript">
					jQuery(function () {
						var needDay = new Date(<?php echo date('Y',$sDateTime); ?>,<?php echo (date('m',$sDateTime)-1); ?>,<?php echo date('d',$sDateTime); ?>);
						$('#timer').countdown({until: needDay,format: 'dHM', layout: $('#timer').html()});
					});
				</script>
                <div class="timer" id="timer">
                    <ul>
                        <li class="title"><?php echo GetMessage("LS_CS_TIMER"); ?></li>
                        <li class="days">
                            <div class="count">{dn}</div>
                            <div class="name"><?php echo GetMessage("LS_CS_TIMER_DAY"); ?></div>
                        </li>
                        <li class="hours">
                            <div class="count">{hn}</div>
                            <div class="name"><?php echo GetMessage("LS_CS_TIMER_HOUR"); ?></div>
                        </li>
                        <li class="none">
                            <div class="count">:</div>
                            <div class="name">&nbsp;</div>
                        </li>
                        <li class="minutes">
                            <div class="count">{mn}</div>
                            <div class="name"><?php echo GetMessage("LS_CS_TIMER_MINITS"); ?></div>
                        </li>
                    </ul>
                </div>
                <?php } ?>
                
				<?php if (isset($arParams['MAIL']) and $arParams['MAIL']) { ?>
					<div class="feedback"><a href="mailto:<?php echo($arParams['MAIL']); ?>"><i></i><?php echo GetMessage("LS_CS_FEEDBACK"); ?></a></div>
				<?php } ?>
            </div>
        </div>

        <div class="hFooter"></div>
    </div>

    <div id="footer">
        <div class="inner">
            <div class="right">
                <a href="http://xeoart.com" class="xeoart"><i></i>Design by <span>xeoart</span></a>
            </div>

            <ul>
                <li class="auth"><a href="/bitrix/"><i></i><span><?php echo GetMessage("LS_CS_LOGIN_PAGE"); ?></span></a></li>
            </ul>
        </div>
    </div>

</body>

</html>