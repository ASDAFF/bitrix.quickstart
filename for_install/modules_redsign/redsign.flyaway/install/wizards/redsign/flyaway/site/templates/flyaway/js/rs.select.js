;(function($, window, document, undefined) {
    'use strict';
    
    var pluginName = 'redsignSelect',
        namespace = 'redsign.select',
        defaultOptions = {
            customSelect: true,
            wrapClass: 'dropdown quantity-select js-quantity-dropdown',
            
            templates: {
                button: '<button \n\
                            class="btn btn-default dropdown-toggle"\n\
                            data-toggle="dropdown" aria-expanded="false" type="button">\n\
                            <span class="js-select-value"> {text} </span> <i class="fa fa-angle-down"></i>\n\
                            </button>',
      
                list: '<ul class="dropdown-menu views-box drop-panel"\n\
                       role="menu" aria-labelledby="dLabel">{items}</ul>',
      
                item: '<li class="views-item" ><a class="js-option" href="#" data-value="{value}">{item}</a></li>'
            }
        };
        
    
    function Select(element, options) {
      
      this.element = element;
      this.$element = $(element);
      this.$customElement = undefined;
      
      this.value = this.$element.children('option:selected').val();
      
      this.options = $.extend({}, defaultOptions, options);
      
      this.init();
      
    }
    
    $.extend(Select.prototype, {
      
        init: function() {
            
            if(this.options.customSelect) {
                this.customizeSelect();
                this.listenCustomizeEvents();
            }
            
        },
        
        customizeSelect: function() {
          
            var button = '';
            if($.type(this.options.templates.button) === "string") {
              button = this.options.templates.button.replace(/\{text\}/g, this.$element.children('option:selected').text());
            }
            
            var items = '';
            this.$element.children('option').each($.proxy(function(key, item) {
                items += this.options.templates.item.replace(/\{item\}/g, $(item).text())
                                                    .replace(/\{value\}/g, $(item).val());
            }, this));

            var list = '';
            if($.type(this.options.templates.list) === "string") {
              list = this.options.templates.list.replace(/\{items\}/g, items);
            }
            
          
            this.$customElement = $('<div></div>')
                                      .hide()
                                      .addClass(this.options.wrapClass)
                                      .append(button)
                                      .append(list);
                             
            
            this.$element.after(this.$customElement);
            this.$element.hide();
            this.$customElement.show();
        },
        
        listenCustomizeEvents: function() {
            var ctx = this;
            
            this.$customElement.find(".js-option").on('click', $.proxy(function(e) {
                e.preventDefault();
                
                var $target = $(e.target);
                this.setValue($target.data('value'), $target.text());
                this.$element.change();
            }, this));
        },
        
        
        getValue: function() {
            return this.value;
        },
        
        setValue: function(value, name) {
            this.value = value;
            
            this.$element.find('option').removeAttr('selected');
            this.$element.find('option[value="' + value + '"]').attr('selected', 'selected');
            if(this.$customElement) {
                this.$customElement.find(".js-select-value").text(name);
            }
        }
        
    });
  
    $.fn[pluginName] = function(options) {
        return this.each(function() {
            if(!$.data(this, namespace)) {
                $.data(this, namespace, new Select(this, options));
            }
        });
    }
})(jQuery, window, document);