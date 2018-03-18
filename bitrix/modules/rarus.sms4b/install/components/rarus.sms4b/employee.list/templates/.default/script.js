function addNumber(userobject, textAreaID)
{  
	var textareaObj = document.getElementById(textAreaID);
	
	if (userobject.phone != '')
	{
		textareaObj.value += userobject.phone+'<' + userobject.name + ', ' + userobject.department + '>'+'\n';
	}
	
	getTelNumber(textAreaID, 'smsNumber');
	Counters('message', 'text-length', 'part-size', 'parts', 'need-sms', 'dest', 'smsNumber');
}