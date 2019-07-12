var keysave = 0;

var antiCusel = 0;

var get = new Array;

var imagepath = 'bitrix/templates/iarga.shopplus100.main/images';

var phoneformat = "+7 (___) ___-__-___";



function setcookie(name,val){

	$.post(SITE_DIR+'inc/ajax/setcookie.php',{'name':name,'val':val});

}

function getval(num,rnd){

	if(num==undefined) return 0;

	ret = eval(num.replace(/[^0-9\.]*/,""));

	if(rnd) ret = Math.round(ret);

	if(ret==undefined) return 0;

	return ret;

}

function round(num, dec){

	var dec = 10 ^ dec;

	return Math.round(num * dec) / dec;

}

function sklon(num, form1, form2, form3){

	if(num%10==0 || num%10>4 || num==11 || num==12 || num==13 || num==14) return form1;

	else if(num%10==1) return form2;

	else return form3;

}



//  

function getAbs(val){

	return Math.abs(eval(val.replace("px","")));

}

function getClientWidth(){

  //return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientWidth:document.body.clientHeight;

  return $.browser.opera? window.innerWidth : $(window).width();

}



function getClientHeight(){

  //return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.innerHeight;

  return $.browser.opera? window.innerHeight : $(window).height();

}



function formatSumm(summ){

	var costf = "";

	cost = summ+"";

	cnt = 0;

	var i = 0;

	for(i=(cost.length-1);i>=0;i--){

		if((cnt)%3==0) costf = ' '+costf;

		costf = cost[i]+costf;

		cnt ++;

	}

	return costf;

}







//  



$(function(){

	//    

	$(".innerlink").css({'cursor':'pointer'}).click(function(){

		if($(this).find(".innerlink").length > 0) location.href = $(this).find(".innerlink").attr("href");

		else if($(this).attr("href")!='') location.href = $(this).attr("href");

	});



	// Popup

	$(".openpopup").live('click',function(){

		openpopup($(this).attr("data-rel"),$(this).attr("data-alt"));

		return false;

	});





	// 

	$(".print_it").click(function(){print();});



	//   

	$(".addpassw").live('focus',function(){

		if($(this).val() == $(this).attr("rel")){

			var newinp = $("<input type='password' name='"+this.name+"'  data-rel='"+$(this).attr('data-rel')+"' value='' class='"+this.className+"'>").insertAfter(this).focus();

			$(this).remove();

			newinp.blur(function(){

				if($(this).val()==''){

					var newinp = $("<input type='text' name='"+this.name+"'  data-rel='"+$(this).attr('data-rel')+"' value='"+$(this).attr('data-rel')+"' class='"+this.className+"'>").insertAfter(this);

					$(this).remove();

				}

			});

		}

	});

	// 

	$("input.repl, textarea.repl").focus(function(){if($(this).val()==$(this).attr("data-alt")) $(this).val("");});

	$("input.repl, textarea.repl").blur(function(){if($(this).val()=="") $(this).val($(this).attr("data-alt"));});

	$("input.repl, textarea.repl").keyup(function(){if($(this).val()=="" || $(this).val()==$(this).attr("data-alt")) $(this).removeClass("act"); else $(this).addClass("act");});

	$("input.repl, textarea.repl").keyup();



	//    

	if($(".pagenavig a").length > 0){

		$("body").keydown(function(e){

		  if(keysave==17 && e.which==39) location.href=$(".pagenavig .next").attr("href");

		  else if(keysave==17 && e.which==37) location.href=$(".pagenavig .prev").attr("href");

		  else keysave = e.which;

		});

	}



	/*

	if(location.href.match(/\?/)){

		var loc = location.href;

		r = loc.replace(loc.split("?")[0]+"?", "").split("&");

		for(i in r){

			v = r[i].split("=");

			get[v[0]] = decodeURIComponent(r[i].replace(v[0]+"=", ""));

		}

	}

	*/

	initUnuform("uniform");





	//    hasDef  rel    

	$("input.hasDef, textarea.hasDef").each(function(){

		if($(this).val()!=$(this).attr("data-rel")) $(this).addClass("hasText");

		$(this).blur(function(){

			if($(this).val()==""){

				$(this).val($(this).attr("data-rel"));

				$(this).removeClass("hasText");

			}

		});

		$(this).focus(function(){

			if($(this).val()==$(this).attr("data-rel")){

				$(this).val("");

				$(this).addClass("hasText");

			}			

		});

	});



	// 

	$("form .submit").live('mouseup',function(){

		if($(this).attr('name')!=undefined) $("<input type='hidden' name='"+$(this).attr('name')+"'  value='1'>").appendTo($(this).parents('form'));		

		$(this).parents('form').submit(); return false;

	});

	$("form").live('submit',function(){

		$(this).find("input.repl,textarea.repl").each(function(){

			if($(this).attr("alt")==$(this).val()) $(this).val('');

		});

	});



	// 

	$(".phoneformat").bind('keyup focus',function(event){

		var val = $(this).val();

		for(i=0;i<val.length;i++){

			if(phoneformat[i]==undefined) break;

			else if(phoneformat[i]!='_') val = repl(val,i,phoneformat[i]);

			else if(!val[i].match(/[0-9]/)){

				val = repl(val,i,'');

				break;

			}

		}

		if(event.keyCode!=8){

			if(i < phoneformat.length){

				for(i;i<phoneformat.length;i++){

					if(phoneformat[i]!='_') val = repl(val,i,phoneformat[i]); else break;

				}

			}

		}

		$(this).val(val.substr(0,(phoneformat.length -1)));		

	});

});







