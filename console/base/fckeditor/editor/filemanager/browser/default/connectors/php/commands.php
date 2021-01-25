<?php 


function GetFolders( $resourceType, $currentFolder )
{
	
	$sServerDir = ServerMapFolder( $resourceType, $currentFolder ) ;

	
	$aFolders	= array() ;

	$oCurrentFolder = opendir( $sServerDir ) ;

	while ( $sFile = readdir( $oCurrentFolder ) )
	{
		if ( $sFile != '.' && $sFile != '..' && is_dir( $sServerDir . $sFile ) )
			$aFolders[] = '<Folder name="' . ConvertToXmlAttribute( $sFile ) . '" />' ;
	}

	closedir( $oCurrentFolder ) ;

	
	echo "<Folders>" ;
	
	natcasesort( $aFolders ) ;
	foreach ( $aFolders as $sFolder )
		echo $sFolder ;

	
	echo "</Folders>" ;
}

function GetFoldersAndFiles( $resourceType, $currentFolder )
{
	
	$sServerDir = ServerMapFolder( $resourceType, $currentFolder ) ;

	
	$aFolders	= array() ;
	$aFiles		= array() ;

	$oCurrentFolder = opendir( $sServerDir ) ;

	while ( $sFile = readdir( $oCurrentFolder ) )
	{
		if ( $sFile != '.' && $sFile != '..' )
		{
			if ( is_dir( $sServerDir . $sFile ) )
				$aFolders[] = '<Folder name="' . ConvertToXmlAttribute( $sFile ) . '" />' ;
			else
			{
				$iFileSize = filesize( $sServerDir . $sFile ) ;
				if ( $iFileSize > 0 )
				{
					$iFileSize = round( $iFileSize / 1024 ) ;
					if ( $iFileSize < 1 ) $iFileSize = 1 ;
				}

				$aFiles[] = '<File name="' . ConvertToXmlAttribute( $sFile ) . '" size="' . $iFileSize . '" />' ;
			}
		}
	}

	
	natcasesort( $aFolders ) ;
	echo '<Folders>' ;

	foreach ( $aFolders as $sFolder )
		echo $sFolder ;

	echo '</Folders>' ;

	
	natcasesort( $aFiles ) ;
	echo '<Files>' ;

	foreach ( $aFiles as $sFiles )
		echo $sFiles ;

	echo '</Files>' ;
}

function CreateFolder( $resourceType, $currentFolder )
{
	$sErrorNumber	= '0' ;
	$sErrorMsg		= '' ;

	if ( isset( $_GET['NewFolderName'] ) )
	{
		$sNewFolderName = $_GET['NewFolderName'] ;

		if ( strpos( $sNewFolderName, '..' ) !== FALSE )
			$sErrorNumber = '102' ;		
		else
		{
			
			$sServerDir = ServerMapFolder( $resourceType, $currentFolder ) ;

			if ( is_writable( $sServerDir ) )
			{
				$sServerDir .= $sNewFolderName ;

				$sErrorMsg = CreateServerFolder( $sServerDir ) ;

				switch ( $sErrorMsg )
				{
					case '' :
						$sErrorNumber = '0' ;
						break ;
					case 'Invalid argument' :
					case 'No such file or directory' :
						$sErrorNumber = '102' ;		
						break ;
					default :
						$sErrorNumber = '110' ;
						break ;
				}
			}
			else
				$sErrorNumber = '103' ;
		}
	}
	else
		$sErrorNumber = '102' ;

	
	echo '<Error number="' . $sErrorNumber . '" originalDescription="' . ConvertToXmlAttribute( $sErrorMsg ) . '" />' ;
}

function FileUpload( $resourceType, $currentFolder )
{
	$sErrorNumber = '0' ;
	$sFileName = '' ;

	if ( isset( $_FILES['NewFile'] ) && !is_null( $_FILES['NewFile']['tmp_name'] ) )
	{
		$oFile = $_FILES['NewFile'] ;

		
		$sServerDir = ServerMapFolder( $resourceType, $currentFolder ) ;

		
		$sFileName = $oFile['name'] ;
		$sOriginalFileName = $sFileName ;
		$sExtension = substr( $sFileName, ( strrpos($sFileName, '.') + 1 ) ) ;
		$sExtension = strtolower( $sExtension ) ;

		global $Config ;

		$arAllowed	= $Config['AllowedExtensions'][$resourceType] ;
		$arDenied	= $Config['DeniedExtensions'][$resourceType] ;

		if ( ( count($arAllowed) == 0 || in_array( $sExtension, $arAllowed ) ) && ( count($arDenied) == 0 || !in_array( $sExtension, $arDenied ) ) )
		{
			$iCounter = 0 ;

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

					break ;
				}
			}
		}
		else
			$sErrorNumber = '202' ;
	}
	else
		$sErrorNumber = '202' ;

	echo '<script type="text/javascript">' ;
	echo 'window.parent.frames["frmUpload"].OnUploadCompleted(' . $sErrorNumber . ',"' . str_replace( '"', '\\"', $sFileName ) . '") ;' ;
	echo '</script>' ;

	exit ;
}
?>