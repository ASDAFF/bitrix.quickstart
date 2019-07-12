;(function ($, BX, document, window, undefined) {
    'use strict';
    
    var elementsCache = {};
    
    function getOfferByValues(values, offers) {
      
        if(window.Object.keys(values).length == 0) {
            return offers[0];
        }
      
        var code, selectedOffers = [];
        
        for(code in values) break;
        
        selectedOffers = getOfferByValue(code, values[code], offers);
        delete values[code];
        
        if(selectedOffers.length > 0) {
            offers = selectedOffers;
        }
      
        return getOfferByValues(values, offers);
    }
    
    function getOfferByValue(offerCode, offerValue, offers) {
          var returnOffers = [],
              i;
        
        for(i in offers) {
          
            if(!offers.hasOwnProperty(i)) {
                continue;
            }
            
            if(
                offers[i]['PROPERTIES'][offerCode] && offerValue &&
                offers[i]['PROPERTIES'][offerCode] == offerValue
            ) {
                returnOffers.push(offers[i]);
            }
          
        }
        
        return returnOffers;
    }
    
    
    function CatalogElement(id, data, url) {
        this.id = id;
        this.isReady = false;
        this.item = null;
        this.url = url || this.getDetailPageUrl();
        this.selectedOffer = undefined;  
        
        this.init(data);
    }
    
    CatalogElement.prototype.init = function(data) {
        
        if (data) {
            this.item = data;
            this.isReady = true;
        } else {            
            this.load();
        }
    };
    
    CatalogElement.prototype.load = function() {
      
        this.isReady = false;
        
        var requestData = {
            AJAX_CALL: "Y",
            action: 'get_element_json',
            element_id: this.id
        };
        
        return $.post(this.url, requestData)
            .then($.proxy(function(data) {
                this.item = BX.parseJSON(data);
                this.isReady = true;
                
                return this;
            }, this));
    };
    
    CatalogElement.prototype.getDetailPageUrl = function() {
        if(this.url) {
            return this.url;
        }
        
        return $(".js-element[data-elementid=" + this.id + "]").data('detailpageurl');
    };
    
    CatalogElement.prototype.getProductId = function() {
        return this.selectedOffer || this.id;
    }
    
    CatalogElement.prototype.ready = function(callback) {
      
        callback = callback || function() {};
        
        if(this.isReady) {
            callback.call(this);
            return;
        }
        
        var interval = window.setInterval($.proxy(function() {
            if(this.isReady) {
               clearInterval(interval);
               callback.call(this);
            }
        }, this), 250);
        
    };
    
    CatalogElement.prototype.selectOfferById = function(offerId) {
        this.selectedOffer = offerId;
    };
    
    CatalogElement.prototype.selectOfferByValues = function(values) {
        this.selectOfferById(
            getOfferByValues(values, this.item.OFFERS).ID
        );
    };
    
    CatalogElement.prototype.getOffer = function() {
        return this.item ? this.item.OFFERS[this.selectedOffer] : undefined;
    };
    
    CatalogElement.prototype.canBuy = function() {
        var offer = this.getOffer();
        
        if(!offer) {
            return true;
        }
        
        return offer.CAN_BUY;
    };
    
    CatalogElement.prototype.getMeasureInfo = function() {
          var offer = this.getOffer();
          
          if(!offer) {
            return true;
          }
          
          return {
              name: offer.CATALOG_MEASURE_NAME,
              ratio: offer.CATALOG_MEASURE_RATIO
          };
    };
    
    CatalogElement.prototype.getPrices = function() {
        var offer = this.getOffer(),
            prices = [],
            code;
        
        if(!offer) {
            return;
        }
        
        for(code in offer.PRICES) {
            prices.push({
                title: this.item.CAT_PRICES[code].TITLE,
                printValue: offer.PRICES[code].PRINT_VALUE,
                printDiscount: offer.PRICES[code].PRINT_DISCOUNT,
                printDiscountValue: offer.PRICES[code].PRINT_DISCOUNT_VALUE,
                hasDiscount: !!parseInt(offer.PRICES[code].DISCOUNT_DIFF)
            });
        }
        
        return prices;
    };
    
    CatalogElement.prototype.getPictures = function(i) {
        var offer = this.getOffer();
        
        if(!offer) {
            return;
        }
        
        return i ? offer.IMAGES[i - 1] : offer.IMAGES;
    };
    
    CatalogElement.prototype.getSkuProperties = function() {
        var offer = this.getOffer();
        return offer ? offer.PROPERTIES : undefined;
    };
    
    CatalogElement.prototype.getName = function() {
        var offer = this.getOffer();
        return offer ? offer.NAME : null;
    };
    
    CatalogElement.prototype.getQuantity = function() {
        var offer = this.getOffer();
        return offer ? offer.QUANTITY : 0;
    };
    
    CatalogElement.prototype.getEnabledOfferValues = function() {
      
        if(!this.item.OFFERS || !this.selectedOffer) return;
      
        var offers = this.item.OFFERS,
            props = this.item.SORT_PROPS,
            selectedOffer = this.getOffer(),
            enabledProperties = [],
            properties,
            childOffers,
            spKey,
            oKey,
            pMatches;
    
        pMatches = function(props, propsValues) {
            var isMatch = true,
                isOneMatch,
                i;
        
            for(i in propsValues) {
                isOneMatch = true;
                
                propsValues[i].forEach(function(propValue) {
                    if(props[i] != propValue) {
                        isOneMatch;
                    }
                });
                
                if(isOneMatch) isMatch = true;
            }
            
            return isMatch;
        };
    
        for(spKey in props) {
          
            childOffers = [];
            properties = [];
            
            if(!selectedOffer.PROPERTIES[props[spKey]]) continue;
            
            
            for(oKey in offers) {
                if(selectedOffer.PROPERTIES[props[spKey]] == offers[oKey].PROPERTIES[props[spKey]]) {
                    childOffers.push(offers[oKey]);
                }
                
                if(
                    (pMatches(offers[oKey].PROPERTIES, enabledProperties) || spKey == 0) &&
                     $.inArray(offers[oKey].PROPERTIES[props[spKey]], properties) === -1
                ) {
                    properties.push(offers[oKey].PROPERTIES[props[spKey]]);
                }
                
                
            }
            
            
            offers = childOffers;
            enabledProperties[props[spKey]] = properties;;
        }
        
        return enabledProperties;
    };

    window.CatalogElement = function(id, data, url) {
       
       if(!id) {
          return;
       }
       
       if(!elementsCache[id]) {
          elementsCache[id] = new CatalogElement(id, data, url); 
       }
       
       return elementsCache[id];
    };
    
}(jQuery, BX, document, window));