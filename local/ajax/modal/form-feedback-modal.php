<div class="modal fade FEEDBACK" tabindex="-1" role="dialog" aria-labelledby="FLabel" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                            class="sr-only">Закрыть</span></button>
                <h4 class="modal-title" id="FLabel">Задать вопрос</h4>
            </div>

            <form name="FEEDBACK_MODAL" action="<?= PATH_AJAX ?>" method="POST" role="form">
                <input type="hidden" name="FEEDBACK_MODAL[SITE_ID]" value="<?= SITE_ID ?>"/>
                <input type="hidden" name="FEEDBACK_MODAL[TITLE]" value="Задать вопрос"/>
                <div class="modal-body">
                    <p><i class="icon-check"></i> Пожалуйста заполните все поля. Наш специалист свяжется с вами в
                        ближайшее время.</p>
                    <div id="results-feedback-modal">
                        <div class="alert alert-danger" id="beforesend-feedback-modal">
                            Пожалуйста заполните все поля.
                        </div>
                        <div class="alert alert-danger" id="error-feedback-modal">
                            Ошибка отправки сообщения.
                        </div>
                        <div class="alert alert-success" id="success-feedback-modal">
                            Спасибо, ваше сообщение отправлено администрации сайта.
                        </div>
                    </div>
                    <img src="/images/loading.gif" alt="Loading" id="form-loading-feedback-modal"
                         class="pull-right mb-10"/>
                    <div class="clearfix"></div>
                    <div class="form-group has-feedback">
                        <input type="text" class="form-control req" name="FEEDBACK_MODAL[NAME]" placeholder="Имя">
                        <i class="fa fa-user form-control-feedback"></i>
                    </div>
                    <div class="form-group has-feedback">
                        <input type="email" class="form-control req" name="FEEDBACK_MODAL[EMAIL]" placeholder="Email">
                        <i class="fa fa-envelope form-control-feedback"></i>
                    </div>
                    <div class="form-group has-feedback">
                        <textarea rows="4" class="form-control req" name="FEEDBACK_MODAL[COMMENT]"
                                  placeholder="Сообщение"></textarea>
                        <i class="fa fa-pencil form-control-feedback"></i>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-default"><i class="icon-check"></i>Отправить</button>
                </div>
            </form>
        </div>
    </div>
</div>