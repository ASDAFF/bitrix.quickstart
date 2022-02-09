<?php
$module_id = "remains"; 
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php"); 
 
CModule::IncludeModule($module_id); 
 
$TRANS_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($TRANS_RIGHT>="R"){

    if ($REQUEST_METHOD=="GET" && $TRANS_RIGHT=="W" && strlen($RestoreDefaults)>0)
    {
            COption::RemoveOption($module_id);
            $z = CGroup::GetList($v1="id",$v2="asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
            while($zr = $z->Fetch())
                    $APPLICATION->DelGroupRight($module_id, array($zr["ID"]));
    }

    CModule::IncludeModule('iblock');
    $arIBlocks=Array();
    $db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array());
    while($arRes = $db_iblock->Fetch())
            $arIBlocks[$arRes["ID"]] = $arRes["NAME"];

    $arAllOptions = Array(
        //Дополнительное время доставки
            array("DOP_TIME", 'Дополнительное время доставки', "", array("text","")),
            array("DAYS_CNT", "Количество дней до обнуления остатков", "", array('text', "")), 
            array("DIR", 'Путь для поиска файлов с прайс-листами', "", array("text","")),
            array("EMAIL", 'E-Mail для уведомлений', "", array("text","")) ,
            array("RAZDELITEL", 'Разделитель', "", array("text","")),
        
        
        
            );   
    $aTabs = array(   
            array("DIV" => "edit1", "TAB" => 'Сопоставление остатков', "ICON" => "translate_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
            array("DIV" => "edit2", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "translate_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
             array("DIV" => "edit3", "TAB" => 'Загрузка', "ICON" => "translate_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
          
        array("DIV" => "edit4", "TAB" => 'Доступ', "ICON" => "translate_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
    );

    $tabControl = new CAdminTabControl("tabControl", $aTabs);

    if(($REQUEST_METHOD=="POST") && (strlen($Update.$Apply.$RestoreDefaults)>0) && ($TRANS_RIGHT=="W") && check_bitrix_sessid())
    {

            if($_FILES["f"]){
                $uploaddir = $_SERVER["DOCUMENT_ROOT"] . '/upload/tmp/';
                $uploadfile = $uploaddir . basename($_FILES['f']['name']);
             
                    $f = file_get_contents($_FILES['f']['tmp_name']);
                  //   $f = iconv('cp1251', 'utf-8',  $f);
                    file_put_contents($uploadfile, $f);
      
                    $remainUpdater = new remainUpdater(); 
                    $arr = $remainUpdater->file2arr($uploadfile);  
                    
                  $remainUpdater->Update($arr);  
           
                unlink($uploadfile); 
                LocalRedirect('/bitrix/admin/remainslog.php?by=ID&order=desc&'); 
            }
            

            if(strlen($RestoreDefaults)>0)
            {
                    COption::RemoveOption($module_id);
                    $z = CGroup::GetList($v1="id",$v2="asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
                    while($zr = $z->Fetch())
                            $APPLICATION->DelGroupRight($module_id, array($zr["ID"]));
            }
            else
            {
                    foreach($arAllOptions as $option)
                    {
                            if(!is_array($option))
                                    continue;

                            $name = $option[0];
                            $val = ${$name};
                            if($option[3][0] == "checkbox" && $val != "Y")
                                    $val = "N";
                            if($option[3][0] == "multiselectbox")
                                    $val = @implode(",", $val);

                            COption::SetOptionString($module_id, $name, $val, $option[1]);
                    }
            }

            $Update = $Update.$Apply;
            ob_start();
            require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
            ob_end_clean();

            if(strlen($_REQUEST["back_url_settings"]) > 0)
            {
                    if((strlen($Apply) > 0) || (strlen($RestoreDefaults) > 0))
                            LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
                    else
                            LocalRedirect($_REQUEST["back_url_settings"]);
            }
            else
            {
                    LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&".$tabControl->ActiveTabParam());
            }
    }

    ?>
    <?
    $tabControl->Begin();
    ?><form method="POST"  enctype="multipart/form-data"  action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&amp;lang=<?=LANGUAGE_ID?>"><?

    $tabControl->BeginNextTab();?>
             
             <tr>
            
               <td width="50%" valign="top" style="border-right: 10px double White;" > 
                  <input type="text" id="searchField" value="" style="width: 203px; margin-bottom: 4px; margin-right: 7px;"><button id="searchBtn">Фильтровать</button>
   
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="/js/libs/jquery-1.8.2.min.js"><\/script>')</script>
<style>
    #search_result table tr td{
        text-align: left;
    }
</style>    
<script>

$(function(){
    
   $('#searchBtn').live('click', function(){
      
       var val = $('#searchField').val();
       
       $.ajax({
           url: '/bitrix/admin/adminsearch.php',
           data: {
               'name': val
           },
           success: function(data){
               $('#search_result').html(data);
           }
       });
       return false;
   });
        
        $('#searchTable2 .str').live('click', function(){
          p = $(this).parent().attr('data-id'); 
          $('#v2_' + p + ' [type=checkbox]').click(); 
        });
        
        
        $('#search_result table .str').live('click', function(){
               p = $(this).parent().parent().attr('data-id'); 
          $('#v1_' + p + ' [type=radio]').click(); 
            
        });
        
   $('#do').live('click', function(){
       
       if(!$('[name = item]:checked').size()
         ||  
         !$('[name = match]:checked').size()){
        return false; 
     }
       
       vals=[]
        $('[name = match]:checked').each(function() {
            vals.push(this.value);
             $('#v2_' + this.value).hide('slow').remove(); 
         
        });
    
       var v2 = $('[name = item]:checked').val();   
 
        
       $.ajax({ 
           url: '/bitrix/admin/adminsearch.php',
           data: {
               'action': 'compare',
               'v1': vals,
               'v2': v2 
           },
           success: function(data){
               
           }
       });
        return false; 
   });
    
   $('.deleteBtn').live('click', function(){
      var i = $(this).data('id');
      $.ajax({
           url: '/bitrix/admin/deleteremain.php',
           data: { 'id': i},
           success: function(data){ }
           }); 
      $('#v2_' + i).remove(); 
      return false; 
   });


   $('#filter1, #filter2').live('click', function(){
             
        var st = $(this).attr('data-status');
        
        $('.sortbtn').attr('data-status','');
        
        if(st == 'up'){
                $(this).attr('data-status','down');  
               zn  = ' &darr;'; 
        } 
        else{
              $(this).attr('data-status','up');
              zn  = ' &uarr;';
        }
       
       $('#filter1').text('По новизне ');
        $('#filter2').text('По названию ');
           
            $(this).html($(this).html() + zn);
 
           
        return false;
   });



  $('#searchBtn').click();

   $('#f2, #filter1, #filter2').live('click', function(){
 
     $('#f2, #filter1, #filter2').attr('disabled','disabled');
  
    if($('#filter1').attr('data-status') == 'up') {
          by = 'ID';
          sort = 'ASC';
      } else
          if($('#filter1').attr('data-status') == 'down'){
             by = 'ID';
          sort = 'DESC';
      }
  
    if($('#filter2').attr('data-status') == 'up') {
          by = 'NAME';
          sort = 'ASC';
      } else
          if($('#filter2').attr('data-status') == 'down'){
             by = 'NAME';
          sort = 'DESC';
      }
  
  
  
      $.ajax({
           url: '/bitrix/admin/matchingsearch.php',
           data: {    
              'sort': sort,
              'by': by,
              'text': $('#searchField2').val()
           },
           success: function(data){
                   
                   $('#searchTable2').html(data);
                       $('#f2, #filter1, #filter2').removeAttr('disabled'); 
          
                }
           }); 


      return false;
   });
   
   
});
</script>
 <div id="search_result" style="height: 580px; overflow: auto;  padding-right: 23px;">
</div> </td>



          <td width="50%" valign="top">  
                      <input type="text" id="searchField2"><button id="f2">Фильтровать</button>  
                    <button class="sortbtn" id="filter1" data-status="up">По новизне &uarr;</button>
                   <button class="sortbtn" id="filter2" data-status="">По названию</button>
                   <table id="searchTable2"> 
                   </table>  
               </td>
               
          

           </tr> 
           <tr>
               <td align="center" colspan="2"> 
                <button id="do">Сопоставить</button> 
               </td>
           </td>    
         
    <?
    $tabControl->BeginNextTab();
    __AdmSettingsDrawList($module_id, $arAllOptions);
    $tabControl->BeginNextTab();
    ?>
    <input name="f" type="file">
    <?
        $tabControl->BeginNextTab();
         
           require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
    <?$tabControl->Buttons();?>
            <input <?if ($TRANS_RIGHT<"W") echo "disabled" ?> type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
            <input <?if ($TRANS_RIGHT<"W") echo "disabled" ?> type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
            <?if(strlen($_REQUEST["back_url_settings"])>0):?>
                    <input <?if ($TRANS_RIGHT<"W") echo "disabled" ?> type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?echo htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
                    <input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
            <?endif?>
            <input <?if ($TRANS_RIGHT<"W") echo "disabled" ?> type="submit" name="RestoreDefaults" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
            <?=bitrix_sessid_post();?>
    <?$tabControl->End();?>
    </form>
<?}