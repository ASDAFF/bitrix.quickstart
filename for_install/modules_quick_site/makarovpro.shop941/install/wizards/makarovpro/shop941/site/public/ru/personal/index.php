<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("������������ ������");
?> 
<div class="inner-content"> 		 
  <p>� ������ �������� �� ������ ��������� ������� ��������� �������, ��� ���������� ����� �������, ����������� ��� �������� ������ ����������, � ����� ����������� �� ������� � ������ �������������� ��������. </p>
 	 
  <div> 		 
    <h2>������ ����������</h2>
   		<a href="profile/" >�������� ��������������� ������</a> 	</div>
 	 
  <div> 		 
    <h2>������</h2>
   		<a href="order/" >������������ � ���������� �������</a> 
    <br />
   		<a href="cart/" >���������� ���������� �������</a> 
    <br />
   		<a href="order/?filter_history=Y" >���������� ������� �������</a> 
    <br />
   	</div>
 	 
  <div> 		 
    <h2>��������</h2>
   		<a href="subscribe/" >�������� ��������</a> 	</div>
 </div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>