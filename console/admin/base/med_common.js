/***
 * This javascript file contains function which is called when the Add/Edit form is submitted for validating the field values.
 * @author     		MediTab Software Inc. 
 * @copyright  		1997-2005 The MediTab Software Inc.
 * @license    		http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version   	 	Release: 1.0
 * @PHP version  	5.x
*/

/** This method is called when the form is submitted for javascript validations
*/

var divStyle

function submit_form(objFrm)
{
 if(checkValid(objFrm.name)==false) //general javascript validations for each field
	{
		return false; 
	}
	
	//if some extra validation required
	if(window.extraValid) //check whether the function for extra validation exist or not
	{
		if(extraValid()==false) //if the function returns false then return false
		{
			return false;	
		}
	}
	else //if function doesnot exist return true
	{
		return true;
	}
	
}

function selectFaq(Frm)
{
	document.frm_list_faq.faq_id.value=document.frm_list_faq.slt_faq_id.value;
	Frm.submit();
}

function zoomIn(url,labelName,control_name,width,height)
{
	try
	{
		contType=document.getElementById(control_name).type;
	}
	catch(e)
	{
		alert(control_name+" does not exists");
		return false;	
	}
	if(contType == "text" || contType == "textarea")
		height = 500;
	else
		height = 200;
	popup(url+'&contName='+control_name+'&labelName='+labelName,width,height);

}
function assignValuesToControl()
{
	txtName = document.frm_zoom.hid_fieldname.value;
	window.opener.document.getElementById(txtName).value = document.frm_zoom.Taara_zoom_desc.value;
	window.close();
}
function assignValuesFromControl()
{
	txtName = document.frm_zoom.hid_fieldname.value;
	document.frm_zoom.Taara_zoom_desc.value = parent.opener.document.getElementById(txtName).value;
	window.focus();
	document.frm_zoom.Taara_zoom_desc.focus();
}

var objHttp = createRequestObject();   //to create a http object

//function to change the employee list according to dept selected
function changeEmpList()
{
	intOfficeId=document.frm_list_record.slt_office_id.value;
	intId=document.frm_list_record.slt_dept.value;
	getResponse("index.php?file=med_dept_emp_list&dept_id="+intId+"&office_id="+intOfficeId);
}

//function to create a http object
function createRequestObject()
{
	var strRequestObj; //declare the variable to hold the object.
	var strBrowserName = navigator.appName; //find the browser name
	if(strBrowserName == "Microsoft Internet Explorer")
	{
		strRequestObj = new ActiveXObject("Microsoft.XMLHTTP");
	}
	else
	{
		strRequestObj = new XMLHttpRequest();
	}
	return strRequestObj; //return the object
}

//function to get the output from the required url then replace the div's content
function handleData()
{	
	if(objHttp.readyState == 4 && objHttp.status == 200)
	{
		var strResponse1 = objHttp.responseText;
		document.getElementById("div_emp_list").innerHTML = strResponse1;
	}
}

//function to get the response from the requested url
function getResponse(strUrl)
{
	objHttp.open("get", strUrl, true);
	objHttp.onreadystatechange =  function() { handleData(); };
	objHttp.send(null);
}

function exportExcelFile(objFrm)
{
	if(submit_form(objFrm) != false)
		objFrm.submit();
	else
		return false;
		
}

//function to get empcombo from dept combo
function getEmpCombo()
{
	OFFICE_COMBO	=	document.getElementById("Sr_slt_office_id");
	DEPT_COMBO		=	document.getElementById("Sr_slt_dept");
	if(OFFICE_COMBO.value==0)
		strOfficeId	=	document.getElementById("hid_all_office").value;
	else
		strOfficeId	=	OFFICE_COMBO.value;
		
	if(DEPT_COMBO.value=="all")
		strDeptId	=	document.getElementById("hid_all_dept").value;
	else
		strDeptId	=	DEPT_COMBO.value;
	
	EMP_COMBO		=	document.getElementById("Sr_Mlslt_emp[]");
	strEmpId	=	"";
	for(intEmp=0; intEmp<EMP_COMBO.length; intEmp++)
	{
		if(EMP_COMBO[intEmp].selected)
			strEmpId	+=	EMP_COMBO[intEmp].value+",";
	}
	strEmpId	=	strEmpId.substr(0,strEmpId.length-1);
	getPageData("index.php?file=med_get_emp_combo&slt_office_id="+strOfficeId+"&slt_dept="+strDeptId+"&slt_emp="+strEmpId,"div_emp")
}

