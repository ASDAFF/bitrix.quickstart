<?php

$checkStatus = ' <a href="http://triggmine.cloudapp.net/" target="_blank">Please check status of your account</a> or contact <a href="http://triggmine.cloudapp.net/Support" target="_blank">support</a>';
$contactSupport = ' Please contact <a href="http://triggmine.cloudapp.net/Support" target="_blank">TriggMine support</a>.';
$getApiUrl = ' You can find correct API Url in <a href="http://triggmine.cloudapp.net/Integration" target="_blank">TriggMine integration settings</a>';
$getApiKey = ' You can find correct API Key in <a href="http://triggmine.cloudapp.net/Integration" target="_blank">TriggMine integration settings</a>';

$MESS['failed_to_activate'] = 'Failed to activate TriggMine module.' . $checkStatus;
$MESS['failed_to_deactivate'] = 'Failed to deactivate TriggMine module.' . $checkStatus;
$MESS['low_version'] = 'TriggMine is not compatibe with Bitrix below v. %1';
$MESS['missing_module'] = 'Module %1 is not installed';
$MESS['missing_function'] = 'Function %1 is not available';
$MESS['no_transport'] = 'Option \'allow_url_fopen\' is Off in your php.ini and cURL extenstion is not installed!' . $contactSupport;
$MESS['empty_api_url'] = 'API URL cannot be empty.' . $getApiUrl;
$MESS['invalid_api_url'] = '%1 is not a valid API URL' . $getApiUrl;
$MESS['empty_api_key'] = 'API Key cannot be empty. ' . $getApiKey;
$MESS['no_access_to_api'] = 'No access to API endpoint %1. Please check that API URL is valid.' . $getApiUrl;
$MESS['invalid_response_from_api'] = 'API endpoint %2 is not accessible.' . $contactSupport;
$MESS['invalid_token'] = 'Invalid API Key is used. Please check your API Key.' . $getApiKey;
$MESS['api_returns_error'] = 'API returned an error (%1).' . $contactSupport;
$MESS['plugin_cannot_be_active'] = 'Module cannot be activated';
$MESS['plugin_can_be_active'] = 'Module is Off now. Switch it to On to start working.';
$MESS['wrong_cart_url'] = 'Cart URL is invalid';









