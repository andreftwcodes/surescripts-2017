/****************************************************************************************************
 *  This reusable component code is developed by Meditab Software Inc,                              *
 *  for use in multiple projects and is the intellectual property of Meditab Software Inc.   		*
 *  DO NOT reuse, distribute or modify this file without prior permission from Meditab Software Inc.*
 *                                                                                                  *
 *  Last updated on: 12-May-2006                                                                    *
 ****************************************************************************************************/

//var QL_DataRowNormalCss = 'data';
var QL_DataRowHoverCss = 'dataH';
var QL_DataRowSelectedCss = 'dataS';
function QL_Submit(controlID, sourceType, sourceValue1, sourceValue2, dataRowIndex)
{
	var TEXT_SEARCH_LINK = 1;
	var ALPHABETIC_SEARCH_LINK = 2;
	var SORT_LINK = 3;
	var PAGE_LINK = 4;

	if (sourceType == TEXT_SEARCH_LINK)
	{
		// Text search button clicked
		var srchText = document.getElementById(controlID + "_tNSrchTxt");
		var srchField = document.getElementById(controlID + "_sNSrchFld");
		
		// Check for valid values
		if ((srchField.value.length > 0 && srchText.value.length <= 0) ||
			(srchField.value.length <= 0 && srchText.value.length > 0))
		{
			//if (typeof(container.srchInvalidValueMsg) != 'undefined')
				//alert(container.srchInvalidValueMsg);
			alert("Invalid values entered for search");
			return false;
		}
	}
	else if (sourceType == ALPHABETIC_SEARCH_LINK)
	{
		var srchTextObj = document.getElementById(controlID + "_hASrch");
		srchTextObj.value = sourceValue1;
	}
	else if (sourceType == SORT_LINK)
	{
		// Sort link clicked
		var orderObj = document.getElementById(controlID + "_hOFld");
		orderObj.value = sourceValue1;
		var orderDir = document.getElementById(controlID + "_hODirc");
		orderDir.value = sourceValue2;
	}
	else if (sourceType == PAGE_LINK)
	{
		// Page link clicked
		var pageObj = document.getElementById(controlID + "_hPg");
		pageObj.value = sourceValue1;
	}
	else
	{
		// External Source caused submit
	}
	var src = document.getElementById(controlID + "_hSubmitSrc");
	src.value = sourceType;
	src.form.submit();
}

function QL_MOver(sourceRow)
{
	if (sourceRow.className != QL_DataRowSelectedCss && (sourceRow.className=='data1' || sourceRow.className=='data2'))
	{
		sourceRow.originalClass = sourceRow.className;
		sourceRow.className = QL_DataRowHoverCss;
	}
	else if(sourceRow.className!=sourceRow.originalClass+'S' && sourceRow.originalClass!='data1' && sourceRow.originalClass!='data2')
	{
		sourceRow.originalClass = sourceRow.className;
		sourceRow.className = sourceRow.originalClass+'H';			
	}	
}

function QL_MOut(sourceRow)
{
	if (sourceRow.className != QL_DataRowSelectedCss && (sourceRow.originalClass=='data1' || sourceRow.originalClass=='data2'))
		sourceRow.className = sourceRow.originalClass;
	else if(sourceRow.className!=sourceRow.originalClass+'S' && sourceRow.originalClass!='data1' && sourceRow.originalClass!='data2')	
		sourceRow.className = sourceRow.originalClass;	
}

////
function QL_CBClick(controlID, srcCheckBox, rowIndex)
{
	var container = document.getElementById(controlID);
	if (srcCheckBox.checked)
	{
		//Check the limit
		if (QL_LimitExceeded(container, srcCheckBox.form))
		{
			QL_LimitExceedAction(container, srcCheckBox.form, rowIndex);
		}
		else
		{
			QL_SetCBState(srcCheckBox, rowIndex);
		}
	}
	else
	{
		// If the state is unchecked, Update the Header Check box state if required
		var headerCB = document.getElementById(controlID + "_cSlcHd");
		if (headerCB)
		{
			headerCB.checked = false;
		}
		QL_SetCBState(srcCheckBox, rowIndex);
	}
}

