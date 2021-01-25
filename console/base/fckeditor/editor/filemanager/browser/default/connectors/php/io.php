<?php 


function GetUrlFromPath( $resourceType, $folderPath )
{
	if ( $resourceType == '' )
		return RemoveFromEnd( $GLOBALS["UserFilesPath"], '/' ) . $folderPath ;
	else
		return $GLOBALS["UserFilesPath"] . $resourceType . $folderPath ;
}

function RemoveExtension( $fileName )
{
	return substr( $fileName, 0, strrpos( $fileName, '.' ) ) ;
}

function ServerMapFolder( $resourceType, $folderPath )
{
	
	$sResourceTypePath = $GLOBALS["UserFilesDirectory"] . $resourceType . '/' ;

	
	CreateServerFolder( $sResourceTypePath ) ;

	
	return $sResourceTypePath . RemoveFromStart( $folderPath, '/' ) ;
}

function GetParentFolder( $folderPath )
{
	$sPattern = "-[/\\\\][^/\\\\]+[/\\\\]?$-" ;
	return preg_replace( $sPattern, '', $folderPath ) ;
}

function CreateServerFolder( $folderPath )
{
	$sParent = GetParentFolder( $folderPath ) ;

	
	if ( !file_exists( $sParent ) )
	{
		$sErrorMsg = CreateServerFolder( $sParent ) ;
		if ( $sErrorMsg != '' )
			return $sErrorMsg ;
	}

	if ( !file_exists( $folderPath ) )
	{
		
		error_reporting( 0 ) ;
		
		ini_set( 'track_errors', '1' ) ;

		
		$oldumask = umask(0) ;
		mkdir( $folderPath, 0777 ) ;
		umask( $oldumask ) ;

		$sErrorMsg = $php_errormsg ;

		
		ini_restore( 'track_errors' ) ;
		ini_restore( 'error_reporting' ) ;

		return $sErrorMsg ;
	}
	else
		return '' ;
}

function GetRootPath()
{
	$sRealPath = realpath( './' ) ;

	$sSelfPath = $_SERVER['PHP_SELF'] ;
	$sSelfPath = substr( $sSelfPath, 0, strrpos( $sSelfPath, '/' ) ) ;

	return substr( $sRealPath, 0, strlen( $sRealPath ) - strlen( $sSelfPath ) ) ;
}
?>