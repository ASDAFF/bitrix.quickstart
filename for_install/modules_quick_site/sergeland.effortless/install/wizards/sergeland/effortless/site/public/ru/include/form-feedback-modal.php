<div class="modal fade FEEDBACK" tabindex="-1" role="dialog" aria-labelledby="FLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">�������</span></button>
				<h4 class="modal-title" id="FLabel">������ ������</h4>
			</div>
			<form name="FEEDBACK_MODAL" action="#SITE_DIR#include/" method="POST" role="form">
				<input type="hidden" name="FEEDBACK_MODAL[SITE_ID]" value="<?=SITE_ID?>"/>
				<input type="hidden" name="FEEDBACK_MODAL[TITLE]" value="������ ������"/>
				<div class="modal-body">
					<p><i class="icon-check"></i> ���������� ��������� ��� ����. ��� ���������� �������� � ���� � ��������� �����.</p>
					<div id="results-feedback-modal">
						<div class="alert alert-danger" id="beforesend-feedback-modal">
							���������� ��������� ��� ����.
						</div>
						<div class="alert alert-danger" id="error-feedback-modal">
							������ �������� ���������.
						</div> 
						<div class="alert alert-success" id="success-feedback-modal">
							�������, ���� ��������� ���������� ������������� �����.
						</div>
					</div>
					<img src="#SITE_DIR#images/loading.gif" alt="Loading" id="form-loading-feedback-modal" class="pull-right mb-10" />
					<div class="clearfix"></div>
					<div class="form-group has-feedback">
						<input type="text" class="form-control req" name="FEEDBACK_MODAL[NAME]" placeholder="���">
						<i class="fa fa-user form-control-feedback"></i>
					</div>
					<div class="form-group has-feedback">
						<input type="email" class="form-control req" name="FEEDBACK_MODAL[EMAIL]" placeholder="Email">
						<i class="fa fa-envelope form-control-feedback"></i>
					</div>
					<div class="form-group has-feedback">
						<textarea rows="4" class="form-control req" name="FEEDBACK_MODAL[COMMENT]" placeholder="���������"></textarea>
						<i class="fa fa-pencil form-control-feedback"></i>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-sm btn-default"><i class="icon-check"></i>���������</button>
				</div>
			</form>
		</div>
	</div>
</div>