function QL_HeaderChkClick(controlID,headerCB,chkName)
{
	var frm = headerCB.form;
	var targetCheckBox = eval('frm.elements["' + chkName + '"]');
	// Return if no child-checkboxes exist
	if (null == targetCheckBox || typeof(targetCheckBox) == 'undefined') return;
	if (targetCheckBox.length)
	{
		for (var idx=0; idx < targetCheckBox.length; idx++)
		{
			targetCheckBox[idx].checked = headerCB.checked;
		}
	}
	else
	{
		QL_SetCBState(targetCheckBox, 0, headerCB.checked);
	}
}
function QL_HeaderCBClick(controlID, headerCB)
{
	var frm = headerCB.form;
	var targetCheckBox = eval('frm.elements["' + controlID + '_cSlcPK[]"]');

	// Return if no child-checkboxes exist
	if (null == targetCheckBox || typeof(targetCheckBox) == 'undefined') return;

	if (targetCheckBox.length)
	{
		for (var idx=0; idx < targetCheckBox.length; idx++)
		{
			QL_SetCBState(targetCheckBox[idx], idx, headerCB.checked);
		}
	}
	else
	{
		QL_SetCBState(targetCheckBox, 0, headerCB.checked);
	}
}

//function QL_SetCBState(container, targetCheckBox, rowIndex, checked)
function QL_SetCBState(targetCheckBox, rowIndex, checked)
{
	var QL_PKValue_Enclosing_Char = '^';
	if (targetCheckBox)
	{
		var originalState = targetCheckBox.checked;

		//Set the checked-state, if passed
		if (null != checked)
		{
			targetCheckBox.checked = checked;
		}

		//Set the value

		var containerRow = targetCheckBox.parentNode.parentNode;
		if (targetCheckBox.checked)
		{
			if (containerRow.className != QL_DataRowHoverCss && containerRow.className != QL_DataRowSelectedCss && (containerRow.className=='data1' || containerRow.className=='data2'))
			{
				containerRow.originalClass = containerRow.className;				
			}			
			else if (containerRow.className != QL_DataRowHoverCss && containerRow.className != QL_DataRowSelectedCss && containerRow.className.substr((containerRow.className.length)-1,containerRow.className.length) != 'H' && containerRow.className.substr((containerRow.className.length)-1,containerRow.className.length) != 'S' && containerRow.className!='data1' && containerRow.className!='data2')	
			{				
				containerRow.originalClass = containerRow.className;
			}
			
			if(containerRow.originalClass!='data1' && containerRow.originalClass!='data2')
			{
				containerRow.className = containerRow.originalClass+'S'
			}
			else
			{
				containerRow.className = QL_DataRowSelectedCss;	
			}			
		}
		else
		{
			if (null != checked)
			{
				if (targetCheckBox.checked != originalState)
					containerRow.className = containerRow.originalClass;
			}
			else
				containerRow.className = containerRow.originalClass;
		}
	}
}

function QL_LimitExceeded(container, form)
{
	var slctLimit = container.getAttribute("slctLimit");
	if (slctLimit)
	{
		var checkBoxArray = eval('form.elements["' + container.id + '_cSlcPK[]"]');

		// Get Total Selected Checkboxes
		var selectionCount = 0;
		for (var idx=0; idx<checkBoxArray.length; idx++)
		{
			if (checkBoxArray[idx].checked)
				selectionCount++;
		}

		// Check if limit exceeded
		if (selectionCount > slctLimit)
		{
			return true;
		}
	}
	return false;
}

