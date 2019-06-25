<? $GLOBALS['_841550575_']=Array('is_' .'arra' .'y','st' .'rv' .'al','intv' .'a' .'l','preg' .'_' .'match','is_array','array_k' .'ey' .'_' .'ex' .'is' .'ts','reset','key','i' .'s_arra' .'y','explode','' .'is_ar' .'ray','i' .'s_a' .'rray','' .'is_a' .'r' .'r' .'ay','' .'is_arra' .'y','implod' .'e','implo' .'de','' .'implode','i' .'s_a' .'rr' .'a' .'y'); ?><? function _759639143($i){$a=Array('TYPE',"STRING",'VALUE','VALUE','TYPE',"NUMBER",'VALUE','VALUE','TYPE',"CHECKBOX",'VALUE','VALUE','Y','Y','N','TYPE',"COLOR",'/^\\#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/','VALUE','DEFAULT','VALUE','DEFAULT','VALUE',"#000000",'TYPE',"LIST",'VALUES','VALUE','VALUES','VALUES','VALUE','VALUES','VALUE',"",'VALUE',"#CONTENT#",'KEY','KEY','PARAMETERS','KEY','KEY','KEY','PROPERTIES','CONTROL',"",'PROPERTIES','PROPERTIES','id','name','TYPE',"STRING",'TYPE',"NUMBER",'CONTROL','<input type="text"
                            id="',' ','',' />','TYPE',"CHECKBOX",'class','CONTROL','<input type="hidden" value="N" name="','','"
                            value="Y" ','VALUE','Y',' checked="checked"','',' ',' ','',' />','<label class="adm-checkbox adm-designed-checkbox-label" for="','TYPE',"COLOR",'CONTROL','<input type="text" id="','TYPE',"LIST",'CONTROL','<select
                            id="',' ','',' />','VALUES','VALUES','CONTROL','<option value="','"','VALUE',' selected="selected"','','>','CONTROL','</select>','CONTROL','CONTROL');return $a[$i];} ?><? class CStartShopToolsAdmin{public static function SaveParameters($_0,$_1,$_2=null){if(!$GLOBALS['_841550575_'][0]($_0)||!($_1 instanceof Closure))return $_0;foreach($_0 as $_3 =>&$_4){if($_4[_759639143(0)]== _759639143(1)){$_4[_759639143(2)]=$GLOBALS['_841550575_'][1]($_4[_759639143(3)]);}else if($_4[_759639143(4)]== _759639143(5)){$_4[_759639143(6)]=$GLOBALS['_841550575_'][2]($_4[_759639143(7)]);}else if($_4[_759639143(8)]== _759639143(9)){$_4[_759639143(10)]=$_4[_759639143(11)]== _759639143(12)?_759639143(13):_759639143(14);}else if($_4[_759639143(15)]== _759639143(16)){if(!$GLOBALS['_841550575_'][3](_759639143(17),$_4[_759639143(18)]))if(!empty($_4[_759639143(19)])){$_4[_759639143(20)]=$_4[_759639143(21)];}else{$_4[_759639143(22)]=_759639143(23);}}else if($_4[_759639143(24)]== _759639143(25)){if($GLOBALS['_841550575_'][4]($_4[_759639143(26)])){if(!$GLOBALS['_841550575_'][5]($_4[_759639143(27)],$_4[_759639143(28)])){$GLOBALS['_841550575_'][6]($_4[_759639143(29)]);$_4[_759639143(30)]=$GLOBALS['_841550575_'][7]($_4[_759639143(31)]);}}else{$_4[_759639143(32)]=_759639143(33);}}else if($_2 instanceof Closure){$_4[_759639143(34)]=$_2($_3,$_4);}$_1($_3,$_4);}return $_0;}public static function DrawSections($_5,$_6=null,$_7=null,$_8=null){if(!$GLOBALS['_841550575_'][8]($_5))return;$_9=$GLOBALS['_841550575_'][9](_759639143(35),$_6);foreach($_5 as $_3 => $_10){$_11=$_10;if(empty($_11[_759639143(36)])){$_11[_759639143(37)]=$_3;}unset($_11[_759639143(38)]);foreach($_11 as&$_12)if(!$GLOBALS['_841550575_'][10]($_12))$_12=htmlspecialcharsbx($_12);foreach($_11 as&$_12)$_12=htmlspecialcharsbx($_12);echo CStartShopUtil::ReplaceMacros($_9[0],$_11);static::DrawParameters($_10['PARAMETERS'],$_7,$_8);echo CStartShopUtil::ReplaceMacros($_9[round(0+0.25+0.25+0.25+0.25)],$_11);}}public static function DrawParameters($_0,$_13="#CONTROL#",$_8=null){if(!$GLOBALS['_841550575_'][11]($_0))return;foreach($_0 as $_3 => $_4){$_11=$_4;if(empty($_11[_759639143(39)])){$_11[_759639143(40)]=$_3;}else{$_3=$_11[_759639143(41)];}unset($_11[_759639143(42)]);foreach($_11 as&$_12)if(!$GLOBALS['_841550575_'][12]($_12))$_12=htmlspecialcharsbx($_12);$_11[_759639143(43)]=_759639143(44);$_14=array();if($GLOBALS['_841550575_'][13]($_4[_759639143(45)]))foreach($_4[_759639143(46)]as $_15 => $_16)$_14[$_15]=htmlspecialcharsbx($_15) .'="' .htmlspecialcharsbx($_16) .'"';unset($_14[_759639143(47)],$_14[_759639143(48)]);if($_4[_759639143(49)]== _759639143(50)|| $_4[_759639143(51)]== _759639143(52)){$_11[_759639143(53)]=_759639143(54) .htmlspecialcharsbx($_3) .'"
                            name="' .htmlspecialcharsbx($_3) .'"
                            value="' .htmlspecialcharsbx($_4['VALUE']) .'"' .(!empty($_14)?' ' .$GLOBALS['_841550575_'][14](_759639143(55),$_14):_759639143(56)) ._759639143(57);}else if($_4[_759639143(58)]== _759639143(59)){$_17=htmlspecialcharsbx($_4['PROPERTIES']['class']);unset($_14[_759639143(60)]);$_11[_759639143(61)]=_759639143(62) .htmlspecialcharsbx($_3) .'" />' .'<input type="checkbox"
                            id="' .htmlspecialcharsbx($_3) .'"
                            name="' .htmlspecialcharsbx($_3) .'"
                            class="adm-checkbox adm-designed-checkbox startshop-area-switch' .(!empty($_17)?' ' .$_17:_759639143(63)) ._759639143(64) .($_4[_759639143(65)]== _759639143(66)?_759639143(67):_759639143(68)) .(!empty($_14)?_759639143(69) .$GLOBALS['_841550575_'][15](_759639143(70),$_14):_759639143(71)) ._759639143(72) ._759639143(73) .htmlspecialcharsbx($_3) .'"></label>';}else if($_4[_759639143(74)]== _759639143(75)){$_11[_759639143(76)]=_759639143(77) .htmlspecialcharsbx($_3) .'" name="' .htmlspecialcharsbx($_3) .'" maxlength="7" value="' .htmlspecialcharsbx($_4['VALUE']) .'"/>' .'<div id="' .htmlspecialcharsbx($_3) .'-color" style="
                            display: inline-block;
                            vertical-align: middle;
                            border: 1px solid;
                            border-color: #87919c #959ea9 #9ea7b1 #959ea9;
                            border-radius: 5px;
                            margin-left: 10px;
                            width: 20px;
                            height: 20px;
                            background: ' .htmlspecialcharsbx($_4['VALUE']) .';
                            cursor: pointer;
                        "></div>' .'<script type="text/javascript">
                            $(\'#' .htmlspecialcharsbx($_3) .'-color\').ColorPicker({
                                color: \'' .htmlspecialcharsbx($_4['VALUE']) .'\',
                                onShow: function ($oColorPicker) {
                                    $($oColorPicker).fadeIn(500);
                                    return false;
                                },
                                onHide: function ($oColorPicker) {
                                    $($oColorPicker).fadeOut(500);
                                    return false;
                                },
                                onSubmit: function (hsb, hex, rgb) {
                                    $(\'#' .htmlspecialcharsbx($_3) .'\').val(\'#\' + hex);
                                    $(\'#' .htmlspecialcharsbx($_3) .'-color\').css(\'background\' ,\'#\' + hex);
                                },
                            });

                            $(\'#' .htmlspecialcharsbx($_3) .'\').change(function() {
                                $(\'#' .htmlspecialcharsbx($_3) .'-color\').css(\'background\', $(this).val());
                            });
                        </script>';}else if($_4[_759639143(78)]== _759639143(79)){$_11[_759639143(80)]=_759639143(81) .htmlspecialcharsbx($_3) .'"
                            name="' .htmlspecialcharsbx($_3) .'"' .(!empty($_14)?' ' .$GLOBALS['_841550575_'][16](_759639143(82),$_14):_759639143(83)) ._759639143(84);if($GLOBALS['_841550575_'][17]($_4[_759639143(85)]))foreach($_4[_759639143(86)]as $_18 => $_19)$_11[_759639143(87)].= _759639143(88) .$_18 ._759639143(89) .($_4[_759639143(90)]== $_18?_759639143(91):_759639143(92)) ._759639143(93) .htmlspecialcharsbx($_19) .'</option>';$_11[_759639143(94)].= _759639143(95);}else if($_8 instanceof Closure){$_11[_759639143(96)]=$_8($_3,$_4);}if(!empty($_11[_759639143(97)]))echo CStartShopUtil::ReplaceMacros($_13,$_11);}}} ?>
