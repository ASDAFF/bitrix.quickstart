<?
    $module_id = "webmechanic.landing";
    CModule::IncludeModule($module_id);
    IncludeTemplateLangFile(__FILE__);
?>

      
    </div><!-- end .container -->

    

    <div id="company-modal" class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-border">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true"></span>
                <span class="sr-only"><?=GetMessage('webmechanic_landing_close')?></span>
              </button>
              <h4 class="modal-title"><?=GetMessage('webmechanic_landing_comp')?></h4>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-sm-12">
                  <?=COption::GetOptionString($module_id, "WEBMECHANIC_CREDIT_ABOUT");?>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <script type="text/javascript" charset="utf-8" src="//api-maps.yandex.ru/services/constructor/1.0/js/?sid=gqQ0Tl85XBWQ-1R1m8RgcyGqtZCS2OId&width=auto&height=300"></script>
                </div>
                <!--  -->
              </div>
            </div>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div id="thanks-modal" class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-border">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true"></span>
                <span class="sr-only"><?=GetMessage('webmechanic_landing_close')?></span>
              </button>
            </div>
            <div class="modal-body text-center">
              <div class="row">
                <div class="col-sm-12">
                  <?=COption::GetOptionString($module_id, "WEBMECHANIC_CREDIT_THANKS_MAIN");?>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <img src="<?php echo SITE_TEMPLATE_PATH ?>/images/thanks.png">
                </div>
              </div>
            </div>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div id="detail-modal" class="modal fade">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-border">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true"></span>
                <span class="sr-only"><?=GetMessage('webmechanic_landing_close')?></span>
              </button>
            </div>
            <div class="modal-body">
            </div>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

</body>
</html>