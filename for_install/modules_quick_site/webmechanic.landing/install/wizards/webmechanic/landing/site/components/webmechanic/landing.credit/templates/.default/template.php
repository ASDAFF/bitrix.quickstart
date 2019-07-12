<!--car-->
<?php

$module_id = "webmechanic.landing";
CModule::IncludeModule($module_id);

$APPLICATION->IncludeComponent("webmechanic:mobile.detect", ".default", array(), false);
$detect = new Mobile_Detect();
$is_mobile = $detect->isMobile();
 
if($is_mobile) {
    $numOnSlide = 2;
    $productClass = 'col-xs-6';
}
else {
    $numOnSlide = 6;
    $productClass = 'col-xs-2';
}

?>


<? if (isset($_POST['credit'])): ?>
    <? unset($_POST['credit']); ?>
    <script type="text/javascript">
        $(function(){
            $('#thanks-modal').modal();
        });
    </script>
<? endif; ?>

<div class="product row">
    <div class="col-sm-5">
        <h1 id="carname"></h1> 
        <a id="modhref" href="#" data-toggle="modal" data-target="#detail-modal" >
            <span></span> 
            <?=GetMessage('webmechanic_landing_detail');?>
        </a> 
        <p id="modeltext">
            <span></span>
        </p> 

        <div class="payment">
            <div id="summa">0</div>
        </div>

    </div>

    <div class="col-sm-7">
        <img id="carimage" class="img-responsive" /> 
    </div>
</div>
<!--end product-->

<?
    $cntitems = count($arResult);
    
    $slides = ceil($cntitems / $numOnSlide);

    $slidesArr = array();
    for ($i = 0; $i < $cntitems; $i++) {
       $slide = ceil(($i + 1) / $numOnSlide);
        $slidesArr[$slide-1][] = $arResult[$i];
    }

    $countSlides = count($slidesArr);
?>

