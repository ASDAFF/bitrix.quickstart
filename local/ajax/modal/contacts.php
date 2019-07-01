<!-- Modal -->
<div class="modal fade contacts-form" id="contacts-form" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"></span>
                </button>
                <h4>Обратная связь</h4>
            </div>
            <form name="CONTACTS_MODAL" action="<?= PATH_AJAX ?>" method="POST" role="form">
                <input type="hidden" name="CONTACTS_MODAL[SITE_ID]" value="<?= SITE_ID ?>"/>
                <div class="modal-body">
                    <div id="results-contacts-modal">
                        <div class="alert alert-danger" id="beforesend-contacts-modal">
                            Пожалуйста заполните обязательные поля.
                        </div>
                        <div class="alert alert-danger" id="error-contacts-modal">
                            Ошибка отправки сообщения.
                        </div>
                        <div class="alert alert-success" id="success-contacts-modal">
                            Спасибо за обращение. В ближайшее время с вами свяжутся, для уточнения деталей.
                        </div>
                    </div>
                    <img src="/local/codenails/ajax/images/loading.gif" alt="Loading" id="form-loading-contacts-modal"
                         class="pull-right"/>
                    <div class="clearfix"></div>
                    <input type="text" name="CONTACTS_MODAL[NAME]" class="inp req" placeholder="Ваше имя *">
                    <input type="tel" class="inp req" placeholder="Телефон *" name="CONTACTS_MODAL[PHONE]" pattern="(([ ]*[\+]?[ ]*\d{1,5})[ ]*[\-]?[ ]*)?(\(?\d{1,5}\)?[ ]*[\-]?[ ]*)?[\d\- ]{5,13}">
                    <input type="text" name="CONTACTS_MODAL[TITLE]" class="inp" placeholder="Тема">
                    <textarea name="CONTACTS_MODAL[COMMENT]" placeholder="Комментарий"></textarea>
                    <div class="wrp-bttn">
                        <button type="submit" class="btn btn-submit">Отправить</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>