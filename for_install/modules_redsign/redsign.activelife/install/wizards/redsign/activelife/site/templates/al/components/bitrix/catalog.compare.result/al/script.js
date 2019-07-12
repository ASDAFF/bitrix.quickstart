BX.namespace("BX.Iblock.Catalog");

BX.Iblock.Catalog.CompareClass = (function()
{
  var CompareClass = function(wrapObjId)
  {
    this.wrapObjId = wrapObjId;
    this.setRhythm();

    BX.data(BX(this.wrapObjId), "cmpRes", this);

    $(window).on('resize', BX.debounce($.proxy(this.setRhythm, this), 250));

    BX.addCustomEvent(window, "OnCompareChange", BX.proxy(function(bRefresh)
    {
      if (!this.refreshed)
      {
        this.MakeAjaxAction(window.location.href, bRefresh);
      }
    }, this));
  };

  CompareClass.prototype.MakeAjaxAction = function(url, bRefresh)
  {
    var wrap = BX(this.wrapObjId);
    $(wrap).rsToggleDark({progress: true, progressTop: '100px'});
    BX.ajax.post(
      url,
      {
        ajax_action: 'Y',
        ajax_id: this.wrapObjId
      },
      BX.proxy(function(result)
      {
        if (!bRefresh)
        {
          this.refreshed = true;
          BX.onCustomEvent('OnCompareChange', [true]);
        }
        $(wrap).rsToggleDark();
        wrap.innerHTML = result;
        this.setRhythm();
        delete appSLine.compareList;
        if($(wrap).find('.js_element').length>0) {
          $('.js_compare__total').html('(' + $(wrap).find('.js_element').length + ')');
          appSLine.compareList = {};
          $(wrap).find('.js_element').each(function(){
            var iProductId = $(this).data('product-id');
            if(iProductId>0) {
              appSLine.compareList[iProductId] = true;
            }
          });
        } else {
          appSLine.compareList = {};
          $('.js_compare__total').html('(0)');
        }
        appSLine.setProductItems();
        //var cmp = BX.findChildrenByClassName(document, 'js_compare__total', true);
        //for (var i in cmp)
        //{
        //  cmp[i].innerHTML = '('+ BX.findChildrenByClassName(BX.findChild(BX.findChildByClassName(wrap, 'cmp_page__table', true), {'tag': 'tr'}, true), 'js_element').length +')';
        //}
        this.refreshed = false;
      }, this)
    );
  };

  CompareClass.prototype.setRhythm = function()
  {
    var wrapObj = BX(this.wrapObjId),
      tableItems = BX.findChild(wrapObj , {'class': 'cmp_page__table'}, true),
      rowsData =  BX.findChildren(tableItems, {'tag': 'tr'}, true);
      rowsName =  BX.findChildren(BX.findChild(wrapObj , {'class': 'cmp_page__names'}, true), {'tag': 'tr'}, true);

    if (!!rowsName && rowsName.length > 0
      && !!rowsData && rowsData.length)
    {
      var match = -1,
          responsive = {
            0: {
              items: 1
            }
          };

      responsive[appSLine.grid.xs] = {
        items: 3
      };
      responsive[appSLine.grid.sm] = {
        items: 3
      };
      responsive[appSLine.grid.md] = {
        items: 3
      };
      responsive[appSLine.grid.lg] = {
        items: 4
      };

      for (var breakpoint in responsive)
      {

        breakpoint = Number(breakpoint);

        if (breakpoint <= appSLine.pageWidth && breakpoint > match) {
          match = breakpoint;
        }
      }

      tableItems.style.width = (100 * BX.findChildren(rowsData[0], {'tag': 'td'}, true).length / responsive[match].items) + '%';
      for (i = 0; rowsName.length > i; i++)
      {
        rowsName[i].style.height = rowsData[i].style.height = 'auto';
        if(rowsData[i].offsetHeight > rowsName[i].offsetHeight)
        {
          rowsName[i].style.height = rowsData[i].offsetHeight + 'px';
        }
        else
        {
          rowsData[i].style.height = rowsName[i].offsetHeight + 'px';
        }
      }
            /*
            $picbox.find('.picbox__scroll').scrollbar({
              showArrows: true,
              scrollx: $picbox.find('.picbox__bar'),
              scrollStep: 104
            });
            */
    }
  }

  return CompareClass;
})();