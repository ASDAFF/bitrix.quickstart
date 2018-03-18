$(document).ready(function(){
  $(".installer-block-cell-right .instal-btn-wrap").html("");
  start_convert(0,bitrix_sessid, 0);
  
  $(".auto_change").live("click",function(){
    $(".installer-block-cell-right .instal-btn-wrap").html("");
    start_convert(0,bitrix_sessid,code_error);
    code_error = 0;
    return false;
  });
  
  $(".try_change").live("click",function(){
    $(".installer-block-cell-right .instal-btn-wrap").html("");
    start_convert(0,bitrix_sessid,0);
    code_error = 0;
    return false;
  });

  $(".go2finish").live("click",function(){
    $(".installer-block-cell-right .instal-btn-wrap").html("");
    start_convert(3,bitrix_sessid,0);
    code_error = 0;
    return false;
  });
        
})
var code_error = 0;
function start_convert(step, break_point,code){  
  $.post("/bitrix/wizards/kriteris/win2utf/convert_utf8.php",{"step":step, "break_point":break_point, "bitrix_sessid":bitrix_sessid, "code_error":code},function(json){
    if(json.error==""){
      if(json.break_point!="")
        json.RES += "<br><small>Текущая позиция: " + json.break_point+"</small>";  
      $(".ajax_convert").html(json.RES).attr("style","");
      if(json.STEEP < 5){
        setTimeout('start_convert("'+json.STEEP+'", "'+json.break_point+'")', 1000);
      }else{
        go2finish();        
      }
    }else{
      $(".ajax_convert").html(json.error).css("color", "red");
      code_error = json.code;
      if(code_error == 4)
        $(".installer-block-cell-right .instal-btn-wrap").append('<input type="submit" class="wizard-next-button auto_change" name="StepNext" value="Изменить автоматически">');
      if(code_error == 5)
        $(".installer-block-cell-right .instal-btn-wrap").append('<input type="submit" class="wizard-next-button go2finish" name="StepNext" value="Пропустить шаг">');

      $(".installer-block-cell-right .instal-btn-wrap").append('&nbsp;&nbsp;&nbsp;<input type="submit" class="wizard-next-button try_change" name="StepNext" value="Повторить попытку">');
    }
  },"json");   
}          

function go2finish(){
  $(".installer-block-cell-right .instal-btn-wrap").html('<input type="hidden" class="wizard-next-button" name="StepNext" value="Y">');
  SubmitForm('next');
}