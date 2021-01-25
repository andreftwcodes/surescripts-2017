<?php

echo base64_decode("VU5BOisuPyojVUlCK1VOT0E6MDo6Kzo6Ois8PE1FU1NBR0VfSURfUExBQ0VfSE9MREVSPj46Ois6Ois6Ois2OTgzNzQ4NDM4MDAxOkQ6Ois5OTAwMDA0OlA6OisyMDEyMDcxNzowODA3MTMsNDorKyNVSUgrU0NSSVBUOjAwODowMDE6TkVXUlg6OisrUlgxMjA0MDo6Ois6Ois6OisjUFZEK1BDKzY5ODM3NDg0MzgwMDE6U1BJOisrQU06VVMrQWlnYmU6TWljaGFlbDo6TT8uRD8uOisrTWljaGFlbCBNIEFsZXhhbmRlciwgRD8uTz8uKzcyNzEgVz8uIFNhaGFyYSBBdmU/LiwgU3RyIDExMDpMYXMgVmVnYXM6TlY6ODkxMTc6OisxMTExMTExMTExWDExMTE6SFAqMjIyMjIyMjIyMlgyMjIyOkhQKjMzMzMzMzMzMzM6RlgqNjE0MjcyNTI0NDpURSo2MTQyNzI5ODQxOkZYOis6Ojo6I1BWRCtQMis5OTAwMDA0OkQzKysrKytIYW5keSdzIFBoYXJtYWN5Kzk5OSBVbmRlciBXYXk6UGhpbGFkZWxwaGlhOlBBOjE5MTAzOjorMjE1OTI2MDM3ODpURTorI1BUVCsrMTk2OTA0MjgrVEVTVDpURVNUOlQ6OitGKzEzMDAxOjk0KzIwMSBFYXN0IEJyb2FkIFN0cmVldDpDb2x1bWJ1czpPSDo0MzIxNTo6KzYxNDIyODY2MjBYMTE6SFAqNjE0MjI4NjYyMFgxMTpXUCo2MTQyMjg2NjE1OkZYOiNEUlUrUDpQUk9aQUMgMjAgTUcgQ0FQU1VMRToxNjU5MDA4NDM5MDpORDo6MjAgbWc6Ojo6OjorQVY6MTU6MzgrOlRha2UgMSBhdCBiZWR0aW1lOis4NToyMDEyMDcxNzoxMDIrMCtSOjA6Kys6Kys6Ojo6KyNVSVQrUlgxMjA0MCsxMCNVSVorKzErIw==");
exit;
$strMsg =  "UNA:+.?*#UIB+UNOA:0::+:::+<<MESSAGE_ID_PLACE_HOLDER>>::+::+::+6983748438001:D::+9900004:P::+20120717:080713,4:++#UIH+SCRIPT:008:001:NEWRX::++RX12040:::+::+::+#PVD+PC+6983748438001:SPI:++AM:US+Aigbe:Michael::M?.D?.:++Michael M Alexander, D?.O?.+7271 W?. Sahara Ave?., Str 110:Las Vegas:NV:89117::+1111111111X1111:HP*2222222222X2222:HP*3333333333:FX*6142725244:TE*6142729841:FX:+::::#PVD+P2+9900004:D3+++++Handy's Pharmacy+999 Under Way:Philadelphia:PA:19103::+2159260378:TE:+#PTT++19690428+TEST:TEST:T::+F+13001:94+201 East Broad Street:Columbus:OH:43215::+6142286620X11:HP*6142286620X11:WP*6142286615:FX:#DRU+P:PROZAC 20 MG CAPSULE:16590084390:ND::20 mg::::::+ZZ:15:38+:Take 1 at bedtime:+85:20120717:102+0+R:0:++:++::::+#UIT+RX12040+10#UIZ++1+#";
echo base64_encode($strMsg);

exit;
$UNA = getDelimitersFromEDIFactMessage($strMsg);


$strDRU = $UNA['5'] . 'DRU' . $UNA[1];
$strUIT = $UNA['5'] . 'UIT' . $UNA[1];


$arrMsg = explode($UNA[5], $strMsg);

foreach($arrMsg as $intLine => $strLine)
{
	$strSegmentID = 'DRU' . $UNA[1];
	if( strpos($strLine, $strSegmentID) !== false )
	{
		$intDRUIndex = $intLine;
		break;
	}
}


$DRU = $arrMsg[$intDRUIndex];


$intDRULengthBeforeCorrection = strlen($DRU);
$DRU = str_replace( $UNA[0] . $UNA[0] . 'ND' . $UNA[0], $UNA[0] . $UNA[0] . $UNA[0], $DRU);
$intDRULengthAfterCorrection = strlen($DRU);


if( $intDRULengthBeforeCorrection == $intDRULengthAfterCorrection )
{
	
	
	
	$arrDRU = explode( $UNA[0] . 'ND' . $UNA[0], $DRU);

	
	$arrDRU = explode( $UNA[0], $arrDRU[0] );

	
	$NDC = $arrDRU[(count($arrDRU) - 1)];
	
	if( strlen($NDC) > 9 )
	{
		
		$QQ = "AQ";
		
		
		$DRU = str_replace( $UNA[1] . 'ZZ' . $UNA[0], $UNA[1] . $QQ . $UNA[0], $DRU );
		
		
		$arrMsg[$intDRUIndex] = $DRU;
	}
}




	function getDelimitersFromEDIFactMessage($EDIFactMessage)
	{
		$intUNAPosition = strpos($EDIFactMessage, 'UNA');
		$intUIBPosition = strpos($EDIFactMessage, 'UIB');
		if($intUNAPosition !== FALSE && $intUIBPosition !== FALSE)
		{
			$EDIFactMessage = str_replace(array('UNA'), array(''), $EDIFactMessage);
			$arrEDIFactMessage = explode('UIB',$EDIFactMessage);
			$UNA = $arrEDIFactMessage[0];
			if(strlen($UNA) != 6)
			{
				return FALSE;
			}
			else
			{
				$UNA = str_split($UNA);
				return $UNA;
			}
		}
		else
		{
			return FALSE;	
		}
	}