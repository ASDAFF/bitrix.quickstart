
<?php
    IncludeModuleLangFile(__FILE__);
?>

<style>
.sh_reg_line {
    background-color: #F4F6F9;
    margin-bottom: 2px;
    min-height: 40px;
}
.sh_reg_line.sh_button_line{
    background: none;
    overflow: hidden;
    zoom: 1;
}
.sh_reg_line.sh_reg_line_visible:after {
    clear: both;
    color: #FFFFFF;
    content: ".";
}
.sh_reg_line.sh_reg_line_visible {
    overflow: visible;
}
.sh_reg_label {
    background-color: #DEE4EE;
    color: #646464;
    float: left;
    font-size: 14px;
    min-height: 25px;
    padding-bottom: 5px;
    padding-left: 10px;
    padding-top: 10px;
    text-align: left;
    width: 250px;
}
.sh_reg_input {
    color: #646464;
    float: left;
    font-size: 14px;
    margin-left: 20px;
    margin-top: 6px;
    text-align: left;
}
.sh_reg_input input{
    border: 1px solid #A5A5A5;
    border-radius: 3px 3px 3px 3px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15) inset;
    color: #363636;
    font-size: 14px;
    height: 30px;
    line-height: 30px;
    padding-left: 3px;
    width: 199px;
}
.sh_reg_line label{
    font-size: 14px;
    color: #646464;
    display: block;
    margin-left: 6px;
    position: relative;
    top: 10px;
}
.sh_reg_line label.sh_layout{
    font-size: 14px;
    color: #646464;
    margin-left: -4px;
	margin-right: 6px;
    display: inline;
    top: 0;
}
.sh_reg_line label.sh_layout input{
    width: 10px;
    height: 10px;
    vertical-align: top;
}
.sh_reg_input select{
    border: 1px solid #A5A5A5;
    border-radius: 3px 3px 3px 3px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15) inset;
    color: #363636;
    font-size: 14px;
    height: 30px;
    line-height: 30px;
    padding: 2px 2px 2px 3px;
    width: 210px;
}
.sh_reg_colorpicker {
    border: 1px solid #A5A5A5;
    border-radius: 1px 1px 1px 1px;
    box-shadow: 0 1px 2px #A5A5A5 inset;
    height: 22px;
    line-height: 24px;
    padding-left: 1px;
    position: relative;
    width: 41px;
}
.sh_reg_color_face {
    background: url(//siteheart.com/img/reg/select.png) no-repeat scroll 100% 50% transparent;
    height: 22px;
}
.sh_reg_color_item {
    background: none repeat scroll 0 0 #D3D3D3;
    height: 20px;
    margin-top: 1px;
    width: 20px;
}
.sh_reg_color_block {
    background: none repeat scroll 0 0 #FFFFFF;
    border: 1px solid #A5A5A5;
    left: -1px;
    padding: 0 0 1px;
    position: absolute;
    top: 23px;
    width: 41px;
}
.sh_reg_color_line {
    padding-left: 1px;
}
.sh_reg_color_line:hover{
    background: #3399ff;
}
.sh_reg_color_item {
    background: #D3D3D3;
    height: 20px;
    margin-top: 1px;
    width: 20px;
}
.sh_reg_color_item.sh_dark {
    background: #3B3B3B;
}
.sh_reg_color_item.sh_green {
    background: #68A604;
}
.sh_reg_color_item.sh_blue {
    background: #1C80E2;
}
.sh_reg_color_item.sh_purple {
    background: #A41CD0;
}
.sh_reg_color_item.sh_red {
    background: #A40404;
}
.sh_reg_color_item.sh_pink {
    background: #F32FB0;
}
.sh_reg_color_item.sh_orange {
    background: #E19104;
}
.sh_submit{
    background: #B11313;
    border-radius: 3px;
    color: #FFFFFF !important;
    cursor: pointer;
    font-size: 22px;
    height: 40px;
    line-height: 40px;
    margin: 20px 0 0 282px;
    text-align: center;
    width: 207px;
    display: block;
    text-decoration: none !important;
}
.sh_preloader{
    width: 100%;
    height: 200px;
    background: #fff url(//d1ytok5muqmio7.cloudfront.net/apps/workplace/img/ajax-loader.gif) no-repeat 50% 50%;
}
</style>
<div class="sh_preloader" id="sh_preloader"></div>
<div id="sh_autorizing" style="margin-top: 15px;width: 245px;text-align: center;display: none;">
    <?=GetMessage("SH_AUTORIZING")?>
    <iframe src="//siteheart.com/<?php echo isset($_GET['lang']) && $_GET['lang'] == 'en' ? 'en/' : 'ru/' ?>bitrixauth" onload="sh_getAuth();" frameborder="0" width="310" height="185" style="display: block;margin-top: 9px;"></iframe>
</div>
<form method="POST" id="sh_divisions" style="display: none;margin-top: 15px;">
<div class="sh_reg_line sh_reg_line_visible">
    <div class="sh_reg_label"><?=GetMessage("SH_DIVISION")?>:</div>
    <div class="sh_reg_input">
	<select name="widget_id" id="sh_ent_id"></select>
    </div>
</div>
<div class="sh_reg_line sh_reg_line_visible" style="height: 46px;">
    <div class="sh_reg_label">
		<?=GetMessage("SH_SECRET_KEY")?>:
		<div style="font-size: 10px; color: #848484;">(<?=GetMessage("SH_SECRET_FOR")?>)</div>
	</div>
    <div class="sh_reg_input">
	<input type="text" name="secret_key" /> 
    </div>
</div>
<div class="sh_reg_line sh_button_line">
    <input type="hidden" name="step" value="2" />
    <a href="javascript:;" class="sh_submit" onclick="document.getElementById('sh_divisions').submit();" ><?=GetMessage("SH_SET")?></a>
</div>
</form>



<script type="text/javascript">

    function sh_getAuth(){
	
	var sh_script = document.createElement("script");  
	sh_script.src = '//siteheart.com/esapi/me/fullinfo?callback=sh_autorize';  
	sh_script.type = 'text/javascript';  
	document.body.appendChild(sh_script); 
	
    }
    
    function sh_autorize(response){
	
	document.getElementById('sh_autorizing').style.display = 'block';
	
	document.getElementById('sh_preloader').style.display = 'none';
	
	if(response.result == 'success'){
	    
	    var options = '';
	    
	    for(var i = 0; i < response.divisions.length; i++){
		
		options += '<option value="' + response.divisions[i].ent_id + '">' + (response.divisions[i].title ? response.divisions[i].title.replace('<', '&lt;').replace('>','&gt;') : '') + '</option>';
		
	    }
	    
	    document.getElementById('sh_ent_id').innerHTML = options;
	    
	    document.getElementById('sh_divisions').style.display = 'block';
	    
	    document.getElementById('sh_autorizing').style.display = 'none';
	    
	    return;
	    
	}
	
    }
    
</script>



