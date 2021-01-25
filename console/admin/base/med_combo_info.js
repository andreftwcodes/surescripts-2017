/***
 * This javascript file contains function which is used for generate Combo
 * @author     		MediTab Software Inc.
 * @copyright  		1997-2005 The MediTab Software Inc.
 * @license    		http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version   	 	Release: 1.0
 * @PHP version  	5.x
*/

function generateComboDetail()
{
	//var class_name,str;
	newRows 	= parseInt(document.frm_add_combo_info.txt_generate_rows.value);
	iteration	= parseInt(document.frm_add_combo_info.hid_generateRows.value);
	FIELD_NAME	= eval(document.frm_add_combo_info.txt_generate_rows);
	if(document.frm_add_combo_info.txt_generate_rows.value.search(/^[0-9]*$/))
	{
		alert("Please Enter Numeric Value for New Rows");
		document.frm_add_combo_info.txt_generate_rows.focus();		
		setStyle(FIELD_NAME);
		eval(strStyle);
		return false;
	}
	else
	{
		resetStyle(FIELD_NAME);
		eval(strStyle);
	}
	totRows = iteration + newRows;
	document.frm_add_combo_info.hid_generateRows.value	= totRows;
	var str = "<table border=0 cellpadding=0 cellspacing=0 width=100% align=left>";
	for(i=0;i<newRows;i++)
	{
		if(iteration%2 == 0)
			var className = 'data1';
		else
			var className = 'data2';
		str += "<tr class="+className+" onmouseover='QL_MOver(this)' onmouseout='QL_MOut(this)'>";
		str += "<td align=left width='40%'><input type='text' name='Mltxt_"+iteration+"_key' id='Mltxt_"+iteration+"_key' class=comn-input size=30 value=''></td>";
		str += "<td align=left width='40%'><input type='text' name='Mltxt_"+iteration+"_key_value' id='Mltxt_"+iteration+"_key_value' class=comn-input size=30 value=''></td>";
		str += "<td align=center width='10%'>&nbsp;<input type='text' name='Mltxt_"+iteration+"_seq_no' id='Mltxt_"+iteration+"_seq_no' class=comn-input size=5 value=''></td>";
		str += "<td align=center width='10%'>&nbsp;<input type='text' name='Mltxt_"+iteration+"_position' id='Mltxt_"+iteration+"_position' class=comn-input size=5  maxlength=1 value='' ></td>";
		str	+= "</tr>";
		iteration++;
	}
	str += "</table>";

document.getElementById("rowgenerate").innerHTML = str;
}

function extraValid()
{
	intTot 		= document.frm_add_combo_info.hid_generateRows.value;
	alertMsg	= "";
	for(intId=0;intId<intTot;intId++)
	{
		seq_no = eval("document.frm_add_combo_info.Mltxt_"+intId+"_seq_no");
		if(seq_no.value.search(/^[0-9]*$/))
		{
			alertMsg += "\nPlease Enter Numeric Value for Seq no "+(intId+1)+".";
			setStyle(seq_no);
		}
		else
		{
			resetStyle(seq_no);
		}
		
		position = eval("document.frm_add_combo_info.Mltxt_"+intId+"_position");
		if(position.value != "A" && position.value != "")
		{
			alertMsg	+= "\nPlease Enter value as 'A' or Blank for position "+(intId+1)+".";
			setStyle(position);
		}
		else
		{
			resetStyle(position);
		}

	}
	if(alertMsg != "")
	{
		alertMsg = "Following errors:\n" + alertMsg;	
		alert(alertMsg);	
		eval(strStyle);
		eval(strFocus);
		return false
	}
}