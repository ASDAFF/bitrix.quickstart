/**
 * @author skyh
 */

var iesa = iesa || {};

/**
 * �������� ������� � ����������
 */

iesa.Banner = function (el, url, par_delay) {
	var T = this;

	T.el = el;              /** �������, � ������� ��� ������������ */
	T.url = url || "";      /** URL ��� ������ ������� (XHR) */
	T.data = [];            /** ������ ������� */
	T.running = false;      /** ���� � ������ ������ ���������� ����� ������� */
	T.playing = null;		/** ���� ������� �������� */
	T.delay = par_delay;    /** ����� �������� ������ */
	T.current_slide = -1;   /** ������� ����� */
	T.current_frame = null; /** ���� ��� ����������� ����������� */
	T.next_frame = null;    /** ���� ��� �������� �������� */
	T.control = null;       /** ������ � �������� ������� */
	T.next_button = null;   /** ������ "��������� �����" */
	T.prev_button = null;   /** ������ "���������� �����" */
	T.change_timer = null;  /** ������ ������������ */
	T.play_pause = null;    /** ������ Play */
	
	$(window).load(function () {
		T.init();
	});
}

iesa.Banner.prototype = {
	toString: function () {
		return "[object iesa.Banner]";
	},
	
	/**
	 * �������� ������ � �������� ��������� ���������
	 */
	init: function () {
		var T = this;
		if (!T.url) {
			
			// ���� URL �� ������ - �������� ������� JSON � .data
			var data = $(".data", T.el);
			var data = eval(data.text());
		} else {
			$.ajaxSetup(
				{
					async: false
				}
			);
			$.getJSON(T.url, null, function (data) {
				T.data = data;
			});
		}
		
		T.data = data;
		
		if (data.length) { /* ������ ��������� */
			
			/* ������������ �������� */
			
			for (var i = 0, l = data.length; i < l; ++i) {
				var x = new Image;
				x.src = T.url + data[i].image;
			}
			var code = '<div class="banner_content"><div class="frame"></div><div class="overframe"></div></div><div class="control_overlay"><i class="c t l"><i></i></i><i class="c t r"><i></i></i><i class="c b l"><i></i></i><i class="c b r"><i></i></i><div class="play_pause pause"></div><div class="left"><</div><ul>';
			for (var i = 0, l = T.data.length; i < l; ++i) {
				code += '<li>' + (i + 1) + '</li>';
			}
			
			code += '</ul><div class="right">></div></div>';
			$(code).appendTo(T.el);
			T.current_frame = $(".frame", T.el).get(0);
			T.next_frame = $(".overframe", T.el).get(0);
			
			// �������� ����������
			$(".control_overlay li", T.el).click(function(){
				T.show(parseInt($(this).text()) - 1);
				T.stop();
			});
			
			//������� �� ������
			$(".frame", T.el).click(function(){
				location.href = T.data[T.current_slide].link;
			});
			
			(T.play_pause = $(".play_pause", T.el)).click(function(){
				if (T.playing) {
					T.stop();
				} else {
					T.start();
				}
			});
			T.show(0);
			T.start();
		} else {
			T.show_error();
		}
	},
	
	/**
	 * ��������� ������������ � ��������� ��������. ���� delay �� ������, ����� ����������� �� ���������
	 */
	start: function (delay) {
		var T = this;
		
		// ��������� ����� ��������
		if (delay) {
			T.delay = delay;
		}
		
		if (T.playing) {
			return;
		}
		T.change_timer = setInterval(function(){
			T.next.call(T);
		}, T.delay);

		T.playing = true;

		T.play_pause.removeClass("paused");
	},
	
	/**
	 * ��������� �������������
	 */
	stop: function () {
		var T = this;
		clearInterval(T.change_timer);
		T.playing = false;
		T.play_pause.addClass("paused");
	},
	
	/**
	 * ����� ������ � ������� 
	 * @param {Number} slide
	 */
	show: function (slide) {
		var T = this;
		
		/* ���� ������ �� ��� �� �����, ������� */
		if (T.current_slide == slide) {return};
		if (T.running) {
			return
		};
		
		var start_after_show = false; 
		if (T.playing) {
			start_after_show = true;
			T.stop();
		}
		
		var old = $(".control_overlay li:eq(" + T.current_slide + ")", T.el);

		T.current_slide = slide;
		var li = $(".control_overlay li:eq(" + slide +")", T.el);
		var image_url = "url('" + T.url + T.data[slide].image + "')";
		 
		$(old).removeClass("active");
		$(li).addClass("active");
		
		T.next_frame.style.display = "none";
		T.next_frame.style.backgroundImage = image_url; 
		
		T.running = true;
		$(T.next_frame).fadeIn("slow", function(){
			T.current_frame.style.backgroundImage = image_url;
			T.next_frame.style.display = "none"
			T.running = false;
		});
		
		if (start_after_show) {
			T.start();
		}
	},
	
	show_error: function (message) {
		var m = "������ �������� ������";
		if (message) {
			m += ":<br/>" + message;
		}
		this.el.innerHTML = m;
	},
	
	next: function () {
		this.show((this.current_slide + this.data.length + 1) % this.data.length);
	},
	
	prev: function () {
		this.show((this.current_slide + this.data.length - 1) % this.data.length);
	}
}
