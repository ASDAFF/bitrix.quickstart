<?if($APPLICATION->GetCurPage(false)==SITE_DIR):?>  
<div id="wrap-promo">
<script type="text/javascript">
$(document).ready(function(){
$("#close-pay a").click(function(){
$("#wrap-promo").toggle();
return false;
});
});

</script>
<div class="wrap_colors">
<ul>
<li class="price">�������� ������������ ������ 941 PRO</li>
<li class="pay"><a href="http://941pro.makarov.pro/" target="_blank">��������</a></li>
<li class="mastercard"></li>
<li class="visa"></li>
<li class="yandexmoney"></li>
</ul>
</div>
<div class="sub_wrap_colors "></div>
<div id="close-pay"><a href="#map" title="�������">�������</a></div>
</div>
<?else:?>
<?endif?>
