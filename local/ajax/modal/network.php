<!-- Modal -->
<div class="modal fade consultation" id="network-form" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"></span>
                </button>
                <h4>�������� � ����</h4>
            </div>
            <form name="NETWORK" action="<?= PATH_AJAX ?>" method="POST" role="form">
                <input type="hidden" name="NETWORK[SITE_ID]" value="<?= SITE_ID ?>"/>
                <div class="modal-body">
                    <div id="results-network">
                        <div class="alert alert-danger" id="beforesend-network">
                            ���������� ��������� ������������ ����.
                        </div>
                        <div class="alert alert-danger" id="error-network">
                            ������ �������� ���������.
                        </div>
                        <div class="alert alert-success" id="success-network">
                            ������ �� ���������� � ���� ��������������. �������� ������ �����������.
                        </div>
                    </div>
                    <img src="/local/codenails/ajax/images/loading.gif" alt="Loading" id="form-loading-network"
                         class="pull-right"/>
                    <div class="clearfix"></div>
                    <input type="text" name="NETWORK[NAME]" class="inp req" placeholder="���� ��� *">
                    <input type="text" name="NETWORK[CITY]" class="inp" placeholder="��� �����">
                    <input type="tel" name="NETWORK[PHONE]" class="inp req" placeholder="������� *" pattern="(([ ]*[\+]?[ ]*\d{1,5})[ ]*[\-]?[ ]*)?(\(?\d{1,5}\)?[ ]*[\-]?[ ]*)?[\d\- ]{5,13}">
                    <div class="wrp-bttn">
                        <button type="submit" class="btn btn-submit">���������</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>