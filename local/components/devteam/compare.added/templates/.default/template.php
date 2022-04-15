<?if($arParams['AJAX']!='Y'){
    ?>
    <script>
        $(function(){
        $('.b-compare__delete').live('click', function(){
            var id = $(this).data('id');
            $.ajax({
                url: '/api/?action=removeFromCompare_&id=' + id,
                success: function(data){
                    $('.b-compare-added').html(data);
                    if(!$('.b-compare-added-list__item').size()){ 
                        $('.b-compare-added').addClass('hidden_').removeClass('clearfix');
                    }
                }
            }); 
            $('.m-compare__added[data-id = ' + id + ']').removeClass('m-compare__added')
                                                        .find('span') 
                                                        .text('Cравнить');
        }); 
    });
    </script>

<div class="b-compare-added <?if(!count($arResult['ITEMS'])){?> hidden_<?} else {?> clearfix<?}?>">
    <?}?>
    
    
<?if(count($arResult['ITEMS'])){?>    
    <div class="b-compare-added__title">Товары в сравнении:</div>
    <div class="b-compare-added-list">
        
      <?foreach($arResult['ITEMS'] as $item){?>
            <div class="b-compare-added-list__item">
                    <div class="b-compare-added-list__link">
                        <a href="<?=$item["DETAIL_PAGE_URL"]?>"><?=$item["NAME"]?></a>
                           <button data-id="<?=$item["ID"]?>" class="b-compare__delete"></button>
     
                    </div>
            </div>
        <?}?>
   
    </div>
    <?if(count($arResult['ITEMS'])>1){?>
    <div class="b-compare-added__btn"><button class="b-button m-grey compare_">Сравнить</button></div>
    <?}?>
<?}?>    
    
<?if($arParams['AJAX']!='Y'){?>
</div>   
<?}?>