function QL_LimitExceedAction(container, form, rowIndex)
{
	var checkBoxArray = eval('form.elements["' + container.id + '_cSlcPK[]"]');
	var slctLimit = container.getAttribute("slctLimit");
	if (slctLimit == 1)
	{
		for (var idx=0; idx<checkBoxArray.length; idx++)
		{
			if (idx == rowIndex)
				QL_SetCBState(checkBoxArray[idx], idx, true);
			else
				QL_SetCBState(checkBoxArray[idx], idx, false);
		}
	}
	else
	{
		QL_SetCBState(checkBoxArray[rowIndex], rowIndex, false);
		var slctLimitMsg = container.getAttribute("slctLimitExceedMsg");
		if (slctLimitMsg)
			alert(slctLimitMsg);
	}
}

/**** JS Functions that can be called from elsewhere ****/
function QL_GetSelectorCheckBoxes(controlID)
{
	var src = document.getElementById(controlID + "_hSubmitSrc");
	var frm = src.form;

	var checkBoxArray = eval('frm.elements["' + controlID + '_cSlcPK[]"]');
	if (checkBoxArray)
	{
		return checkBoxArray;
	}
	return null;
}

function QL_GetSelectionCount(controlID)
{
	var src = document.getElementById(controlID + "_hSubmitSrc");
	var frm = src.form;

	var selectionCount=0;
	var checkBoxArray = eval('frm.elements["' + controlID + '_cSlcPK[]"]');
	if (checkBoxArray)
	{
		if (checkBoxArray.length)
		{
			for(var idx=0; idx <  checkBoxArray.length; idx++)
			{
				if (checkBoxArray[idx].checked)
					selectionCount++;
			}
		}
		else
		{
			if (checkBoxArray.checked) selectionCount++;
		}
	}
	return selectionCount;
}
function checkType(obj,contentType)
{
	if (contentType == "" )
	return true;
	if ( contentType == "integer" )
	{
		stringSet="0123456789.";
		returnString="";
		s=obj.value;
		cnt =0;
		for (i = 0; i < s.length; i++)
		{   
			var c = s.charAt(i);
			if ((c == '.' ) && (cnt > 0 ))
				break;
				
			if (c == '.' ) 
				cnt++;
			if ((c == '.') || (c >=0 && c<=9)) 
			{
				returnString += c;
			}
		}
		obj.value = returnString;
		obj.focus();
	}
	
}
function doAction(obj,controlID,url,doCheck,displayMessage)
{
	if (doCheck)
	{
		selectedRecs = QL_GetSelectionCount(controlID);
		if (selectedRecs == 0 )
		{
			alert("No Items were selected");
			return false;
		}
	}	
	arrParam=  url.split(":");
	if (displayMessage.length == 0)
	{

		obj.form.file.value=arrParam[3];
		obj.form.hid_page_type.value=arrParam[1];
		if(obj.form.hid_page_type.value=="A")
		{
			obj.form.hid_table_id.value=arrParam[2];
		}
		else if(obj.form.hid_page_type.value=='N')
		{			
			window.location=arrParam[3];
			return false;
		}
		else		
		{
			// checking no of rows
			var src = document.getElementById(controlID + "_rows");
			if (!src && src.value <= 0)
				return false;
			obj.form.hid_button_id.value=arrParam[0];
		}
		obj.form.submit();	
		return false;
	}
	if (obj.form.hid_page_type.value!="A")
	{
			// checking no of rows
			var src = document.getElementById(controlID + "_rows");
			if (!src && src.value <= 0)
			return false;
	}	
	ans = confirm(displayMessage);
	if (ans)
	{
		obj.form.file.value=arrParam[3];
		obj.form.hid_page_type.value=arrParam[1];

		if(obj.form.hid_page_type.value=="A")
		{
			obj.form.hid_table_id.value=arrParam[2];
		}		
		else
		{
			obj.form.hid_button_id.value=arrParam[0];
		}
		obj.form.submit();	
	}

}