//  -

function initUnuform(code){







	//   

	//    JQuery  

	//     uniform,     p.error     

	// action      -

	$("."+code+" input").live('keypress',function(event,form){

		if(event.keyCode==13){

			$(this).parents("form").submit();

		}

	});





	

	$("."+code).submit(function(){



		if(antiCusel == 0){

			$(".uniform.active p.step").html('');

			$(this).addClass("active").removeClass("success");

			$(this).find(".element-form").removeClass("error");

			$(this).find("p.error").show().html("<img src='/"+imagepath+"/preloader.gif'>").removeClass("success");

			var inps = $(this).find("input.repl, textarea.repl");

			for(i=0;i<inps.length;i++){

				$("<input type='hidden' name='"+inps.eq(i).attr("name")+"_default_value' value='"+inps.eq(i).attr('alt')+"' class='tempadd'>").appendTo($(this));

			}



			$("#newframe").remove();

			var newframe = $('<iframe id="newframe" name="newframe" src="'+$(this).attr('action')+'"></iframe>').appendTo("body").hide();

			var newform = $(this).attr("target","newframe").attr("method","post").attr("enctype","multipart/form-data");



		

			

			//newform.submit();



			newframe.bind('load',function(){

				$(".tempadd").remove();

				var data = $(this).contents().find('body').html();

				if (typeof handler == 'function') {

					handler(data);

				}

				if(data.match("step")){

					var mat = data.match(/step ([0-9]+):([0-9]+)/);

					$(".uniform.active").find(".skipadr").remove();

					$(".uniform.active").find(".skip_adr").remove();

					$(".uniform.active").append($("<input type='hidden' name='skip' class='skip'>").val(mat[2]));

					$(".uniform.active").append($("<input type='hidden' name='skip_adr' class='skip_adr'>").val(mat[1]));

					$(".uniform.active p.step").html($(".uniform.active p.step").html()+'<br>'+data.replace('step','').replace('error',''));

					$(".uniform.active p.active").html('');

					setTimeout(function(){$(".uniform.active").removeClass('active').submit();},2000);

				}else if(data.match("error")){

					setTimeout((function(data_){return function(){

						var mat = data.match(/errorblock:([--0-9a-zA-Z_\-]+)/);

						if(mat){

							data = data.replace("errorblock:"+mat[1],"");

							var par = $(".uniform.active input[name='"+mat[1]+"']").parent().parent();

							var sp = $(".uniform.active .error span");

							if(mat && par.length > 0){

								par.addClass("error");

								if(sp.length > 0){

									sp.html(data.replace("error ","")).fadeIn();

									$(".uniform.active p.error").html("").fadeIn();

								}else{

									$(".uniform.active p.error").html(data.replace("error ","")).fadeIn();

								}

							}else{

								$(".uniform.active p.error").html(data.replace("error ","")).fadeIn();

							}							

							$(".uniform.active").removeClass("active");

						}else{

							$(".uniform.active p.error").html(data.replace("error ","")).fadeIn();

						}

					}})(data),200);		

				}else  if(data.match("success")){

					setTimeout((function(data_){return function(){

						if(data.match("refresh")) history.go(0);

						else if(data.match("redirect")){

							var mat = data.match(/redirect:([\.0-9a-zA-Z-\-\/]+)/);

							if(mat) location.href = (mat[1]);

						}else if(data.match("alert")){

							alert("success");

						}else if(data.match("closeit")){

							$(".uniform.active").html("<p>"+data.replace("success ","").replace("closeit","")+"</p>");

							setTimeout(function(){$(".popup_bg").click();},3000);

						}else{

							if(data.match("clear")){

								data = data.replace("clear", "");

								$(".uniform.active input, .uniform.active textarea").each(function(){

									if($(this).attr("type")=="text") $(this).val("");

									else if($(this).is("textarea")) $(this).val("");

									else if($(this).attr("type")=="checkbox") $(this).arrt("checked", false);

									else if($(this).attr("type")=="radio") $(this).arrt("checked", false);

								});

							}

							if(data.match("nodelete")){

								$(".uniform.active p.error").html(data.replace("success ","").replace("nodelete","")).addClass("success");

							}else{

								$(".uniform.active").html("<p>"+data.replace("success ","")+"</p>");

							}

							//$(".uniform.active p.error").html(data.replace("success ","")).addClass("success").fadeIn();

						}

						$(".uniform.active").removeClass("active");

					}})(data),200);		

				}else{

					$(".uniform.active p.error").html("").fadeIn();

					$(".uniform.active").removeClass("active");

				}

			});

		}

		//return false;

	});

	$(window).resize(function(){

		$(".popup").height(getClientHeight());

	});

}



