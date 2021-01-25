<?php

class MedGeneral
{
	private	$arrErrorList = array();
	private $arrMessageList = array();
	private $arrModuleName = array();
	public  $strProject    = "mem";
	

	function __construct()
	{
		$GLOBALS["objGeneral"] = $this;
		$this->loadSettings();
	}

	

	function getGeneralObject()
	{
		return $GLOBALS["objGeneral"];
	}

	
	function getProjectName()
	{
		return "mem";
	}


	
	function loadSettings()
	{

		$strTbl_Name	="settings";
		$strField_Names	=" var_name,var_value";
		$strWhere		="module_id='0' or module_id='".$this->getSession("module_id")."'";
		$rsSetting=MedPage::getRecords($strTbl_Name,$strField_Names,$strWhere,"","","",""); 
		for($intSetting=0;$intSetting<count($rsSetting);$intSetting++) 
		{
			$this->arrErrorList[$rsSetting[$intSetting]["var_name"]] = $rsSetting[$intSetting]["var_value"];
		}

		$strTbl_Name	="site_messages";
		$strField_Names	=" msg_code,msg_value";
		$strWhere		="module_id='".$this->getSession("module_id")."' or module_id='0' ";
		$rsMessage = MedPage::getRecords($strTbl_Name,$strField_Names,$strWhere,"","","",""); 
		for($intMess=0;$intMess<count($rsMessage);$intMess++) 
		{
			$this->arrMessageList[$rsMessage[$intMess]["msg_code"]] =$rsMessage[$intMess]["msg_value"];
		}

		$this->arrModuleName=array("","Patient","Doctor","Employer","Insurance");
	}

	

	function getMessage()
	{
		$strMessage = $_SESSION['strErrorMessage'];
		unset($_SESSION['strErrorMessage']);
		return $strMessage;
	}

	
	function getSiteMessage($strCode)
	{
		if (array_key_exists($strCode,$this->arrMessageList)) 
			return $this->arrMessageList[$strCode];
		else
			return $strCode;
	}

	
	function getSettings($strCode)
	{
		$objGeneral = MedGeneral::getGeneralObject();
		if (array_key_exists($strCode,$objGeneral->arrErrorList)) 
			return $objGeneral->arrErrorList[$strCode];
		else
			return $strCode;
	}

	

	function getSession($strVarName)
	{
		$strVar=MedGeneral::getProjectName()."_".$GLOBALS["prModuleId"].'ses'.$strVarName;
		return $_SESSION[$strVar];
	}

	
	function getSessionVarName($strVarName)
	{
		$strVar=MedGeneral::getProjectName()."_".$GLOBALS["prModuleId"].'ses'.$strVarName;
		return $strVar;
	}


	function getValueFromDB($strQuery,$strFieldName)
	{
		$dbObj = MedDB::getDBObject();
		@eval("\$strQuery = $strQuery;");
		$rs = $dbObj->executeSelect($strQuery);
		return $rs[0][$strFieldName];
	}

	

	function setSession($strVarName,$strValue)
	{
		$strVar=MedGeneral::getProjectName()."_".$GLOBALS["prModuleId"].'ses'.$strVarName;
		$_SESSION[$strVar] = $strValue;
	}

	

	function getCookie($strVarName)
	{
		$strVar=MedGeneral::getProjectName().'ck'.$strVarName;
		return $_COOKIE[$strVar];
	}

	

	function setCookie($strVarName,$strValue,$strTime)
	{
		$strVar=MedGeneral::getProjectName().'ck'.$strVarName;
	
		setcookie($strVar,$strValue,$strTime);

	}

	

	function setMessage($strMessage)
	{
		$_SESSION['strErrorMessage'] = $strMessage;
	}

	

	function setDisplayMessage($strAction)
	{
		switch(trim(strtoupper($strAction)))
		{
			case "A":
					$strMsgCode="REC_ADD_MSG";
					break;

			case "E":
					$strMsgCode="REC_MOD_MSG";
					break;

			case "D":
					$strMsgCode="REC_DEL_MSG";
					break;

			case "U" or "MU":
					$strMsgCode="REC_UPDT_MSG";
					break;

		}
		$this->setMessage($this->getSiteMessage($strMsgCode));
	}

	

