function JCommentsEditor(textarea,resizable){this.init(textarea,resizable);}
function JComments(oi,og,r){this.init(oi,og,r);}

JCommentsEditor.prototype = {
	ta: null,
	l10n: {},
	tags: {},
	smiles: {},
	events: null,
	counter: null,
	focused: false,
	resizable: true,

	init: function(textareaID, r) {
		this.ta=JComments.prototype.$(textareaID);
		this.panelElements={};
		this.l10n={counterPre:'',counterPost:' symbols left',enterValue: 'Enter value'};
		this.resizable=r;
		
		this.defaultHeight=this.ta.clientHeight;
		this.defaultRows=this.ta.rows;
		
		this.isWebkit=/webkit/.test(navigator.userAgent.toLowerCase());

		var th = this;
		
	},
	defined: function(v){return (typeof(v)!="undefined");},
	insertText: function(text) {
		var ta=this.ta;
		if(this.defined(ta.caretPos)&&ta.createTextRange){ta.focus();var sel=document.selection.createRange();sel.text=sel.text+text;ta.focus();}
		else if(this.defined(ta.selectionStart)){
			var ss=ta.value.substr(0, ta.selectionStart);
			var se=ta.value.substr(ta.selectionEnd),sp=ta.scrollTop;
			ta.value=ss+text+se;
			if(ta.setSelectionRange){ta.focus();ta.setSelectionRange(ss.length+text.length,ss.length+text.length);}
			ta.scrollTop=sp;
		} else {ta.value+=text;ta.focus(ta.value.length-1);}
	},
	initSmiles: function(p){this.smilesPath=p;
		if(this.ta){
			this.smilesPanel=document.createElement('div');
			if(this.bbcPanel){
				document.body.appendChild(this.smilesPanel);
				this.smilesPanel.id='comments-form-smilespanel';
				this.smilesPanel.setAttribute('style','display:none;top:0;left:0;position:absolute;');
				this.smilesPanel.onclick=function(){this.style.display='none';};
				var jc=this,f=function(e){
					var sp=jc.smilesPanel,p=jc.getElementPos(e);
					if(sp){var sps=sp.style;sps.display=(sps.display=='none'||sps.display==''?'block':'none');sps.left=p.left+"px";sps.top=p.bottom+e.offsetHeight+"px";sps.zIndex=99;}
					return false;
				};
				this.bbcPanel.appendChild(this.createButton(null,null,'bbcode-smile',f));
			} else {
				this.smilesPanel.className='smiles';this.ta.parentNode.insertBefore(this.smilesPanel, this.ta);
			}
		}
	},
	closeSmiles: function(){if(this.smilesPanel&&this.bbcPanel){this.smilesPanel.style.display='none';}},

	addSmile: function(code,image){
		if(this.ta){
			if(!this.smilesPath||!this.smilesPanel){return;}
       			var th=this,e=document.createElement('img');
       			e.setAttribute('src',this.smilesPath+'/'+image);
       			e.setAttribute('alt',code);
       			e.className='smile';
	       		e.onclick=function(){th.insertText(' '+code+' ');};
       			this.smilesPanel.appendChild(e);
		}
	}
};

JComments.prototype = {
	oi:null,
	og:null,
	debug: false,
	requestURI: '',
	oldRequestURI: '',
	busy: null,
	form: null,
	cache: {},
	mode:'add',
	readyList: [],
	isReady: false,

	$: function(id){if(!id){return null;}var e=document.getElementById(id);if(!e&&document.all){e=document.all[id];}return e;}
};