//     Cusel Submit

$(".cusel").live('mouseup',function(){antiCusel = 1;});

$(".cusel").live('click',function(){setTimeout(function(){antiCusel = 0;},100);});



//        [0] -  [1] - 

function offsetPosition(element) {

	if(element!=undefined){

		var offsetLeft = 0, offsetTop = 0;

		do {

			offsetLeft += element.offsetLeft;

			offsetTop  += element.offsetTop;

		} while (element = element.offsetParent);

		return [offsetLeft, offsetTop];

	}

}





//  

$(function(){$(".popup").click(function(ev){$(".popup_bg").click();});});

function openpopup(adr,vals){

	$(".popup").fadeIn().css({'z-index':'301'}).html("<img src='/"+imagepath+"/preloader.gif'>");

	var popup_style = {'position':'fixed','top':'0px','width':'100%','height':$(document).height(),'margin-bottom':'100px','z-index':'300','background':'#000','opacity':'0.5'};

	$("<div class='popup_bg'>").appendTo($('body')).fadeIn().css(popup_style).click(function(){$(".popup").fadeOut(); $('body').css('overflow','auto'); $(this).fadeOut(function(){$(this).remove();});});

	$.post(SITE_DIR+"inc/popups/"+adr+".php",vals,function(data){		

		$(".popup").html(data).height(getClientHeight()).css('top',$(document).scrollTop());

		$('body').css('overflow','hidden');

		initUnuform("unipopup");

		$(".popup div").eq(0).click(function(ev){ev.stopPropagation();});

		$(".popup .close").click(function(){$(".popup_bg").click(); return false;});

		

		// 

		$(".popup input.repl, .popup textarea.repl").focus(function(){if($(this).val()==$(this).attr("rel")) $(this).val("");});

		$(".popup input.repl, .popup textarea.repl").blur(function(){if($(this).val()=="") $(this).val($(this).attr("rel"));});



		// . 

		if(typeof('popup_opened')=='function') popup_opened();



		//   

		top = $(document).scrollTop();

		if($(".popup").height() + top > $(document).height()) top = $(document).height() - $(".popup").height() - 50;

		if($(".popup").height() < getClientHeight() - 100) $(".popup").css({'position':'fixed','top':'50px'});

		else $(".popup").css({'position':'absolute','top':top});

	});

}





//  

function repl(str,num,symb){

	str1 = str.substr(0,num);

	str2 = str.substr(num+1);

	return str1+symb+str2;

}



// 

function preloader_start(targ){

	if($(".iarga_preloared").length < 1){

		$("<img src='/"+imagepath+"/preloader.gif' class='iarga_preloared'>").prependTo($(targ).parents("dd"));

		setTimeout(function(){$(".iarga_preloared").fadeOut(function(){$(this).remove();});},3000);

	}

}

function preloader_stop(){

	$(".iarga_preloared").fadeOut(function(){$(this).remove();});

}