//Function which will redirect to Page Settings page from the other sub-pages of Page Settings
function gotoPageSetting()
{
	self.location = "index.php?file=med_page_listings&hid_page_type=L&hidin_module_id=0";
}

/* This function is used to check number of rows to display with general setting parameter */

function checkRowLimit(intHiddenControl,intMaxRowLimit)
{
	// Check for numeric value
	if(!(checkInteger(intHiddenControl,"","")))
	{
		alert("Please enter valid rows");
		setStyle(intHiddenControl);
		eval(strStyle);
		eval(strFocus);
		intHiddenControl.focus();
		return false;
		
	}
   
	if( parseInt(intHiddenControl.value) > parseInt(intMaxRowLimit))
	{
		alertMessage = "Number of rows should not be greater than "+intMaxRowLimit;  
		alert(alertMessage);
		setStyle(intHiddenControl);
		eval(strStyle);
		eval(strFocus);
		intHiddenControl.focus();
		return false;
	}
}
//This function will give selected values from multi combo (used in tool-tip)
function getSelectedComboValue(strControlId,strToolTipId)
{
	var strSelectedId 	= 	document.getElementById(strControlId);
	var strSelected		=	"";
	
	for(var intSelectedId=0;intSelectedId<strSelectedId.options.length;intSelectedId++)
	{	
		if(strSelectedId.options[intSelectedId].selected == true)
		{	
			strSelected = strSelected + strSelectedId.options[intSelectedId].text+"<br>";
		}
	}
	strSelected = strSelected.substring(0,strSelected.length-1);
	strSelected = strSelected.replace(/All,/gi,'')
//	return overlib(strSelected,CAPTION,'Selected Values', DELAY, 200, STICKY, MOUSEOFF, 1000, WIDTH, 400, CLOSETEXT, '<img border=0 src=./images/close-inline.gif>', CLOSETITLE, 'Click to Close', CLOSECLICK, FGCLASS, 'olFgClass', CGCLASS, 'olCgClass', BGCLASS, 'olBgClass', TEXTFONTCLASS, 'olFontClass', CAPTIONFONTCLASS, 'olCapFontClass', CLOSEFONTCLASS, 'olCloseFontClass')
	return overlib(strSelected,CAPTION,'Selected Values', DELAY, 400, STICKY, MOUSEOFF, 1000, WIDTH, 250, CLOSETEXT, '<img border=0 src=./images/close-inline.gif>', CLOSETITLE, 'Click to Close', CLOSECLICK, FGCLASS, 'olFgClass', CGCLASS, 'olCgClass', BGCLASS, 'olBgClass', TEXTFONTCLASS, 'olFontClass', CAPTIONFONTCLASS, 'olCapFontClass', CLOSEFONTCLASS, 'olCloseFontClass')
}

/* This function is used to zoom combo */
function zoomCombo(strComboName,elmImage,intMin,intMax)
{
	var combo		 = document.getElementById(strComboName);
	var imgZoomIn	 = "s_zoom_down.gif";
	var imgZoomOut	 = "s_zoom_up.gif";
	var imgImage	 = elmImage.src;
	
	if(combo.size >= intMax)
	{
		elmImage.src = imgImage.replace(imgZoomOut,imgZoomIn);
		combo.size	 = intMin;
	}
	else
	{
		elmImage.src = imgImage.replace(imgZoomIn,imgZoomOut);
		combo.size	 = intMax;
	}
		
	return false;
}

/* This function is used to set display property based on browser*/

if(navigator.appName == 'Netscape')
	divStyle	=	'table-row';
else
	divStyle	=	'inline';
