<?
$ajax = ($_SERVER['HTTPS']=="on")?"https://":"http://";
$ajax .= $_SERVER['HTTP_HOST'].$componentPath.'/ajax.php';
?>

<script type="text/javascript">
   $(function(){
      var $form = $('#ajax-form');
      $form.on('submit', function(){

         // данные формы
         var msg   = $form.serialize();

         // добавляем кастомные данные
         var params = {};
         // рейтинг
         if($('.rating-stars_active').length != 0)
         {
            params[$('.rating-stars_active').data('name')] = $('.rating-stars_active').attr('data-rate');
         }

         // пока не используется. Планируется перевести все ajax на один исполняющий файл core.php
         params['type'] = "callback";

         editional_data = $.param(params);
         msg = msg + "&" + editional_data;

         $.ajax({
            type: 'POST',
            url: "<?=$ajax?>",
            data: msg,
            dataType: 'json',
            success: function(data) {
               console.log(data);
               if($.type(data.errors) !=='undefined')
               {
                  $('.alert-success').hide();
                  $('.alert-error').html(data.errors).show().delay(2000).animate({height: 'toggle'}, 500);
               }
               else
               {
                  $('.alert-error').hide();
                  $('.alert-success').html(data.success).show().delay(2000).animate({height: 'toggle'}, 500);
                  $form.trigger('reset');
               }
            },
            error:  function(error,status){
               $('.alert-error').show().delay(2000).animate({height: 'toggle'}, 500);
            }
         });

         return false;
      });
   });
</script>