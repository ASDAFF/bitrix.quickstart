<? 
$module_id = "webmechanic.landing";
CModule::IncludeModule($module_id);

?>

<div class="row">
  <div class="col-sm-12">
    <?=COption::GetOptionString($module_id, "WEBMECHANIC_CREDIT_ACTION");?>
  </div>
</div>

<div id="carousel-actions-row">
  <div id="carousel-actions" class="carousel slide" data-ride="carousel">
    
    <!-- Wrapper for slides -->
    <div class="carousel-inner" role="listbox">
      <? for($i = 0; $i < sizeof($arResult); $i++): ?>
        <div class="item <?=$i == 0 ? 'active' : '' ?>" data-text="<?=$arResult[$i]['PREVIEW_TEXT'] ?>">
          <img src="<?=$arResult[$i]['PREVIEW_PICTURE'];?>" alt="<?=$arResult[$i]['NAME'] ?>" class="img-responsive">
          <div class="carousel-caption">
            <?= $v['NAME'] ?>
          </div>
        </div>
      <? endfor ?>
    </div>

    <!-- Indicators -->
    <ol class="carousel-indicators pull-right">
      <? for($i = 0; $i < sizeof($arResult); $i++): ?>
        <li data-target="#carousel-actions" data-slide-to="<?=$i;?>" class="<?=$i == 0 ? 'active' : '' ?>"></li>
      <? endfor ?>            
    </ol>

    <!-- Controls -->
    <a class="left carousel-control" href="#carousel-actions" role="button" data-slide="prev">
      <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
      <span class="sr-only">Следующая</span>
    </a>
    <a class="right carousel-control" href="#carousel-actions" role="button" data-slide="next">
      <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
      <span class="sr-only">Предыдущая</span>
    </a>
  </div>
</div>

<div class="row">
  <div id="actions-info" class="col-sm-12">
    <p></p>
  </div>
</div>

<script type="text/javascript">

  $(function(){

    var carousel = $('#carousel-actions');

    carousel.carousel({
      interval: 10000
    });

    carousel.on('slid.bs.carousel', function (e, slide) {
      $('#actions-info p').html(carousel.find('.active').data('text'));
    });

    $('#special-modal').on('shown.bs.modal', function(){
      carousel.carousel(0);  
    });

  });
</script>
