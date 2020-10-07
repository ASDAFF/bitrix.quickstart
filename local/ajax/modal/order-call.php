<!-- Modal -->
<div class="modal fade order-call" id="order-call" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"></span>
                </button>
                <h4>Заказать звонок</h4>
            </div>
            <form name="CALLBACK_MODAL" action="<?= PATH_AJAX ?>" method="POST" role="form">
                <input type="hidden" name="CALLBACK_MODAL[SITE_ID]" value="<?= SITE_ID ?>"/>
                <input type="hidden" name="CALLBACK_MODAL[TITLE]" value="Обратный звонок"/>
                <div class="modal-body">
                    <div id="results-callback-modal">
                        <div class="alert alert-danger" id="beforesend-callback-modal">
                            Пожалуйста заполните все поля.
                        </div>
                        <div class="alert alert-danger" id="error-callback-modal">Ошибка отправки формы.</div>
                        <div class="alert alert-success" id="success-callback-modal">Мы получили Вашу заявку и скоро перезвоним.</div>
                    </div>

                    <div class="clearfix"><img src="/local/ajax/images/loading.gif" alt="Loading"
                                               id="form-loading-callback-modal" class="pull-right"/></div>
                    <input type="text" name="CALLBACK_MODAL[NAME]" class="inp req" placeholder="Ваше имя *">
                    <input type="tel" name="CALLBACK_MODAL[PHONE]" pattern="(([ ]*[\+]?[ ]*\d{1,5})[ ]*[\-]?[ ]*)?(\(?\d{1,5}\)?[ ]*[\-]?[ ]*)?[\d\- ]{5,13}" class="inp req" placeholder="Телефон *">
                    <div class="wrp-bttn">
                        <button type="submit" class="btn btn-submit">Отправить</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>