	function raiseError($intErrorCode,$strErrorDesc="",$strPageName="",$strSolution="")
	{
		global $gErrorPath,$IMAGE_PATH;
		if($intErrorCode != NULL)
		{
			
			$strErrorPath = $this->getBackTrace();
			
			$strErrorMessage=$this->getSiteMessage($intErrorCode);
			
			require_once("med_errorpage.php");

			
			
			$strFilePath		=	$this->getSettings("ERROR_PATH");
			$strCurrentDate		=	date("d-M-y h-i A");
			$strFileName		=	$strCurrentDate."_".time().".html";
			$arrPathInfo		=	pathinfo($_SERVER['SCRIPT_NAME']);
			$strDirPath			=	$arrPathInfo['dirname'];
			$arrDirectory		=	explode("/",$strDirPath);
			$strUpperMostDirectory	=	$arrDirectory[1];

			
			@file_put_contents($strFilePath."/".$strUpperMostDirectory."/".$strFileName,$errorContent);

						

			
			switch(trim(strtoupper($this->getSettings("ERR_DESC"))))
			{
				case "YES" : case "TRUE" : case "1" : case "Y"  : case "T"  :
						echo $errorContent;
						break;
				default :
						echo $dbConnectError;

			}
			 
			if($this->getSettings("SEND_ERR_MAIL") == "Yes")
				$this->sendErrorMail($errorContent); 
			exit;
		}
	}

	
	function checkSession($intModuleID)
	{
		$intModule_id=MedGeneral::getSessionVarName('intModule_id');
		if((isset($_SESSION[$intModule_id])) && ($_SESSION[$intModule_id] == $intModuleID)) return true;
		else return false;
	}

	
	function sendErrorMail($strBody,$blnDbConnFail=false)
	{
		
		require_once("../base/mailer/class.phpmailer.php");
		
		$strSubject	=	"Error Reporting...";

		
		$strHeaders  =	"MIME-Version: 1.0\r\n";
		$strHeaders .=	"Content-type: text/html; charset=iso-8859-1\r\n";

		$objMail = new PHPMailer();
		$objMail->IsHTML(true);

		if($blnDbConnFail)
		{
			include("med_mail_constants.php");
			
			$strTo		=	GEN_ERROR_TO;
			
			$strHeaders .=	GEN_ADDITIONAL_HED;

			
			$objMail->Host		=	GEN_ERROR_HOST;
			$objMail->Mailer	=	GEN_ERROR_MAILER;
			$objMail->From		=	GEN_ERROR_FROM;
			$objMail->FromName	=	GEN_FROM_NAME;
		}
		else
		{
			include("med_mail_constants.php");
			
			$strTo				=	$this->getSettings("GEN_ERROR_TO_ID");
			
			$strHeaders 		.=	$this->getSettings("GEN_ADDITIONAL_HED");

			$objMail->Host		=	$this->getSettings("GEN_ERROR_HOST");
			$objMail->Mailer	=	$this->getSettings("GEN_ERROR_MAILER");
			$objMail->From		=	$this->getSettings("GEN_ERROR_FROM");
			$objMail->FromName	=	$this->getSettings("GEN_FROM_NAME");
		}

		$objMail->Subject	=	$strSubject;
		$objMail->Body		=	$strBody;
		$objMail->AddAddress($strTo);
		$objMail->Send();
		
	}

	
	function sendMail($strTo,$strSubject,$strBody,$strBCC=NULL)
	{
			require_once("../base/mailer/class.phpmailer.php");
			$objMail	=	new PHPMailer();
			$objMail->IsHTML(true);
			$objMail->Host		=	$this->getSettings("GEN_ERROR_HOST");
			$objMail->Mailer	=	$this->getSettings("GEN_ERROR_MAILER");
			$objMail->Subject	=	$strSubject;
			$objMail->From		=	$this->getSettings("ADMIN_EMAIL");

			if (!empty($strBCC)) $objMail->AddBCC($strBCC);

			$strBCC	=	$this->getSettings("BCC_EMAIL");
			if (!empty($strBCC)) $objMail->AddBCC($strBCC);

			$objMail->FromName	=	$this->getSettings("GEN_FROM_NAME");
			$objMail->Body		=	$strBody;
			$arrTo	=	explode(",",$strTo);
			for($intTo = 0; $intTo < count($arrTo); $intTo++)
			{
				$objMail->AddAddress($arrTo[$intTo]);
			}

			if(!$objMail->Send())
				$this->raiseError("MAIL_ERROR",$objMail->ErrorInfo);
	}
	
	
	
		
	function sendEmbeddedMail($strTo,$strSubject,$strBody,$strBCC=NULL,$arrEmbeddedImage,$strCC=NULL,$strCharacterSeperator=",",$arrAttachments=NULL,$strFrom="")
	{
		set_time_limit(0);
		if(trim(strtoupper($this->getSettings("SEND_MAIL")))=='YES')
		{
			
			require_once("../base/mailer/class.phpmailer.php");
			$objMail	=	new PHPMailer();
			$objMail->IsHTML(true);
			$objMail->Mailer	=	$this->getSettings("GEN_ERROR_MAILER");
			$objMail->Host		=	$this->getSettings("GEN_ERROR_HOST");	
			$objMail->SMTPAuth	=	true;
				
			
			
					
			$objMail->Subject	=	$strSubject;
			if($strFrom == "")
				$objMail->From		=	$this->getSettings("ADMIN_EMAIL"); 
			else
				$objMail->From		=	$strFrom;

			if (!empty($strBCC))
			{
				$arrStrBCC	= explode($strCharacterSeperator,$strBCC);
				foreach($arrStrBCC as $strBCC)
				{
					$objMail->AddBCC($strBCC);
				}		
			}
			
			if (!empty($strCC))
			{
				$arrStrCC	= explode($strCharacterSeperator,$strCC);
				foreach($arrStrCC as $strCC)
				{
					$objMail->AddCC($strCC);
				}		
			}
			
			
			$strSitePath		=	$this->getSettings('IMAGE_URL');
			
			
			foreach($arrEmbeddedImage as $key=>$value)
			{
				if($key == "top")
					$objMail->AddEmbeddedImage($strSitePath.$value,$key,$value);
				else
					$objMail->AddEmbeddedImage($value,$key,$value);	
			}

			$strBCC	=	$this->getSettings("BCC_EMAIL");
			if (!empty($strBCC)) $objMail->AddBCC($strBCC);
				
			$objMail->FromName	=	$this->getSettings("GEN_FROM_NAME");
			$objMail->Body		=	$strBody;
			
			
			$arrStrTo	= explode($strCharacterSeperator,$strTo);
			
			foreach($arrStrTo as $strTo)
			{
				$objMail->AddAddress($strTo);
			}			
		
			if(is_array($arrAttachments))
			{
				foreach($arrAttachments as $intIndex=>$arrFilename)
				{
					foreach($arrFilename as $strPath=>$strFilename)
					{
						$objMail->AddAttachment($strPath,$strFilename);
						
					}
				}
			}		
			
			if(!$objMail->Send())
				return false;
			else
				return true;
		}
		else
			return false;
	}
	
	

	
	function getMailContent($arrData,$strTemplateName)
	{
		global $objSmarty;
		if ($objSmarty == null )
			$objGeneral->raiseError("SMARTY_ERROR","Smarty object is not available","","Make your smary object globally");

		$strMiddle = "./mailtemplate/".$strTemplateName;
		if (!isset($arrData))
			$arrData = array();
		foreach ($arrData as $key => $value)
					$objSmarty->assign($key,$value);
		$strMailContent = $objSmarty->fetch($strMiddle);
		return $strMailContent ;
	}
	
