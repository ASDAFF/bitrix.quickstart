  <?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?>
						</td>
						<td style="padding: 0px 0px 20px 20px; width: 100px; height: 100%; border-left: solid 1px #C3DCEA; " valign="top">
						<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
							"AREA_FILE_SHOW" => "sect",
							"AREA_FILE_SUFFIX" => "right1_inc",
							"AREA_FILE_RECURSIVE" => "Y",
							"EDIT_TEMPLATE" => "sect_inc.php"
							),
							false
						);?>
						
						<!-- спецпредложение -->
						<div class="spezuha">
							<div class="specline"></div>     
							<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
	"AREA_FILE_SHOW" => "sect",
	"AREA_FILE_SUFFIX" => "right2_inc",
	"AREA_FILE_RECURSIVE" => "Y",
	"EDIT_TEMPLATE" => "sect_inc.php"
	),
	false,
	array(
	"ACTIVE_COMPONENT" => "Y"
	)
);?>
						</div>
						<!-- /спецпредложение -->
						
						</td>
					</tr>
				</table>
        	</td> 
		</tr>
	    </table>
	</td>
</tr>
<tr>
	<td class="developers" height=95 align=center>	    
	    <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
							"AREA_FILE_SHOW" => "sect",
							"AREA_FILE_SUFFIX" => "bottom1_inc",
							"AREA_FILE_RECURSIVE" => "Y",
							"EDIT_TEMPLATE" => "sect_inc.php"
							),
							false
						);?>
	</td>
	<td class="copyright" align=center>    	    
	    	<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
							"AREA_FILE_SHOW" => "sect",
							"AREA_FILE_SUFFIX" => "bottom2_inc",
							"AREA_FILE_RECURSIVE" => "Y",
							"EDIT_TEMPLATE" => "sect_inc.php"
							),
							false
						);?>
	</td>
</tr>
</table>      
<!-- Yandex.Metrika counter -->
<div style="display:none;"><script type="text/javascript">
(function(w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter7026910 = new Ya.Metrika({id:7026910,
                    clickmap:true,
                    trackLinks:true});
        }
        catch(e) { }
    });
})(window, 'yandex_metrika_callbacks');
</script></div>
<script src="//mc.yandex.ru/metrika/watch_visor.js" type="text/javascript" defer="defer"></script>
<noscript><div><img src="//mc.yandex.ru/watch/7026910" style="position:absolute; left:-9999px;" alt="" /></div></noscript>


<!-- /Yandex.Metrika counter -->
</body>
</html>