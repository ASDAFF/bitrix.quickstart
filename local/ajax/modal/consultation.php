<!-- Modal -->
<div class="modal fade consultation" id="consultation-form" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"></span>
                </button>
                <h4>Отправить сообщение</h4>
            </div>
            <form name="CALLBACK" action="<?= PATH_AJAX ?>" method="POST" role="form">
                <input type="hidden" name="CALLBACK[SITE_ID]" value="<?= SITE_ID ?>"/>
                <div class="modal-body">
                    <div id="results-callback">
                        <div class="alert alert-danger" id="beforesend-callback">
                            Пожалуйста заполните обязательные поля.
                        </div>
                        <div class="alert alert-danger" id="error-callback">
                            Ошибка отправки сообщения.
                        </div>
                        <div class="alert alert-success" id="success-callback">
                            Заявка принята. Ожидайте звонка эксперта.
                        </div>
                    </div>
                    <img src="/local/codenails/ajax/images/loading.gif" alt="Loading" id="form-loading-callback"
                         class="pull-right"/>
                    <div class="clearfix"></div>
                    <input type="text" name="CALLBACK[NAME]" class="inp req" placeholder="Ваше имя *">
                    <input type="tel" class="inp req" placeholder="Телефон *" name="CALLBACK[PHONE]" pattern="(([ ]*[\+]?[ ]*\d{1,5})[ ]*[\-]?[ ]*)?(\(?\d{1,5}\)?[ ]*[\-]?[ ]*)?[\d\- ]{5,13}">
                    <input type="text" name="CALLBACK[TITLE]" class="inp" placeholder="Тема">
                    <textarea name="CALLBACK[COMMENT]" placeholder="Комментарий"></textarea>
                    <div class="wrp-bttn">
                        <button type="submit" class="btn btn-submit">Отправить</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>