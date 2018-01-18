Likeit = {
	list: [],
	class: 'vs-likeit',
	classActive: 'vs-likeit-active',
	classCnt: 'vs-likeit-cnt',
	classAction: 'vs-likeit-action',
	onLike: function (data) {
		if (data.RESULT > 0) {
			for (j = 0; j < Likeit.list[data.ID].length; ++j) {
				var el = Likeit.list[data.ID][j];

				var cnt = BX.findChildByClassName(el, Likeit.classCnt);
				if (data.RESULT == 1) {
					BX.addClass(el, Likeit.classActive);
					if (!!cnt) {
						cnt.innerText = parseInt(cnt.innerText) + 1;
					}
				} else {
					BX.removeClass(el, Likeit.classActive);
					if (!!cnt) {
						cnt.innerText = parseInt(cnt.innerText) - 1;
					}
				}
			}
		}
	},
	onClick: function () {
		var el = BX(this), id = el.getAttribute('dataid') || 0;
		if (id > 0) {
			BX.ajax.loadJSON('/bitrix/tools/vasoft_likeit_like.php', {ID: id}, Likeit.onLike);
		}
		return false;
	},
	init: function () {
		var elements = document.querySelectorAll('.' + Likeit.class), i, ids = [];
		if ('vas_likeit_classactive' in window && window.vas_likeit_classactive != '') {
			Likeit.classActive = window.vas_likeit_classactive;
		}
		if ('vas_likeit_classcnt' in window && window.vas_likeit_classcnt != '') {
			Likeit.classCnt = window.vas_likeit_classcnt;
		}
		Likeit.list = [];
		for (i = 0; i < elements.length; ++i) {
			var el = BX(elements[i]), id = el.getAttribute('dataid') || 0;
			BX.unbind(el, 'click', Likeit.onClick);
			if (id > 0) {
				ids.push(id);
				if (BX.hasClass(el, Likeit.classAction)) {
					BX.bind(el, 'click', Likeit.onClick);
				}
				if (id in Likeit.list) {
					Likeit.list[id].push(el);
				} else {
					Likeit.list[id] = [el];
				}
			}
		}
		if (ids.length > 0) {
			BX.ajax.loadJSON('/bitrix/tools/vasoft_likeit_list.php', {IDS: ids}, Likeit.onList);
		}
	},
	onList: function (data) {
		if (data.RESULT > 0) {
			var i, j;
			for (i = 0; i < data.ITEMS.length; ++i) {
				if (data.ITEMS[i].ID in Likeit.list) {
					for (j = 0; j < Likeit.list[data.ITEMS[i].ID].length; ++j) {
						var el = Likeit.list[data.ITEMS[i].ID][j];
						if (data.ITEMS[i].CHECKED > 0) {
							BX.addClass(el, Likeit.classActive);
						}
						var cnt = BX.findChildByClassName(el, Likeit.classCnt);
						if (!!cnt) {
							cnt.innerText = data.ITEMS[i].CNT;
						}
					}
				}
			}
		}
	}
};
BX.ready(function () {
	Likeit.init();
});