<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<form><h2 class="b-sidebar-filter__title">Выбор по параметрам:</h2> 
<?foreach($arResult['PROPS'] as $prop_code => $property){?>
<div class="b-sidebar-filter-container">
        <div class="b-sidebar-filter-caption">
                <span class="b-sidebar-filter-caption__text"><?=$property["NAME"]?></span>
        </div> 
   <?switch ($property["PROPERTY_TYPE"]){
        case 'N': 
            
            switch ($property['CONFIG']) {
                case 1:
                case 2:
                case 3:
                    ?>
                    <div class="clearfix">
                        <div class="b-sidebar-filter__left">
                                <input type="text" value="<?=$property["VALUES_"]?>" class="b-text"  name="filter[<?=$property["CODE"]?>]">
                        </div>
                    </div>
                    <? 
                    break;
                case 4: 
                    if(!$property["VALUES"]['MIN'])
                        $property["VALUES"]['MIN'] = 1;
                    if(!$property["VALUES"]['MAX'])
                        $property["VALUES"]['MAX'] = 50;
                    if(!$property["VALUES"]['STEP'])
                        $property["VALUES"]['STEP'] = 1;
                    
                    if(!$property["VALUES_"]['MIN'])
                        $property["VALUES_"]['MIN'] = $property["VALUES"]['MIN'];
                    if(!$property["VALUES_"]['MAX'])
                        $property["VALUES_"]['MAX'] = $property["VALUES"]['MAX'];
                    
                    ?>
                    <script> 
                    $(function(){ 
                        var slider_min_<?=$prop_code;?> = <?=$property["VALUES"]['MIN'];?>, slider_max_<?=$prop_code;?> = <?=$property["VALUES"]['MAX'];?>;
                        $("#SLIDER_MIN_<?=$prop_code;?>").numOnly();
                        $("#SLIDER_MAX_<?=$prop_code;?>").numOnly();

                        $("#SLIDER_MIN_<?=$prop_code;?>").change(function() {
                            var value1_<?=$prop_code;?> = $("#SLIDER_MIN_<?=$prop_code;?>").val();
                            var value2_<?=$prop_code;?> = $("#SLIDER_MAX_<?=$prop_code;?>").val();

                            if(parseInt(value1_<?=$prop_code;?>) > parseInt(value2_<?=$prop_code;?>)) {
                                    value1_<?=$prop_code;?> = value2_<?=$prop_code;?>;
                                    $("#SLIDER_MIN_<?=$prop_code;?>").val(value1_<?=$prop_code;?>);
                            }
                            $("#b-slider_<?=$prop_code;?>").slider("values", 0, value1_<?=$prop_code;?>);	
                        });
 
                        $("#SLIDER_MAX_<?=$prop_code;?>").change(function() {
                            var value1_<?=$prop_code;?> = $("#SLIDER_MIN_<?=$prop_code;?>").val();
                            var value2_<?=$prop_code;?> = $("#SLIDER_MAX_<?=$prop_code;?>").val();

                            if(value2_<?=$prop_code;?> > slider_max_<?=$prop_code;?>) { 
                                    value2_<?=$prop_code;?> = slider_max_<?=$prop_code;?>; 
                                    $("#SLIDER_MAX_<?=$prop_code;?>").val(slider_max_<?=$prop_code;?>);
                            }

                            if(parseInt(value1_<?=$prop_code;?>) > parseInt(value2_<?=$prop_code;?>)) {
                                    value2_<?=$prop_code;?> = value1_<?=$prop_code;?>;
                                    $("#SLIDER_MAX_<?=$prop_code;?>").val(value2_<?=$prop_code;?>);
                            }
                            $("#b-slider_<?=$prop_code;?>").slider("values", 1, value2_<?=$prop_code;?>);
                        });

                        $("#b-slider_<?=$prop_code;?>").slider({
                            range: true,
                            min: slider_min_<?=$prop_code;?>,
                            max: slider_max_<?=$prop_code;?>,
                            step: <?=$property["VALUES"]['STEP'];?>,
                            values: [ <?=$property["VALUES_"]['MIN']?>, <?=$property["VALUES_"]['MAX']?> ],
                            create: function(event, ui) {
                                    $("#SLIDER_MIN_<?=$prop_code;?>").val($(this).slider("values", 0));
                                    $("#SLIDER_MAX_<?=$prop_code;?>").val($(this).slider("values", 1));
                            },
                            slide: function(event, ui) {
                                    $("#SLIDER_MIN_<?=$prop_code;?>").val(ui.values[0]);
                                    $("#SLIDER_MAX_<?=$prop_code;?>").val(ui.values[1]);
                            }
                        }); 
                    });
                    </script>    
                 <div class="clearfix"> 
                        <div class="b-sidebar-filter__left">
                           <input type="text" class="b-text" id="SLIDER_MIN_<?=$prop_code;?>"  name="filter[min_<?=$prop_code;?>]"/>
                        </div>
                        <span class="b-sidebar-filter__mdash">—</span>
                        <div class="b-sidebar-filter__right">
                           <input type="text" class="b-text" id="SLIDER_MAX_<?=$prop_code;?>" name="filter[max_<?=$prop_code;?>]" />
                        </div>
                        <div class="b-sidebar-filter-slider">
                           <div id="b-slider_<?=$prop_code;?>"></div>
                        </div>
                    </div>
                 <? break;  
            } 
            break;
        case 'L':  
            switch ($property['CONFIG']) {
                case 1: ?>
                    <table> 
                            <tbody>
                            <?foreach($property["VALUES"] as $value){?>    
                            <tr>
                               <td><label class="b-checkbox m-checkbox_gp_1"><input type="checkbox" <?if(in_array($value['ID'], $property["VALUES_"])){?> checked="checked" <?}?> value="<?=$value['ID']?>" name="filter[<?=$prop_code?>][]"><?=$value["VALUE"]?></label></td>
                            </tr>	
                            <? } ?>
                    </tbody>
                    </table> <?
                    break; 
                case 2:
                     ?>
                    <table> 
                         <tbody>
                            <?foreach($property["VALUES"] as $value){?>    
                            <tr>
                                <td><label class="b-radio m-checkbox_gp_1"><input type="radio" <?if(in_array($value['ID'], $property["VALUES_"])){?> checked="checked" <?}?> value="<?=$value['ID']?>" name="filter[<?=$prop_code?>]"><?=$value["VALUE"]?></label></td>
                            </tr>	
                         <? } ?>
                    </tbody>
                    </table><?
                case 3: ?>
                    <table> 
                     <tbody><tr> 
                      <td> 
                        <select name="filter[<?=$prop_code?>]" class="b-chosen__no-text">
                         <option value="false">Выберите</option> 
                         <?foreach($property["VALUES"] as $value){?>
                         <option <?if($value == $property["VALUES_"]){?> selected <?}?> value='<?=$value;?>'><?=$value;?></option>
                         <? } ?>
                       </select>
                     </td></tr>
                   </tbody>
                   </table> <? 
                    break;
                 case 4:  
                    if(!$property["VALUES"]['MIN'])
                        $property["VALUES"]['MIN'] = 1;
                    if(!$property["VALUES"]['MAX'])
                        $property["VALUES"]['MAX'] = 50;
                    if(!$property["VALUES"]['STEP'])
                        $property["VALUES"]['STEP'] = 1;
                    if(!$property["VALUES_"]['MIN'])
                        $property["VALUES_"]['MIN'] = $property["VALUES"]['MIN'];
                    if(!$property["VALUES_"]['MAX'])
                        $property["VALUES_"]['MAX'] = $property["VALUES"]['MAX'];
                    ?>
                    <script> 
                    $(function(){ 
                        var slider_min_<?=$prop_code;?> = <?=$property["VALUES"]['MIN'];?>, slider_max_<?=$prop_code;?> = <?=$property["VALUES"]['MAX'];?>;
                        $("#SLIDER_MIN_<?=$prop_code;?>").numOnly();
                        $("#SLIDER_MAX_<?=$prop_code;?>").numOnly();
                        $("#SLIDER_MIN_<?=$prop_code;?>").change(function() {
                            var value1_<?=$prop_code;?> = $("#SLIDER_MIN_<?=$prop_code;?>").val();
                            var value2_<?=$prop_code;?> = $("#SLIDER_MAX_<?=$prop_code;?>").val();

                            if(parseInt(value1_<?=$prop_code;?>) > parseInt(value2_<?=$prop_code;?>)) {
                                    value1_<?=$prop_code;?> = value2_<?=$prop_code;?>;
                                    $("#SLIDER_MIN_<?=$prop_code;?>").val(value1_<?=$prop_code;?>);
                            }
                            $("#b-slider_<?=$prop_code;?>").slider("values", 0, value1_<?=$prop_code;?>);	
                        }); 
                        $("#SLIDER_MAX_<?=$prop_code;?>").change(function() {
                            var value1_<?=$prop_code;?> = $("#SLIDER_MIN_<?=$prop_code;?>").val();
                            var value2_<?=$prop_code;?> = $("#SLIDER_MAX_<?=$prop_code;?>").val();
                            if(value2_<?=$prop_code;?> > slider_max_<?=$prop_code;?>) { 
                                    value2_<?=$prop_code;?> = slider_max_<?=$prop_code;?>; 
                                    $("#SLIDER_MAX_<?=$prop_code;?>").val(slider_max_<?=$prop_code;?>);
                            }
                            if(parseInt(value1_<?=$prop_code;?>) > parseInt(value2_<?=$prop_code;?>)) {
                                    value2_<?=$prop_code;?> = value1_<?=$prop_code;?>;
                                    $("#SLIDER_MAX_<?=$prop_code;?>").val(value2_<?=$prop_code;?>);
                            }
                            $("#b-slider_<?=$prop_code;?>").slider("values", 1, value2_<?=$prop_code;?>);
                        });
                        $("#b-slider_<?=$prop_code;?>").slider({
                            range: true,
                            min: slider_min_<?=$prop_code;?>,
                            max: slider_max_<?=$prop_code;?>,
                            step: <?=$property["VALUES"]['STEP'];?>,
                            values: [ <?=$property["VALUES_"]['MIN']?>, <?=$property["VALUES_"]['MAX']?> ],
                            create: function(event, ui) {
                                    $("#SLIDER_MIN_<?=$prop_code;?>").val($(this).slider("values", 0));
                                    $("#SLIDER_MAX_<?=$prop_code;?>").val($(this).slider("values", 1));
                            },
                            slide: function(event, ui) {
                                    $("#SLIDER_MIN_<?=$prop_code;?>").val(ui.values[0]);
                                    $("#SLIDER_MAX_<?=$prop_code;?>").val(ui.values[1]);
                            }
                        }); 
                    });
                    </script>    
                 <div class="clearfix"> 
                        <div class="b-sidebar-filter__left">
                           <input type="text" class="b-text" id="SLIDER_MIN_<?=$prop_code;?>"  name="filter[min_<?=$prop_code;?>]"/>
                        </div>
                        <span class="b-sidebar-filter__mdash">—</span>
                        <div class="b-sidebar-filter__right">
                           <input type="text" class="b-text" id="SLIDER_MAX_<?=$prop_code;?>" name="filter[max_<?=$prop_code;?>]" />
                        </div>
                        <div class="b-sidebar-filter-slider">
                           <div id="b-slider_<?=$prop_code;?>"></div>
                        </div>
                    </div>
                 <? break; 
            } 
        break;
        case 'S':  
            switch ($property['CONFIG']) {
                case 1: 
                    ?>
                   <table> 
                           <tbody> 
                           <?foreach($property["VALUES"] as $value){?>    
                           <tr> 
                              <td><label class="b-checkbox m-checkbox_gp_1"><input type="checkbox" <?if(in_array($value, $property["VALUES_"])){?> checked="checked" <?}?> value="<?=$value?>" name="filter[<?=$prop_code?>][]"><?=$value?></label></td> 
                           </tr>	
                           <? } ?>
                   </tbody>
                   </table> <?
                    break;   
                case 2:
                 ?> 
                   <table> 
                           <tbody> 
                           <?foreach($property["VALUES"] as $value){?>    
                           <tr> 
                              <td> 
                                 <label class="b-radio">
                                     <input type="radio" <?if($value == $property["VALUES_"]){?> checked="checked" <?}?> value="<?=$value?>" name="filter_<?=$prop_code?>"><?=$value?>
                                  </label>
                              </td> 
                           </tr>	
                           <? } ?> 
                   </tbody>
                   </table> <? 
                    break;     
                case 3: ?>
                    <table> 
                       <tbody><tr> 
                          <td> 
                            <select name="filter[<?=$prop_code?>]" class="b-chosen__no-text">
                             <option value="false">Выберите</option> 
                             <?foreach($property["VALUES"] as $value){?>
                             <option <?if($value == $property["VALUES_"]){?> selected <?}?> value='<?=$value;?>'><?=$value;?></option>
                             <? } ?>
                           </select>
                         </td></tr>
                   </tbody>
                   </table> <? 
                    break;
                 case 4:  
                    if(!$property["VALUES"]['MIN'])
                        $property["VALUES"]['MIN'] = 1;
                    if(!$property["VALUES"]['MAX'])
                        $property["VALUES"]['MAX'] = 50;
                    if(!$property["VALUES"]['STEP'])
                        $property["VALUES"]['STEP'] = 1;
                    
                    if(!$property["VALUES_"]['MIN'])
                        $property["VALUES_"]['MIN'] = $property["VALUES"]['MIN'];
                    if(!$property["VALUES_"]['MAX'])
                        $property["VALUES_"]['MAX'] = $property["VALUES"]['MAX'];
                    
                    ?>
                    <script> 
                    $(function(){ 
                        var slider_min_<?=$prop_code;?> = <?=$property["VALUES"]['MIN'];?>, slider_max_<?=$prop_code;?> = <?=$property["VALUES"]['MAX'];?>;
                        $("#SLIDER_MIN_<?=$prop_code;?>").numOnly();
                        $("#SLIDER_MAX_<?=$prop_code;?>").numOnly();

                        $("#SLIDER_MIN_<?=$prop_code;?>").change(function() {
                            var value1_<?=$prop_code;?> = $("#SLIDER_MIN_<?=$prop_code;?>").val();
                            var value2_<?=$prop_code;?> = $("#SLIDER_MAX_<?=$prop_code;?>").val();

                            if(parseInt(value1_<?=$prop_code;?>) > parseInt(value2_<?=$prop_code;?>)) {
                                    value1_<?=$prop_code;?> = value2_<?=$prop_code;?>;
                                    $("#SLIDER_MIN_<?=$prop_code;?>").val(value1_<?=$prop_code;?>);
                            }
                            $("#b-slider_<?=$prop_code;?>").slider("values", 0, value1_<?=$prop_code;?>);	
                        });
 
                        $("#SLIDER_MAX_<?=$prop_code;?>").change(function() {
                            var value1_<?=$prop_code;?> = $("#SLIDER_MIN_<?=$prop_code;?>").val();
                            var value2_<?=$prop_code;?> = $("#SLIDER_MAX_<?=$prop_code;?>").val();

                            if(value2_<?=$prop_code;?> > slider_max_<?=$prop_code;?>) { 
                                    value2_<?=$prop_code;?> = slider_max_<?=$prop_code;?>; 
                                    $("#SLIDER_MAX_<?=$prop_code;?>").val(slider_max_<?=$prop_code;?>);
                            }

                            if(parseInt(value1_<?=$prop_code;?>) > parseInt(value2_<?=$prop_code;?>)) {
                                    value2_<?=$prop_code;?> = value1_<?=$prop_code;?>;
                                    $("#SLIDER_MAX_<?=$prop_code;?>").val(value2_<?=$prop_code;?>);
                            }
                            $("#b-slider_<?=$prop_code;?>").slider("values", 1, value2_<?=$prop_code;?>);
                        });

                        $("#b-slider_<?=$prop_code;?>").slider({
                            range: true,
                            min: slider_min_<?=$prop_code;?>,
                            max: slider_max_<?=$prop_code;?>,
                            step: <?=$property["VALUES"]['STEP'];?>,
                            values: [ <?=$property["VALUES_"]['MIN']?>, <?=$property["VALUES_"]['MAX']?> ],
                            create: function(event, ui) {
                                    $("#SLIDER_MIN_<?=$prop_code;?>").val($(this).slider("values", 0));
                                    $("#SLIDER_MAX_<?=$prop_code;?>").val($(this).slider("values", 1));
                            },
                            slide: function(event, ui) {
                                    $("#SLIDER_MIN_<?=$prop_code;?>").val(ui.values[0]);
                                    $("#SLIDER_MAX_<?=$prop_code;?>").val(ui.values[1]);
                            }
                        }); 
                    });
                    </script>    
                 <div class="clearfix"> 
                        <div class="b-sidebar-filter__left">
                           <input type="text" class="b-text" id="SLIDER_MIN_<?=$prop_code;?>"  name="filter[min_<?=$prop_code;?>]"/>
                        </div>
                        <span class="b-sidebar-filter__mdash">—</span>
                        <div class="b-sidebar-filter__right">
                           <input type="text" class="b-text" id="SLIDER_MAX_<?=$prop_code;?>" name="filter[max_<?=$prop_code;?>]" />
                        </div>
                        <div class="b-sidebar-filter-slider">
                           <div id="b-slider_<?=$prop_code;?>"></div>
                        </div>
                    </div>
                 <? break; 
            } 
        break;
        case 'PRICE':   
      
            if(!$property["VALUES_"]['MAX_'])
                $property["VALUES_"]['MAX_'] = 100000;
            
            if(!$property["VALUES_"]['MIN'])
                $property["VALUES_"]['MIN'] = 0;
            if(!$property["VALUES_"]['MAX'])
                $property["VALUES_"]['MAX'] = $property["VALUES_"]['MAX_'];
             
            ?>
            <script>
            $(function(){ 
                var slider_min = 0, slider_max = <?=$property["VALUES_"]['MAX_']?>;

                $("#SLIDER_MIN").numOnly();
                $("#SLIDER_MAX").numOnly();

                $("#SLIDER_MIN").change(function() {
                        var value1 = $("#SLIDER_MIN").val();
                        var value2 = $("#SLIDER_MAX").val();

                        if(parseInt(value1) > parseInt(value2)) {
                                value1 = value2;
                                $("#SLIDER_MIN").val(value1);
                        }
                        $("#b-slider").slider("values", 0, value1);	
                });


                $("#SLIDER_MAX").change(function() {
                        var value1 = $("#SLIDER_MIN").val();
                        var value2 = $("#SLIDER_MAX").val();

                        if(value2 > slider_max) { 
                                value2 = slider_max; 
                                $("#SLIDER_MAX").val(slider_max);
                        }

                        if(parseInt(value1) > parseInt(value2)) {
                                value2 = value1;
                                $("#SLIDER_MAX").val(value2);
                        }
                        $("#b-slider").slider("values", 1, value2);
                });

                $("#b-slider").slider({
                        range: true,
                        min: slider_min,
                        max: slider_max,
                        step: 100,
                        values: [ <?=$property["VALUES_"]['MIN']?>, <?=$property["VALUES_"]['MAX']?> ],
                        create: function(event, ui) {
                                $("#SLIDER_MIN").val($(this).slider("values", 0));
                                $("#SLIDER_MAX").val($(this).slider("values", 1));
                        },
                        slide: function(event, ui) {
                                $("#SLIDER_MIN").val(ui.values[0]);
                                $("#SLIDER_MAX").val(ui.values[1]);
                        }
                }); 
            });
            </script>    
         <div class="clearfix"> 
                <div class="b-sidebar-filter__left">
                        <input type="text" class="b-text" id="SLIDER_MIN" name="filter[min_price]"/>
                </div>
                <span class="b-sidebar-filter__mdash">—</span>
                <div class="b-sidebar-filter__right">
                        <input type="text" class="b-text" id="SLIDER_MAX" name="filter[max_price]" />
                </div>
                <div class="b-sidebar-filter-slider">
                        <div id="b-slider"></div>
                </div>
        </div>
        <?break; } ?> 
</div>
<?}?>
<div><button class="b-button">Показать</button></div>
</form>