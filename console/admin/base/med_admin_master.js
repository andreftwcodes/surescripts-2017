/***
 * This javascript file contains function which is used for extra validation in the Admin Master
 * @author     		MediTab Software Inc.
 * @copyright  		1997-2005 The MediTab Software Inc.
 * @license    		http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version   	 	Release: 1.0
 * @PHP version  	5.x
*/

//this function checks whether the password and the confirm password have the same value for admin master
function extraValid()
{
	if(document.frm_add_record.hid_page_type.value=='A') //to be checked only at the time of add
	{
		if(document.frm_add_record.TaRpas_password.value!=document.frm_add_record.Rpas_cpass.value)	//if password and confirm password not same
		{
			alert("Confirm password did not match");
			document.frm_add_record.TaRpas_password.focus();

			//set the error style of password and confirm password textbox
			FIELD_NAME=eval(document.frm_add_record.TaRpas_password);
			setStyle(FIELD_NAME);
			FIELD_NAME=eval(document.frm_add_record.TcRpas_cpass);
			setStyle(FIELD_NAME);
			eval(strStyle);

			return false; //return false
		}
		else
		{
			return true;
		}
	}
	else
	{
		return true;
	}
}
// JavaScript Document