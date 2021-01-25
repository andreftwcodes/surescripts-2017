<?php

		
		$errorContent='
				<style>
				.err
				{
				   border: 1px solid #ff0000;
				   background-color: #FFFFCC;
				   font-family: Tahoma, Arial, Helvetica, sans-serif;
				   color: #000000;
				   font-size: 11px;
				}
				
				.errortable {
					font-family: Tahoma, Arial, Helvetica, sans-serif;
					font-size: 11px;
					font-weight: normal;
					color: #000000;
					line-height: 16px;
				}
				
				.error-text-red {
					font-family: Georgia, "Times New Roman", Times, serif;
					font-size: 11px;
					font-weight: normal;
					color: #ff0012;
				}
				
				.error-text-path {
					font-family: Georgia, "Times New Roman", Times, serif;
					font-size: 11px;
					font-weight: normal;
					color: #006dff;
				}
				
				.error-bold {
					font-family: Tahoma, Arial, Helvetica, sans-serif;
					font-weight: bold;
					font-size: 11px;
					color: #3399cc
				}
				
				.error-bold-02 {
					font-family: Tahoma, Arial, Helvetica, sans-serif;
					font-weight: bold;
					font-size: 11px;
					color: #003b81;
				}
				
				
				.error-head {
					font-family: Verdana, Arial, Helvetica, sans-serif;
					font-size: 12px;
					color: #003b80;
					font-weight: bold;
				}
				
				.error-td02 {
					background-color: #cadffa;
				}
				
				.error-td03 {
					background-color: #eff5fd;
				}
				
				.error-td04 {
					background-color: #fefeed;
				}
				.header_text_blue {
					FONT-WEIGHT: bold; 
					FONT-SIZE: 11px; 
					COLOR: #799cc3; 
					FONT-FAMILY: Tahoma, verdana, Helvetica, sans-serif ; 
					text-decoration:none;
				}	
}	

				</style>';
			$strSiteName 	=	$_SERVER["HTTP_HOST"];
			$strRequestURL 	=	$_SERVER["REQUEST_URI"];
			$strBrowserName	=	$_SERVER['HTTP_USER_AGENT'];
			$strIPAddress	=	$_SERVER['REMOTE_ADDR'];
			$intEmpCode		=	Medgeneral::getSession("intEmployeeCode");
			$strUserName	=	Medgeneral::getSession("strUserName");
			
			$errorContent.='<br><br><table width="98%" border="0" cellpadding="0" cellspacing="0">
				 <tr>
				  <td class="rightbg-middle" align="center" bgcolor="#9cc0f0" valign="top">
				  <table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#CCCCCC" class="errortable">
					  <tbody>
						<tr>
							  <td height="24" colspan="2" align="center" valign="middle" class="error-td02">
									<font class="error-head"> Error Page : '.$strPageName.'</font>
								</td>
						</tr>';

				
				$errorContent.='
						<tr>
						  <td width="29%" align="right" class="error-td03"><font class="error-bold">Username:</font>&nbsp;</td>
						  <td width="71%" align="left" class="error-td04">'.$strUserName.' ( '.$intEmpCode.' )</td>
						</tr>
						<tr>
						  <td width="29%" align="right" class="error-td03"><font class="error-bold">Http Host:</font>&nbsp;</td>
						  <td width="71%" align="left" class="error-td04">'.$strSiteName.'</td>
						</tr>
						<tr>
						  <td width="29%" align="right" class="error-td03"><font class="error-bold">IP/Browser:</font>&nbsp;</td>
						  <td width="71%" align="left" class="error-td04">'.$strIPAddress.' ['.$strBrowserName.' ]</td>
						</tr>';

				
				if (!empty($intErrorCode))
					{	
					$errorContent.='<tr>
						  <td width="29%" align="right" class="error-td03"><font class="error-bold">Error Code:</font>&nbsp;</td>
						  <td width="71%" align="left" class="error-td04">'.$intErrorCode.'</td>
						</tr>';
					}	
					
				
				if (!empty($strErrorMessage))
					{	
				$errorContent.='
						<tr height="35">
						  <td align="right" nowrap="nowrap" class="error-td03"><font class="error-bold-02">Error Message:&nbsp;</font></td>
						  <td align="left" class="error-td04"><font class="error-text-red">'.$strErrorMessage.'</font></td>
						</tr>';
					}
								
				if (!empty($strRequestURL))
					{	
				$errorContent.='
						<tr height="35">
						  <td align="right" nowrap="nowrap" class="error-td03"><font class="error-bold-02">Request URL</font></td>
						  <td align="left" class="error-td04"><font class="error-td04">'.$strRequestURL.'</font></td>
						</tr>';
					}
				
				
				
				if (!empty($strErrorDesc))		
				{
				$errorContent.='<tr height="50">
						  <td align="right" valign="top" nowrap="nowrap" class="error-td03"><font class="error-bold">Error Message:&nbsp;</font></td>
						  <td align="left" valign="top" class="error-td04">'.$strErrorDesc.'</td>
						</tr>';
				}
				
				
				if (!empty($strSolution))		
				{
				$errorContent.='<tr height="50">
						  <td align="right" valign="top" nowrap="nowrap" class="error-td03"><font class="error-bold">Error Description Solution:&nbsp;</font></td>
						  <td align="left" valign="top" class="error-td04">'.$strSolution.'</td>
						</tr>';
				}							
				
				
				if (!empty($strErrorPath))		
				{									
				$errorContent.='<tr height="35">
						  <td align="right" valign="top" nowrap="nowrap" class="error-td03"><font class="error-bold-02">Error Path:&nbsp;</font></td>
						  <td align="left" valign="top" class="error-td04"><font class="error-text-path">'.$strErrorPath.'</font> </td>
						</tr>';
				}		
				$errorContent.='</tbody>
					</table>
					</td>
				</tr>
				</table>';

				
				
				$dbConnectError ='
									<br><br><table width="98%" border="0" cellpadding="0" cellspacing="0">
			 						<tr>
			  								<td class="comnbgmid" align="center"  valign="middle" height="300">				
			  									<font class="header_text_blue">There is some technical problem, please contact site administrator.</font>
			  								</td>
									</tr>
									<table>';
?>