	function getBackTrace()
	{
		
		$arrTemp=debug_backtrace();
		
		for($intTemp=2;$intTemp < count($arrTemp) ;$intTemp++)
		{
			
			$strFileName = $arrTemp[$intTemp]["file"];
			
			$strLine = $arrTemp[$intTemp]["line"];
			
			$strFunction = $arrTemp[$intTemp]["function"];
			
			if ((strstr($strFileName,"smarty")) || (strstr($strFileName,"med_bottom")))
				continue;
			
			$strStrackTrace .= $strFileName."=>".$strFunction."=> Line (".$strLine.")<br>";
		}
		
		return $strStrackTrace;
	}

	
	function uploadImage($strImageFieldName,$strUploadPath,$strImageId)
	{
		$strImageName	=	$_FILES[$strImageFieldName]['name'];
		$strImageType	=	substr($strImageName,strlen($strImageName)-3,strlen($strImageName));
		$strImageFile	=	$strUploadPath.$strImageId.".".$strImageType;

		if($_FILES[$strImageFieldName]['size']>0)
		{
			
			if(@move_uploaded_file($_FILES[$strImageFieldName]['tmp_name'], $strImageFile))
			{
			}
			else $this->setMessage('IMAGE_ERROR_MESSAGE');
		}
		else $this->setMessage($this->getSiteMessage('IMAGE_ERROR_MESSAGE'));
		if(@file_exists($strImageFile)) return $strImageId.".".$strImageType;
		else return "";
	}

	
	function uploadAttachedImage($strImageFieldName,$strUploadPath,$intIndex)
	{
		$strImageName	=	$_FILES[$strImageFieldName]['name'][$intIndex];
		$strImageFile	=	$strUploadPath.$strImageName;

		if($_FILES[$strImageFieldName]['size'][$intIndex]>0)
		{
			
			if(@move_uploaded_file($_FILES[$strImageFieldName]['tmp_name'][$intIndex], $strImageFile))
			{
			}
			else $this->setMessage('IMAGE_ERROR_MESSAGE');
		}
		else $this->setMessage($this->getSiteMessage('IMAGE_ERROR_MESSAGE'));
		if(@file_exists($strImageFile)) return $strImageName;
		else return "";
	}

	
	
