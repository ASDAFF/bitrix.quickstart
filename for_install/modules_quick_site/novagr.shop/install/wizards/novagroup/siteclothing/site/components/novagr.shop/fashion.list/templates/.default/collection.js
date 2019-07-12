var collection = {
	/**
     * 	1 - the image in a popup window on the whole screen (by default)
     *  2 - increase when the mouse on the picture
     *  3 - the image in a popup window is scaled to the size of the screen
     */
    detailCardView:1,
    messages: {},
    photos: {},
    nophotoPic: {},
    changePhotos: function(arrPic) {

    	if (collection.photos.MIDDLE.length ==0) {
    		$("#photosIm").html('<img width="450" alt="" src="'+collection.nophotoPic+'">');
    		return;
    	}
    	
    	var arrPic = arrPic || 0;
    	
		var bigPhotosHtml = '';//, smallPhotosHtml = '';
		var firstPicSrc = '';
		var j=1;
		var continuePicture = '';
		if (collection.detailCardView == 3) {
			
			if (arrPic !== 0) {

				continuePicture = arrPic['middle'];
				
				bigPhotosHtml += '<a class="detailLink fancybox" rel="gallery" href="'+arrPic['big']+'"><img id="detailImg'+j+'" src="'+arrPic['middle']+'" width="450" alt="" /></a>';

				j=2;
			}
			
			for (var i in collection.photos.MIDDLE) {
				
				if (continuePicture == collection.photos.MIDDLE[i]) continue;
				if (j==1) {
					var style = '';
				} else {
					var style = 'style="display:none;"';
				}
				
				bigPhotosHtml += '<a class="detailLink fancybox" rel="gallery" href="'+collection.photos.BIG[i]+'" '+style+'><img id="detailImg'+j+'" src="'+collection.photos.MIDDLE[i]+'" width="450" alt="" /></a>';

				
				j++;
			}
			
		} else {
			
			for (var i in collection.photos.MIDDLE) {
				
				if (j==1) {
					firstPicSrc = collection.photos.BIG[i];	
					bigPhotosHtml += '<a id="fLinkPicCollection" href="#myModalCollection" role="button" class="detailLink"><img data-big-pic="'+collection.photos.BIG[i]+'" id="detailImg"  width="450" alt="" src="'+collection.photos.MIDDLE[i]+'"></a>';
						
				}
							
				j++;
			}
		}
		$("#photosIm").html(bigPhotosHtml);
		if (collection.detailCardView == 2) {
				
			$('#photosIm').zoom({url: firstPicSrc});
		}
    },
	init: function(detailCardView, messages, photos, nophotoPic) {
		var self = this;
		self.messages = messages;
		self.detailCardView = detailCardView;
		self.photos = photos;
		self.nophotoPic = nophotoPic;

		self.changePhotos();
		
		if (self.detailCardView == 3) {
			$(".fancybox").fancybox({ helpers:  { title:  null } });

			$('.previewImg').mouseenter(function() {
				
				var arrPic = [];
	    		arrPic['small'] = $(this).attr('src');
	    		arrPic['middle'] = $(this).data('middle-pic');
	    		arrPic['big'] = $(this).data('big-pic');	
	    		self.changePhotos(arrPic);
	
	        });
		} else if (self.detailCardView == 2) {
			
			 // handler hover on the preview gallery
			$('.previewImg').mouseenter(function(){
		        //document.getElementById('detailImg1').src = $(this).data('big-pic');
		        $('#detailImg')
		            .attr("src", $(this).data('middle-pic'))
		            .attr("data-big-pic", $(this).data('big-pic'));
		        
		     // prescribe zoom handler for the picture
				$('img.zoomImg').remove();
				$('#photosIm').zoom({url:  $(this).data('big-pic')});
		    });
		}
		else {

			$('.previewImg').mouseenter(function(){
	            $('#detailImg').attr('src', $(this).attr('data-middle-pic'));
	            $('#detailImg').attr('data-big-pic', $(this).attr('data-big-pic'));
	        });

	        // when you click on a small picture pops up large	
	        $("#thumbsCollection img").live('click', function(){

	            $("#fLinkPicCollection").trigger('click');
	            return false;
	        });

	        // click the picture comes modal window with carousel
	        $("#fLinkPicCollection").live('click',function() {

	            showAjaxLoader();

	            var picHTML = '';
	            var picArr = [];
	            var curPic = $(this).find("img").attr("data-big-pic");

	            var total = $('#thumbsCollection img').length;

	            var title = $('.header-title-demo h1').html();
	            $('#thumbsCollection img').each(function(i, val) {
	                //console.log($(this).attr("name"));
	                picArr[i] = $(this).attr("data-big-pic");
	            });

	            var j = 1;
	            var curImageIndex = 1;

	            for (var i in picArr) {
	                var active = '';
	                if (picArr[i] == curPic) {
	                    active = 'active ';
	                    curImageIndex = j;
	                }

	                picHTML += '<div class="'+active+'item">' +
	                '<div class="modal-header">' +
	                '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
	                '<h3>'+title+' </h3>' +
	                '</div>' +
	                '<div class="modal-body">' +
	                '<img alt="" src="'+picArr[i]+'">' +
	                '</div>' +
	                '<div class="modal-footer">'+self.messages.CAROUSEL_LABEL1+' <span class="curImg">'+j+'</span> '+self.messages.CAROUSEL_LABEL2+' <span class="totalImg">'+total+'</span></div>' +
	                '</div>';
	                ++j;
	            }

	            // show arrows depending on the current page
	            showHideArrows(curImageIndex, total);

	            $("#carousel-inner-collection").html(picHTML);

	            var $myCarouselCollection = $('#myCarouselCollection').carousel({'interval': false});
	            // скрываем стрелки если послед. картинка
	            $myCarouselCollection.on('slid', function() {

	                var curImageIndex = $("#carousel-inner-collection .active .curImg").html();
	                showHideArrows(curImageIndex, total);
	                showAjaxLoader();

	                var preloadImage = new Image();
	                preloadImage.onload = function(){
	                    hideAjaxLoader();
	                    var marginLeft = ((preloadImage.width+30)/2);
	                    $("#myModalCollection").css('marginLeft', "-"+marginLeft+"px");
	                }
	                preloadImage.src = $("#myModalCollection .carousel-inner .active .modal-body img").attr("src");
	            });

	            var preloadImage = new Image();
	            preloadImage.onload = function(){
	                hideAjaxLoader();
	                var marginLeft = ((preloadImage.width+30)/2);
	                $("#myModalCollection").modal({'marginLeft': marginLeft});
	            }
	            preloadImage.src = curPic;
	            return false;
	        });
		}
		// events
		$('.quickViewLink').click(function(){
	        return loadPreviewElementModalWindow($(this).attr('href'),0,true);
	    });
		// tooltips
        $('.tooltip-demo').tooltip({
            selector: "button,li,a[rel=tooltip]"
        });
	}
}