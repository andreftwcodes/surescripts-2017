<?php 


require('config.php') ;
require('util.php') ;


function SendResults( $errorNumber, $fileUrl = '', $fileName = '', $customMsg = '' )
{
	echo '<script type="text/javascript">' ;
	echo 'window.parent.OnUploadCompleted(' . $errorNumber . ',"' . str_replace( '"', '\\"', $fileUrl ) . '","' . str_replace( '"', '\\"', $fileName ) . '", "' . str_replace( '"', '\\"', $customMsg ) . '") ;' ;
	echo '</script>' ;
	exit ;
}


if ( !$Config['Enabled'] )
	SendResults( '1', '', '', 'This file uploader is disabled. Please check the "editor/filemanager/upload/php/config.php" file' ) ;


if ( !isset( $_FILES['NewFile'] ) || is_null( $_FILES['NewFile']['tmp_name'] ) || $_FILES['NewFile']['name'] == '' )
	SendResults( '202' ) ;


$oFile = $_FILES['NewFile'] ;


$sFileName = $oFile['name'] ;
$sOriginalFileName = $sFileName ;
$sExtension = substr( $sFileName, ( strrpos($sFileName, '.') + 1 ) ) ;
$sExtension = strtolower( $sExtension ) ;


$sType = isset( $_GET['Type'] ) ? $_GET['Type'] : 'File' ;


$arAllowed	= $Config['AllowedExtensions'][$sType] ;
$arDenied	= $Config['DeniedExtensions'][$sType] ;


if ( ( count($arAllowed) > 0 && !in_array( $sExtension, $arAllowed ) ) || ( count($arDenied) > 0 && in_array( $sExtension, $arDenied ) ) )
	SendResults( '202' ) ;

$sErrorNumber	= '0' ;
$sFileUrl		= '' ;


$iCounter = 0 ;


$sServerDir = GetRootPath() . $Config["UserFilesPath"] ;

while ( true )
{
	
	$sFilePath = $sServerDir . $sFileName ;

	
	if ( is_file( $sFilePath ) )
	{
		$iCounter++ ;
		$sFileName = RemoveExtension( $sOriginalFileName ) . '(' . $iCounter . ').' . $sExtension ;
		$sErrorNumber = '201' ;
	}
	else
	{
		move_uploaded_file( $oFile['tmp_name'], $sFilePath ) ;

		if ( is_file( $sFilePath ) )
		{
			$oldumask = umask(0) ;
			chmod( $sFilePath, 0777 ) ;
			umask( $oldumask ) ;
		}
		
		$sFileUrl = $Config["UserFilesPath"] . $sFileName ;

		break ;
	}
}

SendResults( $sErrorNumber, $sFileUrl, $sFileName ) ;
?>