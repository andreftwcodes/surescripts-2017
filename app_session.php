<?php
session_id("MediAppX");
session_start();

echo session_id();
echo $_SESSION['app_status'];

class MedApplication
{
	private $strApp;

	public function __construct($strApp)
	{
		$this->strApp = $strApp;
	}
}