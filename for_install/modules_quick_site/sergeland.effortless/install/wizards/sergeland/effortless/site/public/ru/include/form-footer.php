<h3>������ ������</h3>
<p>���������� ��������� ��� ����. ��� ���������� �������� � ���� � ��������� �����.</p>
<form name="FEEDBACK" action="#SITE_DIR#include/" method="POST" role="form">
	<div id="results-feedback">
		<div class="alert alert-danger" id="beforesend-feedback">
			���������� ��������� ��� ����.
		</div>
		<div class="alert alert-danger" id="error-feedback">
			������ �������� ���������.
		</div> 
		<div class="alert alert-success" id="success-feedback">
			�������, ���� ��������� ���������� ������������� �����.
		</div>
	</div>
	<img src="#SITE_DIR#images/loading.gif" alt="Loading" id="form-loading-feedback" class="pull-right mb-10" />
	<div class="clearfix"></div>								
	<input type="hidden" name="FEEDBACK[SITE_ID]" value="<?=SITE_ID?>"/>
	<input type="hidden" name="FEEDBACK[TITLE]" value="������ ������"/>
	<div class="form-group has-feedback">
		<input type="text" class="form-control req" placeholder="���" name="FEEDBACK[NAME]">
		<i class="fa fa-user form-control-feedback"></i>
	</div>
	<div class="form-group has-feedback">
		<input type="email" class="form-control req" placeholder="Email" name="FEEDBACK[EMAIL]">
		<i class="fa fa-envelope form-control-feedback"></i>
	</div>
	<div class="form-group has-feedback">
		<textarea class="form-control req" rows="4" placeholder="���������" name="FEEDBACK[COMMENT]"></textarea>
		<i class="fa fa-pencil form-control-feedback"></i>
	</div>
	<input type="submit" value="���������" class="btn btn-white pull-right">
</form>