	function resizeImage($strSourceImage,$strDestpath,$strImageName,$intNewwidth,$intNewHeight)
	{
		$strFlag = false;
		
   		$strType=explode(".",$strImageName);
		if($strType[count($strType)-1] == "jpg" || $strType[count($strType)-1] == "JPG" )
   		{
		   	$strSrcImage = @imagecreatefromjpeg( $strSourceImage );
   		}
   		else if($strType[count($strType)-1]=="gif" || $strType[count($strType)-1]=="GIF")
   		{
   			$strSrcImage = @imagecreatefromgif( $strSourceImage );
   		}
   		else if($strType[count($strType)-1]=="png" || $strType[count($strType)-1]=="PNG")
   		{
   		  	$strSrcImage = @imagecreatefrompng($strSourceImage);
   		}

		
	   	$intSrcWidth  = (@imagesx( $strSrcImage )>$intNewwidth)?$intNewwidth:@imagesx( $strSrcImage );
	   	$intSrcHeight = (@imagesy( $strSrcImage )>$intNewHeight)?$intNewHeight:@imagesy( $strSrcImage );

		$intSrcWidth  = @imagesx( $strSrcImage );
	   	$intSrcHeight = @imagesy( $strSrcImage );

		$intNewwidth  = ($intSrcWidth>$intNewwidth)?$intNewwidth:$intSrcWidth;
	   	$intNewHeight = ($intSrcHeight>$intNewHeight)?$intNewHeight:$intSrcHeight;

	   	
	   	
	   	
	   	

	   	if( $intSrcWidth < $intSrcHeight )
		{
		   $ratio = @(((float)$intNewHeight) / $intSrcHeight);
		   $intDestWidth=$intSrcWidth  * $ratio;
		   $intDestHeight=$intSrcHeight * $ratio;




	   	}
		else
		{
		   $ratio = @(((float)$intNewwidth) / $intSrcWidth);
		   $intDestWidth=$intSrcWidth  * $ratio;
		   $intDestHeight=$intSrcHeight * $ratio;

		   
		   
	   	}
	   	
	   	$strDestImage =  @imagecreatetruecolor( $intDestWidth, $intDestHeight);

	   	
	   	@imagecopyresampled( $strDestImage, $strSrcImage, 0, 0, 0, 0, $intDestWidth, $intDestHeight, $intSrcWidth, $intSrcHeight );

	   	
	  	if($strType[count($strType)-1]=="jpg" || $strType[count($strType)-1]=="JPG")
	   	{
			if(@imagejpeg($strDestImage,$strDestpath.$strImageName)) 	$strFlag = true;
	  	}
	   	else if($strType[count($strType)-1]=="gif" || $strType[count($strType)-1]=="GIF")
	   	{
			if(@imagegif($strDestImage,$strDestpath.$strImageName)) 		$strFlag = true;
	   	}
	   	else if($strType[count($strType)-1]=="png" || $strType[count($strType)-1]=="PNG")
	   	{
			if(@imagepng($strDestImage,$strDestpath.$strImageName))		$strFlag = true;
	   	}

	   
	   @imagedestroy( $strSrcImage  );
	   @imagedestroy( $strDestImage );

	   if($strFlag == true)		return $strImageName;
	   else						$this->setMessage('IMAGE_RESIZE_ERROR_MESSAGE');
	}