<!--form-->
<div class="form">

    <form id="credit-form" action="<?= POST_FORM_ACTION_URI ?>" method="post" class="form-inline" role="form">

        <div class="row">
            <div class="form-group col-sm-12">
                <div id="carousel-product" class="carousel slide carousel-fade <? $countSlides > 1 ? 'nas-slides' : ''; ?>" data-ride="carousel">
                
                  <div class="row">
                      <div class="gift_text col-sm-8">
                          <?=COption::GetOptionString($module_id, "WEBMECHANIC_CREDIT_GIFT"); ?>
                      </div>
                      <div class="product_pages col-sm-4">

                          <!-- Indicators -->
                          <? if($countSlides > 1): ?>
                              <ol class="carousel-indicators pull-right">
                                <? for($i = 0; $i < $slides; $i++):?>
                                  <li data-target="#carousel-product" data-slide-to="<?=$i;?>" class="<?=$i == 0 ? 'active' : '' ?>">
                                    <?=($i+1);?>
                                  </li>
                                <? endfor ?>
                              </ol>
                          <? endif; ?>

                      </div>
                  </div>
                  

                  <!-- Wrapper for slides -->
                  <div class="carousel-inner" role="listbox">
                    <? for($i = 0; $i < count($slidesArr); $i++):?>
                      <div class="item <?=$i==0 ? 'active' : '' ?>">
                        <div class="row">
                          <? for($j = 0; $j < count($slidesArr[$i]); $j++):?>
                            <div class="product col-sm-2 <?=$productClass?>"
                                data-id="<?=$slidesArr[$i][$j]['ID']?>"
                                data-price="<?=$slidesArr[$i][$j]['PRICE']?>"
                                data-img="<?=$slidesArr[$i][$j]['DETAIL_PICTURE']?>"
                                data-descr="<?=$slidesArr[$i][$j]['DESC']?>"
                                data-model="<?=$slidesArr[$i][$j]['NAME']?>"
                                data-modif="<?=$slidesArr[$i][$j]['SECTION_NAME']?>"
                                data-id="<?=$slidesArr[$i][$j]['ID']?>"
                            >
                              
                              <div class="thumb">
                                <div class="thumb-img">
                                    <img src="<?=$slidesArr[$i][$j]['PREVIEW_PICTURE']?>" alt="Image" class="img-responsive">
                                </div>
                                <p><?= $slidesArr[$i][$j]['NAME'] ?></p>
                                <p><?= number_format($slidesArr[$i][$j]['PRICE'], 0, ',', ' ') ?> <?=GetMessage('webmechanic_landing_rub');?></p>
                                <div class="detail">
                                <? 
                                    $params = $slidesArr[$i][$j]['ALL_PARAMS'];
                                    $vals = ($slidesArr[$i][$j]['VAL_PARAMS']) ? $slidesArr[$i][$j]['VAL_PARAMS'] : array();
                                    $cntcompls = count($params); 
                                ?>
                                <table class="table table-bordered">
                                    <tr>
                                        <td class="key"></td>
                                        <? for ($k = 0; $k < $cntcompls; $k++): ?>
                                            <td><?= $params[$k]['NAME'] ?></td> 
                                        <? endfor ?>
                                    </tr>
                                    <tr>
                                        <td class="key"><span class="bold"><?=GetMessage('webmechanic_landing_price');?></span></td>
                               
                                        <? for ($k = 0; $k < $cntcompls; $k++): ?>
                                            <td><?= number_format($params[$k]['PROPERTY_PRICE_VALUE'], 0, ',', ' ') ?> <?=GetMessage('webmechanic_landing_rub');?></td>
                                        <? endfor ?>
                                    </tr>    

                                    <? foreach ($vals as $key => $value): ?>
                                        <tr>                            
                                            <td class="key"><?= $key; ?></td>
                                            <? for ($k = 0; $k < $cntcompls; $k++): ?>
                                                <td><?=$vals[$key][$params[$k]["ID"]] ?></td>
                                            <? endfor ?>
                                        </tr>
                                    <? endforeach; ?>
                                </table>  
                              </div>
                              </div>
                            </div>
                          <? endfor; ?>
                        </div>
                      </div>
                    <? endfor; ?>
                  </div>

                  <!-- Controls -->
                  <? if($countSlides > 1): ?>
                  <a class="left carousel-control" href="#carousel-product" role="button" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                    <span class="sr-only"><?=GetMessage('webmechanic_landing_prev');?></span>
                  </a>
                  <a class="right carousel-control" href="#carousel-product" role="button" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                    <span class="sr-only"><?=GetMessage('webmechanic_landing_next');?></span>
                  </a>
                  <? endif; ?>

                </div>
            </div>
        </div>

        <div class="row">

            <div class="form-group col-sm-5">
                <span class="label"><?=GetMessage('webmechanic_landing_fpay');?></span>
                <div class="ui credit-fpay">
                    <div class="uic">
                        <div id="credit-fpay">
                            <a href="#" class="ui-slider-handle ui-state-default ui-corner-all">
                                <span class="svalues" id="t2"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group col-sm-7">
                <div class="ui credit-month">
                    <span class="label"><?=GetMessage('webmechanic_landing_credmonth');?></span>
                    <div class="uic">
                        <div id="credit-month">
                            <a href="#" class="ui-slider-handle ui-state-default ui-corner-all">
                                <span class="svalues" id="t3"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        
        </div>

        <!--row-->
        <div class="row">

            <div class="form-group col-md-2 col-sm-2 col-xs-5">
                <label class="label" for="age"><?=GetMessage('webmechanic_landing_age');?></label>
                <select name="age" data-required class="form-control">
                    <option value=""><?=GetMessage('webmechanic_landing_select');?></option>
                    <?  
                    $min = COption::GetOptionString($module_id, "WEBMECHANIC_CREDIT_MIN_AGE");
                    $max = COption::GetOptionString($module_id, "WEBMECHANIC_CREDIT_MAX_AGE");

                    for ($i = $min; $i <= $max; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <? endfor ?>
                </select>
                <div class="err-msg">
                    <span><?=GetMessage('webmechanic_landing_notfill');?></span>
                </div>
            </div>

            <div class="form-group col-md-3 col-sm-3 col-xs-7">
                <label class="label" for="region"><?=GetMessage('webmechanic_landing_region');?></label>
                <select name="region" data-required class="form-control">
                    <option value=""><?=GetMessage('webmechanic_landing_select');?></option>
                    <?
                    $regions = unserialize(COption::GetOptionString($module_id, "WEBMECHANIC_CREDIT_REGION"));

                    for ($i = 0; $i < count($regions); $i++): ?>
                        <option value="<?= $regions[$i]; ?>"><?= $regions[$i]; ?></option>
                    <? endfor; ?>

                </select>
                <div class="err-msg">
                    <span><?=GetMessage('webmechanic_landing_notfill');?></span>
                </div>
            </div>

            <div class="form-group col-md-4 col-sm-4 col-xs-12">
                <label class="label" for="fio"><?=GetMessage('webmechanic_landing_fio');?></label>
                <input type="text" class="form-control" name="fio" value="<?= $params['fio'] ?>" data-required>
                <div class="err-msg">
                    <span><?=GetMessage('webmechanic_landing_notfill');?></span>
                </div>
            </div>

            <div class="form-group col-md-3 col-sm-3 col-xs-12">
                <label class="label" for="phone"><?=GetMessage('webmechanic_landing_phone');?></label>
                <input type="text" class="form-control phonemask" name="phone" value="<?= $params['phone'] ?>" data-required>
                <div class="err-msg">
                    <span><?=GetMessage('webmechanic_landing_notfill');?></span>
                </div>
            </div>

        </div>
        <!--end row-->

        <input type="hidden" name="price" />
        <input type="hidden" name="fpay" />
        <input type="hidden" name="month" />
        <input type="hidden" name="monthpay" />
        <input type="hidden" name="mark" />
        <input type="hidden" name="model" />
        <input type="hidden" name="mod" />
        <input type="hidden" name="compl" />
        <input type="hidden" name="carpos" />
        <input type="hidden" name="price_id" />
        <input type="hidden" name="model_id" />
        <input type="hidden" name="credit" value="yes" />

        <div class="send-row row">
            <div class="col-sm-12 text-center">
                <div class="btn-group text-center">
                    <button  type="submit" id="btn-send" class="btn"><?=GetMessage('webmechanic_landing_send');?></button>
                </div>
            </div>    
        </div>



        <!--terms-->
        <div class="terms row">
            <div class="col-sm-12 text-center">
                <table>
                    <tr>
                        <td class="agree">
                            <input name="terms" type="checkbox" value="yes" />
                        </td>
                        <td class="agree-text">
                            <p><?= COption::GetOptionString($module_id, "WEBMECHANIC_CREDIT_TERM") ?></p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <!--end terms-->

    </form>
</div>

<?
    $arFilter = Array("CODE" => 'credit_banner');
    if($arParams['BANNERS_SECTION']) {
        $arFilter['SECTION_ID'] = $arParams['BANNERS_SECTION'];
    }
    $APPLICATION->IncludeComponent("webmechanic:landing.banner", ".default", $arFilter, false);
?>

<div class="row">
  <div class="top_menu col-sm-12">
      <a href="#" rel="acts" data-toggle="modal" data-target="#special-modal" ><?=GetMessage('webmechanic_landing_present');?></a>
      <a href="#" rel="about" data-toggle="modal" data-target="#company-modal" ><?=GetMessage('webmechanic_landing_company');?></a>
  </div>
</div>

<div class="row">
    <div class="col-sm-12">
      <p class="copy">
          <span><?=COption::GetOptionString($module_id, "WEBMECHANIC_CREDIT_COPY");?></span>
          <?=COption::GetOptionString($module_id, "WEBMECHANIC_CREDIT_COMMENT");?>
      </p>
    </div>
</div>

<div id="special-modal" class="modal fade animated" tabindex="-1" role="dialog" aria-labelledby=" " aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-border">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true"></span>
            <span class="sr-only"><?=GetMessage('webmechanic_landing_close');?></span>
          </button>
          <h4 class="modal-title"><?=GetMessage('webmechanic_landing_present');?></h4>
        </div>
        <div class="modal-body">
          <?
              $APPLICATION->IncludeComponent("webmechanic:landing.actions", ".default", array(
                "CODE" => 'company_actions',
                'SECTION_ID' => $arParams['ACTIONS_SECTION'],
              ), false);
          ?>
        </div>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
