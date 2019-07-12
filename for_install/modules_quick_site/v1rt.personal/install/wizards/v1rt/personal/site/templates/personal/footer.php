<?IncludeTemplateLangFile(__FILE__);?>
</td>
				</tr>
			</table>
		</td>
	</tr>
    <?$APPLICATION->IncludeFile($APPLICATION->GetTemplatePath("include_areas/inc.twitter.php"), array(), array("MODE"=>"html"));?>
	<tr>
		<td class="footer">
		<div class="counter">
            <?$APPLICATION->IncludeFile($APPLICATION->GetTemplatePath("include_areas/inc.counters.php"), array(), array("MODE"=>"html"));?>
        </div>
		<?$APPLICATION->IncludeFile($APPLICATION->GetTemplatePath("include_areas/inc.copyright.php"), array(), array("MODE"=>"html"));?>
		<br/>
        <?=GetMessage("DEV")?>
        </td>
	</tr>
</table>
</body>
</html>