<?php


class queryOutBoxMessages
{
	public $MeditabID;
	public $MeditabTranID;
	public $FromID;
	public $ToID;
	public $MessageStatus;
	public $MeditabResponseStatus;
	public $SentTimeFrom;
	public $SentTimeTo;
}

class Response
{
	public $out;
	public $Error;
	public $Success;
}

class Request
{
	public $in;
}

class ErrorType
{
	public $Code;
	public $Message;
}

class SuccessType
{
	public $Code;
	public $Message;
}

class ExtendedStatus
{
	public $Status;
	public $Succeeded;
	public $Duplicate;
	public $Failed;
}

?>