<?php

abstract class CVCSAjaxException extends Exception {

	public abstract function GetType();
}

class CVCSAjaxExceptionSystemError extends CVCSAjaxException {

	public function GetType() {
		return 'system';
	}
}

class CVCSAjaxExceptionServiceError extends CVCSAjaxException {

	public function GetType() {
		return 'service';
	}
}

class CVCSAjaxExceptionAuthError extends CVCSAjaxException {

	public function GetType() {
		return 'auth';
	}
}
