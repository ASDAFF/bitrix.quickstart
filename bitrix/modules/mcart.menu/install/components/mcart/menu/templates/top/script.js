function newSubLiObj(Li, subLi){
    
    var SubLiObj = new Object();

    SubLiObj.Li = Li;
    SubLiObj.subLi = subLi;

    SubLiObj.goLeft = function() {
	this.subLi.hide();
        if( this.widthWindow < this.widthSubLiRight ){
            this.subLi.css( "left", this.widthLi - this.widthSubLi)
         };
    }

    SubLiObj.constuct = function() {

        this.widthLi = this.Li.width();
        this.widthSubLi = this.subLi.outerWidth();
     this.subLiLeft = this.Li.offset().left;
        this.widthWindow = $(window).width();
        this.widthSubLiRight = this.widthSubLi + this.subLiLeft;

    };

    SubLiObj.destruct = function() { 

        delete this.widthLi;
        delete this.widthSubLi;
     delete this.subLiLeft;
        delete this.widthWindow;
        delete this.widthSubLiRight;

        delete this.Li;
        delete this.subLi;
    };

SubLiObj.constuct();
SubLiObj.goLeft();
    SubLiObj.destruct();
};


function goodbayMouseMenu(Li){
        var subLi = Li.find( ".MCArt__subLevelMenuBox" );
        subLi.hide();
};

function helloMouseMenu(Li){
        var subLi = Li.find( ".MCArt__subLevelMenuBox" );
        subLi.show();    
};


function createBind() {
    $("#MCArt #MCArt__topLevelMenu > li").each(function(){
        _Li = $( this );
        _Li.find( ".MCArt__subLevelMenuBox" ).each(function(){
_subLi = $( this );
            newSubLiObj(_Li, _subLi);
        });
    }) 
};


		$(document).ready(function(){	
		
		
				
			$("#MCArt .MCArt__subLevelMenuBox ").each(function(){
				var menuBox = $(this),
				newWidth = 0;
				menuBox.find(" .MCArt__subLevelMenu ").each(function(){
					newWidth += $(this).width() + 40;
				});

				menuBox.width(newWidth - 20);

		
				
			
				menuBoxHeight = menuBox.height();
				var tempScrollTop = 0
				var currentScrollTop = 0;
				var positionMenu = 0;
				$(window).scroll(function(){
					currentScrollTop = $(window).scrollTop();
					if (tempScrollTop != currentScrollTop ){
						tempScrollTop = currentScrollTop;
						if(tempScrollTop > 200){
							$("#MCArt").css({'position':'fixed', left:'0px', top:'0px'});
						}else{
							$("#MCArt").css({'position':'relative'});
							}
					}    
				});	
			});
			$("#MCArt #MCArt__topLevelMenu > li").mouseenter(function(){
        var _Li = $( this );
        helloMouseMenu(_Li);

    }).mouseleave(function(){
         var _Li = $( this );
         goodbayMouseMenu(_Li);
    });	
    var _Li;
    var _subLi;

    createBind();

    $(window).resize(function(){
       createBind();
    });	
		});