	function removeImage($strFileType,$strPath,$strFileName)
	{
		$blnAction=true;
		if(trim(strtoupper($strFileType))=="IMG")
		{
			if(is_writable($strPath."/".$strFileName))
				@unlink($strPath."/".$strFileName);
			else
			{
				$strFileList .= "/".$strFileName;
				$this->setMessage($this->getSiteMessage("FILE_WRITABLE_ERROR"));
				$blnAction = false;
			}


			if(is_writable($strPath."large/".$strFileName))
				@unlink($strPath."large/".$strFileName);
			else
			{
				if (file_exists($strPath."large/".$strFileName))
				{
					$strFileList .= " large/".$strFileName."<br>";
					$blnAction=false;
				}
			}

			if(is_writable($strPath."middle/".$strFileName))
				@unlink($strPath."middle/".$strFileName);
			else
			{
				if (file_exists($strPath."middle/".$strFileName))
				{
					$strFileList .= "middle/".$strFileName."<br>";
					$blnAction=false;
				}
			}
			if(is_writable($strPath."thumb/".$strFileName))
				@unlink($strPath."thumb/".$strFileName);
			else
			{
				if (file_exists($strPath."thumb/".$strFileName))
				{
					$strFileList .= " thumb/".$strFileName."<br>";
					$blnAction=false;
				}
			}
			if (!$blnAction)
			{
				$strMessage= $this->getSiteMessage("FILE_WRITABLE_ERROR")."<br>".$strFileList;
				$this->setMessage($strMessage);
			}
		}
		else
		{
			if(is_writable($strPath."/".$strFileName))
				@unlink($strPath."/".$strFileName);
			else
			{
				$strFileList .= "/".$strFileName;
				$this->setMessage($this->getSiteMessage("FILE_WRITABLE_ERROR"));
				$blnAction = false;
			}
		}
		return $blnAction;
	}

	function checkPasswordExists($strPass,$intEmpId = '' )
	{
		$strTblName		= "emp_master";
		$strFieldNames	= "count(*) as tot";
		$strWhere		= "password='".md5($strPass)."'";
		if($intEmpId != '')
			$strWhere	.= " AND emp_id != $intEmpId "; 
			
		$rsRecords		= MedPage::getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
		return $rsRecords;
	}


	function checkClientUsernameExists($strUsername,$strPassword,$intClientId,$strPageType)
	{
		$strTblName		= "client_master";
		$strFieldNames	= "client_username,client_password,client_id";
		
		$strWhere		=	"client_username = '".$strUsername."' and client_username IS NOT NULL";
		if($strPageType == "E")
			$strWhere		.= " and client_id != '".$intClientId."'";

		return MedPage::getRecords($strTblName,$strFieldNames,$strWhere,"","","","");

	}


	
	function checkAuth($intId,$strType="T")
	{
		

		$arrFileName	=	array("med_auth",
								  "med_logout",
								  "med_home",
								  "./../../base/meditab/med_getHTMLControl");
		$strFileName	=	MedPage::getRequest('file');

		if($this->getSession("intAdminRole") > 0 && !in_array($strFileName,$arrFileName) && !empty($strFileName))
		{
			
			if($strType=="T" && $intId!="")
			{
				$strTblName		= 	"adminlink_master";
				$strFieldName	= 	"adminlink_id";
				$strWhere		= 	"linktable_id=".$intId;
				$rsAdminLink	=	MedPage::getRecords($strTblName, $strFieldName, $strWhere, "", "","", "");
				$intAdminLinkId	=	$rsAdminLink[0]['adminlink_id'];
			}
			elseif($strType=="L" && $intId!="")
				$intAdminLinkId	=	$intId;

			
			if($intAdminLinkId!="")
			{
				$strTblName		= 	"role_link";
				$strFieldName	= 	"count(0) as cnt";
				$strWhere		= 	"role_id='".$this->getSession("intAdminRole")."' and adminlink_id='".$intAdminLinkId."'";
				$rsAuth			=	MedPage::getRecords($strTblName, $strFieldName, $strWhere, "", "","", "");
				if($rsAuth[0]['cnt'] > 0)	$blnAuth	=	true;
				else $blnAuth	=	false;
			}
			else
				$blnAuth	=	false;
		}
		else	$blnAuth	=	true;

		if(!$blnAuth)
		{
			global $objSmarty,$objPage,$globalValues,$strIndex;
			$strMiddle	= "./middle/med_auth.htm";
			
			include_once("./scripts/bottom/med_bottom.php");
			exit;
		}
	}

}
?>
