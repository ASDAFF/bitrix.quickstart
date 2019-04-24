//@TODO...
var CustomProp = {
    arParams: null,
    arElements: null,
    btn: null,
    path: '/local/components/lema/form.ajax',
    formObj: null,
    init: function(arParams) {

        var _this = this;

        _this.arParams = arParams;

        _this.path = _this.arParams.propertyParams.CUSTOM_DIR;

        _this.btn = $('<a href="#" class="set_fields">Указать поля</a>');

        $.get(_this.path + '/custom/custom.php', {data: _this.arParams.data}, function(ans) {
            _this.formObj = $(ans);
        });

        _this.arElements = _this.arParams.getElements();

        $(_this.arParams.oCont)
            .append(_this.btn);

        _this.btn.on('click', function(e) {
            e.preventDefault();
            var popup = new BX.PopupWindow('form_fields_popup', null, {
                content: $(_this.formObj).html(),
                closeIcon: {right: '20px', top: '10px'},
                closeByEsc: true,
                titleBar: {content: BX.create('span', {html: 'Добавление полей к форме', 'props': {'className': 'access-title-bar'}})},
                zIndex: 0,
                offsetLeft: 0,
                offsetTop: 0,
                draggable: {restrict: false},
                overlay: {backgroundColor: 'black', opacity: '80' },  /* затемнение фона */
                buttons: [
                    new BX.PopupWindowButton({
                        text: 'Добавить еще поле',
                        className: 'popup-window-button-accept',
                        events: {
                            click: function(){
                                var copyBlock = $('.form-ajax-custom_fields').find('.copy-block').first().clone();
                                copyBlock.css('display', 'block');
                                $('.form-ajax-custom_fields').append(copyBlock);
                                $('.form-ajax-delete-field').on('click', function(e) {
                                    e.preventDefault();
                                    $(this).closest('.copy-block').remove();
                                })
                            }
                        }
                    }),
                    new BX.PopupWindowButton({
                        text: 'Закрыть окно',
                        className: 'webform-button-link-cancel',
                        events: {
                            click: function(){
                                var res = [];
                                $('.copy-block').each(function(i, el) {
                                    if(!$(el).is(':visible') || !$.trim($(el).find('[name^="name"]').val()).length)
                                        return ;

                                    res.push({
                                        name: $(el).find('[name^="name"]').val(),
                                        type: $(el).find('[name^="type"]').val(),
                                        title: $(el).find('[name^="title"]').val(),
                                        placeholder: $(el).find('[name^="placeholder"]').val(),
                                        default: $(el).find('[name^="default"]').val(),
                                        required: ($(el).find('[name^="required"]').is(':checked') ? 'Y' : 'N')
                                    })
                                });
                                _this.arParams.oInput.value = JSON.stringify(res);
                                this.popupWindow.close(); // закрытие окна
                            }
                        }
                    })
                ]
            });
            popup.show();

            $('.form-ajax-delete-field').on('click', function(e) {
                e.preventDefault();
                $(this).closest('.copy-block').remove();
            })
        });
    },
};

function showCustom(arParams)
{
    CustomProp.init(arParams);
}