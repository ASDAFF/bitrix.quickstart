<?
	$pathToJQ = CJSCore::getExtInfo('jquery');
	$pathToJQ = $pathToJQ['js'];
?>
<script type='text/javascript'>
	if(typeof IPOL_JSloader == 'undefined')
	var IPOL_JSloader = {
		ver: 2,

		jqInited: false,

		bindReady: function(handler){
			var called = false;
			function ready(){
				if (called) return;
				called = true;
				handler();
			}
			if(document.addEventListener){
				document.addEventListener("DOMContentLoaded",function(){
					ready();
				}, false);
			}else if(document.attachEvent) {
				if (document.documentElement.doScroll && window == window.top){
					function tryScroll(){
						if (called) return;
						if (!document.body) return;
						try {
							document.documentElement.doScroll("left");
							ready();
						}catch(e){
							setTimeout(tryScroll,0);
						}
					}
					tryScroll();
				}
				document.attachEvent("onreadystatechange", function(){
					if (document.readyState === "complete"){
						ready();
					}
				});
			}
			if (window.addEventListener)
				window.addEventListener('load', ready, false);
			else if (window.attachEvent)
				window.attachEvent('onload', ready);
		},

		loadScript: function(src,ifJQ,callback){
			if(typeof(ifJQ) == 'undefined')
				ifJQ = false;
			var loadedJS = document.createElement('script');
			loadedJS.src = src;
			loadedJS.type = "text/javascript";
			loadedJS.language = "javascript";
			var head = document.getElementsByTagName('head')[0];
			head.appendChild(loadedJS);
			if(ifJQ || callback){
				loadedJS.onload = (ifJQ) ? IPOL_JSloader.recall : callback;
				loadedJS.onreadystatechange = function () { //  IE
					 if (this.readyState == 'complete' || this.readyState == 'loaded')
						 loadedJS.onload();
				};
			}
		},

		loadJQ: function(){
			IPOL_JSloader.loadScript('<?=$pathToJQ?>',true);
			jqInited = true;
		},

		recalled: [],
		checkScript: function(checker,src,callback){
			if(typeof(callback) == 'undefined') callback = false;
			IPOL_JSloader.recalled.push([checker,src,callback]);
			if(!IPOL_JSloader.jqInited && !IPOL_JSloader.checkJQ())
				IPOL_JSloader.loadJQ();
			else
				IPOL_JSloader.recall();
		},

		checkLoadJQ: function(callback){
			if(!IPOL_JSloader.jqInited && !IPOL_JSloader.checkJQ()){
				if(typeof(callback) == 'function')
					IPOL_JSloader.recalled.push([true,false,callback]);
				IPOL_JSloader.loadJQ();
			}else
				if(typeof(callback) == 'function')
					callback();
		},

		checkJQ: function(){
			return(typeof($) != 'undefined' && typeof($('body').html) != 'undefined');
		},

		recall: function(){
			if(IPOL_JSloader.recalled.length == 0) return;
			else
				for(var i in IPOL_JSloader.recalled){
					if(!IPOL_JSloader.recalled[i][0] || typeof(eval(IPOL_JSloader.recalled[i][0])) == 'undefined')
						IPOL_JSloader.loadScript(IPOL_JSloader.recalled[i][1],false,IPOL_JSloader.recalled[i][2]);
					else
						if(typeof(IPOL_JSloader.recalled[i][2])=='function')
							IPOL_JSloader.recalled[i][2]();
					delete(IPOL_JSloader.recalled[i]);
				}
		}
	};
</script>