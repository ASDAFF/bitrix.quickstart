BX.namespace('RS');

if (!window.RSLocation) {

  window.RSLocation = (function() {

    var Location = function(data, params) {
      this.data = data;
      this.params = params;
    }

    BX.merge(Location.prototype, {
      getCityId: function () {
        return this.data['ID'];
      },
      
      getCityName: function() {
        return this.data['NAME'];
      },

      change: function(id) {
        var data = {
          action: 'change',
          id: id,
          siteId: this.params.siteId
        };

        return this.request(data).then(BX.proxy(this.changeSuccess, this));
      },

      changeSuccess: function(data) {
        this.data = data;

        BX.onCustomEvent('rs.location_change');

        return data;
      },

      request: function(data) {
        var p = new BX.Promise();

        data['sessid'] = BX.bitrix_sessid();

        BX.ajax({
          url: this.params.ajaxUrl,
          method: 'POST',
          data: data,
          dataType: 'json',
          onsuccess: function (result) {
            p.resolve(result);
          },
          onfailure: function (err) {
            p.reject(err);
          }
        });

        return p;
      }
    });

    return Location;
  }());
}
