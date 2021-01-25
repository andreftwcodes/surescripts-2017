/********************************************************************************************************************************
	Company Name 	:	Meditab Softwares
	Created On		: 	15 April 2006	
	File Name		:	common.js
	Purpose			:   This File Includes all the common functions to check javascript validations....
*********************************************************************************************************************************/

/*********************************************** Functions Starts ***************************************************************/
/********************************************************************************************************************************
	Created On		: 	19 April 2006	
	Function Name	:	checkValid
	Arguments		:	Id Of the form
	Purpose			:   it will check validation for all the fields of form
*********************************************************************************************************************************/
/*
cases....
Ap : Alphabetic
Dt : Date
In : Integer
Dc : Decimal
Zp : Zip
Em : Email
Ph : Phone
Fx : Fax
Ta : Normal Required Field
Ur : URL
Ft : float
R  : Required Field

Required Field should come after data type... e.g. ApRtxt_name
*/
var selcount=0;
var itercount=0;
var alertMessage="";	// the alert nessage
var alertfield;
var strStyle="";
var strFocus="";
var strReOneOrMoreDigits = /[\d+]/;
var strReNoDigits = /[^\d]/gi;
var strSesstionExpireMsg = "Sorry, your current session has been expired! Please re-login to the site.";
function checkValid(FORMID)
{
	//alert(FORMID);
	var frmname=document.getElementById(FORMID);
	var blnflag=true;
	alertMessage="";	// reset the alert nessage
	alertfield="";
	strStyle="";
	strFocus="";
	selcount=0;
	itercount=0;
	
	//Get no of elements of form...
	var frmcnt=document.getElementById(FORMID).elements.length;

	for(var i=0;i<frmcnt;i++)
	{
		//alert(frmname.elements[i].name)
		try
		{
			MESSAGE=eval("LBL_"+frmname.elements[i].name);
		}
		catch(e)
		{
			MESSAGE="Field";
		}
		//alert(frmname.elements[i].type+' : '+frmname.elements[i].name+' : '+blnflag+'  : '+alertMessage);
		if(frmname.elements[i].type=='text' && !frmname.elements[i].name.match(/\[]/))		// handling of various textbox validations
		{	
			// first 2 characters of the textbox name are to identify type of validation...
			

			
			var ChkType=frmname.elements[i].name.substr(0,2);
			//third character is to identify required field...
			var RChr=frmname.elements[i].name.substr(2,1);
			
			//alert(frmname.elements[i].name+' '+ChkType+'    '+RChr+'  ');			
			
			var REQUIRED="";
			if(RChr=="R")
			{
				REQUIRED=true;
			}					
			else
			{
				REQUIRED=false;	
			}
			//Generate name of the field...
			
			FIELD_NAME=eval("document."+FORMID+"."+frmname.elements[i].name);			
			switch(ChkType)
			{
				case "Ap" :
				
					if(!checkAlpha(FIELD_NAME,MESSAGE,REQUIRED))
					{
						blnflag = false;
					}
					break;
				case "Dt" :
					if(!checkDate(FIELD_NAME,MESSAGE,REQUIRED))
					{
						blnflag = false;
					}
					break;
				case "In" :
					if(!checkInteger(FIELD_NAME,MESSAGE,REQUIRED))
					{
						blnflag = false;
					}
					break;
				case "Ft" :
					var arrName=frmname.elements[i].name.split("txt");
					var strRequired=arrName[0].substr(arrName[0].length - 1 ,1);
					if(strRequired == 'R')
					{
						REQUIRED=true;
						var strLength = (arrName[0].length - 1);
					}					
					else
					{
						REQUIRED=false;
						var strLength = (arrName[0].length);
					}
					var ChkCharID = arrName[0].substr(2 ,strLength - 2); 
					if(!checkFloat(FIELD_NAME,MESSAGE,ChkCharID,REQUIRED))
					{
						blnflag = false;
					}
					break;
				case "Dc" :
					if(!checkDecimal(FIELD_NAME,MESSAGE,REQUIRED))
					{
						blnflag = false;
					}
					break;
				case "Zp" :
					if(!checkZip(FIELD_NAME,MESSAGE,REQUIRED))
					{
						blnflag = false;
					}
					break;
				case "Em" :
					if(!checkEmail(FIELD_NAME,MESSAGE,REQUIRED))
					{	//alert("z");
						blnflag = false;
					}
					break;
				case "Ph" :
					if(!checkPhone(FIELD_NAME,MESSAGE,REQUIRED))
					{
						blnflag = false;
					}
					break;
				case "Fx" :
					if(!checkFax(FIELD_NAME,MESSAGE,REQUIRED))
					{
						blnflag = false;
					}
					break;
				case "Ta" :
					if(REQUIRED == true)
					{
						if(checkBlank(FIELD_NAME,MESSAGE))
						{
							blnflag = false;
						}
					}
					break;
				case "Ur" :
					if(!checkURL(FIELD_NAME,MESSAGE,REQUIRED))
					{
							blnflag = false;
					}
					break;
					
			}			
		}
		else if(frmname.elements[i].type=='textarea')
		{			
			var ChkType=frmname.elements[i].name.substr(0,2);
			//third character is to identify required field...
			var RChr=frmname.elements[i].name.substr(2,1);
			var REQUIRED="";
			if(RChr=="R")
			{
				REQUIRED=true;
			}					
			else
			{
				REQUIRED=false;	
			}
			//Generate name of the field...
			
			FIELD_NAME=eval("document."+FORMID+"."+frmname.elements[i].name);
			
			if(frmname.elements['hid_editor'] != undefined)
			{
				if(frmname.elements['hid_editor'].name == 'hid_editor')
				{
					var strValue = getTxtAreaValue(frmname.elements[i].name);
					FIELD_NAME.value = strValue;
				}
			}
			if(REQUIRED == true)
			{
				//alert(FIELD_NAME);
				if(checkBlank(FIELD_NAME,MESSAGE))
				{
					blnflag = false;
				}
			}	
		}
		else if(frmname.elements[i].type=='password')		// handling of various textbox validations
		{
			var RChr=frmname.elements[i].name.substr(2,1);
			var REQUIRED="";
			if(RChr=="R")
			{
				REQUIRED=true;
			}					
			else
			{
				REQUIRED=false;	
			}
			FIELD_NAME=eval("document."+FORMID+"."+frmname.elements[i].name);
			if(REQUIRED == true)
			{
				//alert(FIELD_NAME);
				if(checkBlank(FIELD_NAME,MESSAGE))
				{
					blnflag = false;
				}
			}		
		}
		else if(frmname.elements[i].type=='checkbox')		// handling of checkbox validations
		{
			// for checkbox first character to identify the required field
			var RChr=frmname.elements[i].name.substr(0,1);
			var REQUIRED="";
			if(RChr=="R")
			{
				REQUIRED=true;
			}					
			else
			{
				REQUIRED=false;	
			}

			if(REQUIRED == true)
			{
				//Generate name of the field...
				FIELD_NAME=eval("document."+FORMID+"."+frmname.elements[i].name);
				if(!checkMultiSelectBox(FIELD_NAME,MESSAGE,REQUIRED))
				{
					blnflag = false;
				}
			}
		}
		else if(frmname.elements[i].type=='radio')			// handling of radio button validations
		{
			// for checkbox first character to identify the required field
			var RChr=frmname.elements[i].name.substr(0,1);
			var REQUIRED="";
			if(RChr=="R")
			{
				REQUIRED=true;
			}					
			else
			{
				REQUIRED=false;	
			}
			//Generate name of the field...
			FIELD_NAME=eval("document."+FORMID+"."+frmname.elements[i].name);
			if(REQUIRED==true)
			{
				if(!checkMultiSelectBox(FIELD_NAME,MESSAGE,REQUIRED))
				{
					blnflag = false;
				}
			}
		}
		else if(frmname.elements[i].type=='file' && !frmname.elements[i].name.match(/\[]/))
		{
			// for file input the first character to identify the required field
			var RChr=frmname.elements[i].name.substr(0,1);
			var REQUIRED="";
			if(RChr=="R")
			{
				REQUIRED=true;
			}					
			else
			{
				REQUIRED=false;	
			}
			//Generate name of the field...
			FIELD_NAME=eval("document."+FORMID+"."+frmname.elements[i].name);
			if(RChr=="R")
			{
				var strType=frmname.elements[i].name.substr(4,3);
			}
			else
			{
				var strType=frmname.elements[i].name.substr(3,3);	
			}
			strType=strType.toUpperCase()
			if(strType=="IMG")
			{
				var EXTENSIONS="jpg,jpeg,png,gif";	// valid file extensions				
			}
			else if(strType=="DOC")	
			{
				var EXTENSIONS="txt,doc,pdf,xls,csv";	// valid file extensions	
			}
			else if(strType=="VID")	
			{
				var EXTENSIONS="mp3,3gp,gif,avi,mpeg";	// valid file extensions
			}
			else
			{
				var EXTENSIONS="";	// valid file extensions
			}
			if(!checkFileName(FIELD_NAME,MESSAGE,REQUIRED,EXTENSIONS))
			{
						blnflag = false;
			}			
		}
		else if(frmname.elements[i].type=='select-one')			// handling of select box validations
		{
			// for file input the first character to identify the required field
			var RChr=frmname.elements[i].name.substr(0,1);
			var REQUIRED="";
			var multi=false;
			if(RChr=="R")
			{
				REQUIRED=true;
			}					
			else
			{
				REQUIRED=false;	
			}
			//Generate name of the field...
			var sfldname;
			if(frmname.elements[i].name.match(/\[]/))	// array of select fields
			{
				multi = true;
				sfldname=frmname.elements[i];	// get the select field element for select array
			}
			else
			{
				FIELD_NAME=eval("document."+FORMID+"."+frmname.elements[i].name);					
			}


			if(REQUIRED == true && multi == false)
			{
				// if select then check for  selected according to required or not				
				if(checkSelectBox(FIELD_NAME,MESSAGE,REQUIRED))
				{
						blnflag = false;
				}			
			}
			else
			{
				// for select array atleast on selected
				if(multi == true)
				{
					// if select array then check for atleast one selected
					if(checkMultiDropDownBox(sfldname,MESSAGE,REQUIRED)) 
					{					
							blnflag = false;
					}	

				}
				
			}

		}
		else
		{

		}
	}

	// alert the fields and set the error style for the required fields
	if(blnflag == false && alertMessage!="")
	{
		alertMessage="Following errors:\n\n" + alertMessage;	
		alert(alertMessage);	
		eval(strStyle);
		eval(strFocus);
		return false;
	}
	else
	{
		eval(strStyle);
		return true;
	}
	
}


/********************************************************************************************************************************
	Created By		:	Hemita(hemitat@meditab.in)
	Created On		: 	15 April 2006	
	Function Name	:	checkBlank
	Arguments		:	Name of the field, Message to be displayed
	Purpose			:   it Will display tne message if the mendatory filed is Null
*********************************************************************************************************************************/

function checkBlank(FIELD_NAME,MESSAGE)
{
	//if value is null then alert message...
	if(Trim(FIELD_NAME.value) == '')
	{
		FIELD_NAME.value="";	
		alertMessage+="\nPlease enter value for "+MESSAGE+".";
		if(FIELD_NAME.type!='textarea')
		{
			setStyle(FIELD_NAME);
		}
		return true;
	}
	else
	{	
		if(FIELD_NAME.type!='textarea')
		{
			resetStyle(FIELD_NAME);			
		}
		return false;	
	}
}


/********************************************************************************************************************************
	Created By		:	Hemita(hemitat@meditab.in)
	Created On		: 	18 April 2006	
	Function Name	:	checkAlpha
	Arguments		:	Name of the, Message to be displayed, Required Field
	Purpose			:   it Will display tne message if the mendatory filed is Null
*********************************************************************************************************************************/
function checkAlpha(FIELD_NAME,MESSAGE,REQUIRED)
{
	//Check For Mendatory field
	if(REQUIRED && checkBlank(FIELD_NAME,MESSAGE)) {return false;}
	if(FIELD_NAME.value.search(/^[A-Za-z]*$/))
	{
		alertMessage+="\nPlease enter valid "+MESSAGE+".";
		fldname=FIELD_NAME;
		setStyle(FIELD_NAME);
		return false;
	}
	else
	{	
		resetStyle(FIELD_NAME);			
		return true;	
	}
}


/********************************************************************************************************************************
	Created By		:	Hemita(hemitat@meditab.in)
	Created On		: 	18 April 2006	
	Function Name	:	checkDate
	Arguments		:	Name of the field, message to be displayed, Required Field
	Purpose			:   it Will check Date Formate it should be mm-dd-yyyy
*********************************************************************************************************************************/
function checkDate(FIELD_NAME,MESSAGE,REQUIRED)
{
	//Check For Mendatory field
	if(REQUIRED && checkBlank(FIELD_NAME,MESSAGE)) {return false;}
	
	if(FIELD_NAME.value!="")
	{
		if(!isDate(FIELD_NAME.value))
		{
			//FIELD_NAME.focus();
			fldname=FIELD_NAME;
			setStyle(FIELD_NAME);
			return false;
		}
	
	}
	resetStyle(FIELD_NAME);
	return true;	
}


/********************************************************************************************************************************
	Created By		:	Hemita(hemitat@meditab.in)
	Created On		: 	15 April 2006	
	Function Name	:	dateCompare
	Arguments		:	Sart Date, End Date, Lable of start date, lable of end date
	Purpose			:   it Will compare start date & end date. if start date>end date then it will give alert message...
*********************************************************************************************************************************/
function dateCompare(START_DATE,END_DATE,MESSAGE_START,MESSAGE_END)
{
	//if both start date & end date are not null then
	sadate=Array();
	eadate=Array();
	sadate=START_DATE.value.split("-");
	eadate=END_DATE.value.split("-");
	
	sdate=new Date(sadate[2],sadate[0]-1,sadate[1]);
	edate=new Date(eadate[2],eadate[0]-1,eadate[1]);
	//compare both start date & end date. if start date is greater then end date then alert message...
	if(sdate>edate)	
	{
		alert(MESSAGE_END+" can not be less than "+MESSAGE_START+".");
		fldname=END_DATE;
		setStyle(END_DATE);
		eval(strStyle);
		END_DATE.focus();
		return false;
	}
	else
	{
		return true;
	}
}
function dateGCompare(START_DATE,END_DATE,MESSAGE_START,MESSAGE_END)
{
	//if both start date & end date are not null then
	sadate=Array();
	eadate=Array();
	sadate=START_DATE.value.split("-");
	eadate=END_DATE.value.split("-");
	
	sdate=new Date(sadate[2],sadate[0]-1,sadate[1]);
	edate=new Date(eadate[2],eadate[0]-1,eadate[1]);
	//compare both start date & end date. if start date is greater then end date then alert message...
	if(sdate<edate)	
	{
		alert(MESSAGE_END+" can not be greater than "+MESSAGE_START+".");
		fldname=END_DATE;
		setStyle(END_DATE);
		eval(strStyle);
		END_DATE.focus();
		return false;
	}
	else
	{
		return true;
	}
}
/***
 * This function is used to check for future date
 * It Will compare date with cureeent date
 * @param  		CURRENT_DATE	String		current date
 * @param 		FIELD_DATE		String 		Date to be compared
 * @param 		MESSAGE			String 		Message to be displayed
 * @return		true/false		Bollean
**/
function checkFutureDate(CURRENT_DATE,FIELD_DATE,MESSAGE)
{
	// If both Current date & date to be compared are not null then...
	sadate	=	Array();
	cdate	=	Array();
	sadate	=	FIELD_DATE.value.split("-");
	cadate	=	CURRENT_DATE.value.split("-");
	sdate	=	new Date(sadate[2],sadate[0]-1,sadate[1]);
	cdate	=	new Date(cadate[2],cadate[0]-1,cadate[1]);

	// Compare both current date & field date. if field date date is greater then current date then alert message...
	if(sdate>cdate)	
	{
		alert(MESSAGE+' can not be future date.');
		fldname=FIELD_DATE;
		setStyle(FIELD_DATE);
		eval(strStyle);
		FIELD_DATE.focus();
		return false;
	}
	else
	{
		return true;
	}
}

/********************************************************************************************************************************
	Created By		:	Hemita(hemitat@meditab.in)
	Created On		: 	15 April 2006	
	Function Name	:	checkInteger
	Arguments		:	Name of the field, message to be displayed, Required Field
	Purpose			:   check for numeric value
*********************************************************************************************************************************/
function checkInteger(FIELD_NAME,MESSAGE,REQUIRED)
{
	//Check For Mendatory field
	if(REQUIRED && checkBlank(FIELD_NAME,MESSAGE)) {return false;}
	//Allow numbers only...
	if(FIELD_NAME.value.search(/^[0-9]*$/))
	{
		alertMessage+="\nPlease enter valid "+MESSAGE+".";
		fldname=FIELD_NAME;
		setStyle(FIELD_NAME);
		return false;
	}
	else
	{
		resetStyle(FIELD_NAME);			
		return true;
	}
}


/********************************************************************************************************************************
	Created By		:	Hemita(hemitat@meditab.in)
	Created On		: 	21 April 2006	
	Function Name	:	checkFloat
	Arguments		:	Name of the field, message to be displayed,no of digits after floating point, Required Field
	Purpose			:   check for numeric value
*********************************************************************************************************************************/

function checkFloat(FIELD_NAME,MESSAGE,CHARNO,REQUIRED)
{
	var stringsearch="/^[0-9]+(\.)?[0-9]{0,"+CHARNO+"}$/";
	//Check For Mendatory field
	if(REQUIRED && checkBlank(FIELD_NAME,MESSAGE)) {return false;}
	//Allow numbers only...
	if(FIELD_NAME.value.search(eval(stringsearch)))
	{
		alertMessage+="\nPlease enter valid "+MESSAGE+".";
		fldname=FIELD_NAME;
		setStyle(FIELD_NAME);
		return false;
	}
	else
	{
		resetStyle(FIELD_NAME);			
		return true;
	}
}


/********************************************************************************************************************************
	Created By		:	Hemita(hemitat@meditab.in)
	Created On		: 	15 April 2006	
	Function Name	:	checkDecimal
	Arguments		:	Name of the field, message to be displayed, Required Field
	Purpose			:   check for numeric value with decimal point...
*********************************************************************************************************************************/
function checkDecimal(FIELD_NAME,MESSAGE,REQUIRED)
{
	//Check For Mendatory field
	if(REQUIRED && checkBlank(FIELD_NAME,MESSAGE)) {return false;}
	//Allow numbers only...
	if(isNaN(FIELD_NAME.value))
	{
		alertMessage+="\nPlease enter valid "+MESSAGE+".";
		fldname=FIELD_NAME;
		setStyle(FIELD_NAME);
		return false;
	}
	else
	{
		resetStyle(FIELD_NAME);			
		return true;
	}
}


/********************************************************************************************************************************
	Created By		:	Hemita(hemitat@meditab.in)
	Created On		: 	15 April 2006	
	Function Name	:	checkZip
	Arguments		:	Name of the field, message to be displayed, Required Field
	Purpose			:   Check For Valid Zip
*********************************************************************************************************************************/
function checkZip(FIELD_NAME,MESSAGE,REQUIRED)
{
	//Check For Mendatory field
	if(REQUIRED && checkBlank(FIELD_NAME,MESSAGE)) {return false;}
	//Allow numbers only...
	//valid zip should contain 5 or 9 digits...
	if(FIELD_NAME.value!="")
	{
		if(FIELD_NAME.value.search(/^\d\d\d\d\d(\d\d\d\d)?$/))
		{
			alertMessage+="\nPlease enter valid "+MESSAGE+".";
			fldname=FIELD_NAME;
			setStyle(FIELD_NAME);
			return false;
		}
		else
		{	
			resetStyle(FIELD_NAME);			
			return true;	
		}
	}
	else
	{	
			resetStyle(FIELD_NAME);			
			return true;	
	}
}


/********************************************************************************************************************************
	Created By		:	Hemita(hemitat@meditab.in)
	Created On		: 	15 April 2006	
	Function Name	:	checkPhone
	Arguments		:	Name of the field, message to be displayed, Required Field
	Purpose			:   Check For Valid Phone
*********************************************************************************************************************************/
function checkPhone(FIELD_NAME,MESSAGE,REQUIRED)
{
	//Check For Mendatory field
	if(REQUIRED && checkBlank(FIELD_NAME,MESSAGE)) {return false;}
	if(FIELD_NAME.value!="")
	{
		if(FIELD_NAME.value.search(/^[0-9]{10}[0-9Xx]?[0-9]{0,3}$/))
		{
			alertMessage+="\nPlease enter valid "+MESSAGE+".";
			fldname=FIELD_NAME;
			setStyle(FIELD_NAME);
			return false;
		}
		else
		{	
			resetStyle(FIELD_NAME);			
			return true;	
		}
	}
	else
	{	
			resetStyle(FIELD_NAME);			
			return true;	
	}
}


/********************************************************************************************************************************
	Created By		:	Hemita(hemitat@meditab.in)
	Created On		: 	15 April 2006	
	Function Name	:	checkFax
	Arguments		:	Name of the field, message to be displayed, Required Field
	Purpose			:   Check For Valid Fax No.
*********************************************************************************************************************************/
function checkFax(FIELD_NAME,MESSAGE,REQUIRED)
{
	//Check For Mendatory field
	if(REQUIRED && checkBlank(FIELD_NAME,MESSAGE)) {return false;}
	if(FIELD_NAME.value!="")
	{
		if(FIELD_NAME.value.search(/^[0-9]{10}$/))
		{
			alertMessage+="\nPlease enter valid "+MESSAGE+".";
			fldname=FIELD_NAME;
			setStyle(FIELD_NAME);
			return false;
		}
		else
		{	
			resetStyle(FIELD_NAME);			
			return true;	
		}
		
	}
	else
	{	
			resetStyle(FIELD_NAME);			
			return true;	
	}
}


/********************************************************************************************************************************
	Created By		:	Hemita(hemitat@meditab.in)
	Created On		: 	15 April 2006	
	Function Name	:	checkEmail
	Arguments		:	Name of the field, message to be displayed, Required Field
	Purpose			:   Check For Valid Email
*********************************************************************************************************************************/
function checkEmail(FIELD_NAME,MESSAGE,REQUIRED)
{
	//Check For Mendatory field
	if(REQUIRED && checkBlank(FIELD_NAME,MESSAGE)) {return false;}
	if(FIELD_NAME.value!="")
	{
		if(FIELD_NAME.value.search(/^[\w-\.]+@[\w-\.]+\.\w+$/))
		{
			alertMessage+="\nPlease enter valid "+MESSAGE+".";
			fldname=FIELD_NAME;
			setStyle(FIELD_NAME);
			return false;
		}
	}
	else
	{	
			resetStyle(FIELD_NAME);
			return true;	
	}
}


/********************************************************************************************************************************
	Created By			:	Hemita(hemitat@meditab.in)
	Created On			: 	15 April 2006	
	Main Function Name	:	isDate
	Arguments			:	Date
	Purpose				:   Function For Date Validation
*********************************************************************************************************************************/

/**
 * DHTML date validation script. 
 */
// Declaring valid date character, minimum year and maximum year

var dtCh= "-";
var minYear=1900;
var maxYear=2100;


//Function checks all the characters are numbers or not...
function isInteger(s)
{
	var i;
    for (i = 0; i < s.length; i++)
	{   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}


function stripCharsInBag(s, bag)
{
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++)
	{   
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}


function daysInFebruary (year)
{
	// February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ((!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}


/*This function makes day Array...
if month = 2(feb)
then days = 29
if mont = 4,6,9,11
then days=30
else days=31
*/

function DaysArray(n) 
{
	for (var i = 1; i <= n; i++) 
	{
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) 
		{
			this[i] = 30
		}
		if (i==2) 
		{
			this[i] = 29
		}
   } 
   return this
}


//Check whether date format is correct or not...
function isDate(dtStr)
{
	var daysInMonth = DaysArray(12)
	var pos1=dtStr.indexOf(dtCh)
	var pos2=dtStr.indexOf(dtCh,pos1+1)
	var strMonth=dtStr.substring(0,pos1)
	var strDay=dtStr.substring(pos1+1,pos2)
	var strYear=dtStr.substring(pos2+1)
	strYr=strYear
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1)
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
	for (var i = 1; i <= 3; i++) 
	{
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
	}
	month=parseInt(strMonth)
	day=parseInt(strDay)
	year=parseInt(strYr)
	if (pos1==-1 || pos2==-1)
	{
		//if two "-" are not there in date then date is invalid...
		alertMessage+="\nInvalid date.The date format should be : mm-dd-yyyy";
		return false
	}
	if (strMonth.length<1 || month<1 || month>12)
	{
		//checking for month format...
		alertMessage+="\nPlease enter a valid month";
		return false
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month])
	{
		//Checking for day format...
		alertMessage+="\nPlease enter a valid day";
		return false
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear)
	{
		//Checking for Year Format...
		alertMessage+="\nPlease enter a valid 4 digit year between "+minYear+" and "+maxYear;
		return false
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false)
	{
		//check whether it is integer or not...
		alertMessage+="\nPlease enter a valid date";
		return false
	}
	return true
}


/********************************************************************************************************************************
	Created On		: 	15 April 2006	
	Function Name	:	formatDate
	Arguments		:	Date To be Formated,Date Format
	Purpose			:   It Will convert date in specific format.
*********************************************************************************************************************************/
function formatDate(DATE,DATE_FORMAT)
{
	FDate=DATE.split('-');	
	FirstFC=DATE_FORMAT.substr(0,1);
	SecondFC=DATE_FORMAT.substr(2,1);
	ThirdFC=DATE_FORMAT.substr(4,1);
	AddChr=DATE_FORMAT.substr(1,1);	
	var MONTH_NAMES=new Array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
	if(FirstFC=='Y' || FirstFC=='y')
	{
		//Check For First Place For Year...
		if(FirstFC=='Y')
		{
	  		FirstFC=FDate[0];	
		}
		else
		{
			FirstFC=FDate[0].substr(2,2);
		}
	}
	else if(FirstFC=='M' || FirstFC=='m')
	{
		//Check For First Place For Month...
		if(FirstFC=='M')	
		{
			FirstFC=MONTH_NAMES[FDate[1]-1];	
		}
		else
		{
			FirstFC=FDate[1];	
		}
	}
	else
	{
		FirstFC=FDate[2];
	}
	if(SecondFC=='Y' || SecondFC=='y')
	{
		//Check For Second Place For Year...
		if(SecondFC=='Y')
		{
	  		SecondFC=FDate[0];	
		}
		else
		{
			SecondFC=FDate[0].substr(2,2);
		}
	}
	else if(SecondFC=='M' || SecondFC=='m')
	{	
		//Check For Second Place For Month...
		if(SecondFC=='M')	
		{
			SecondFC=MONTH_NAMES[FDate[1]-1];	
		}
		else
		{
			SecondFC=FDate[1];	
		}
	}
	else
	{
		SecondFC=FDate[2];	
	}
	if(ThirdFC=='Y' || ThirdFC=='y')
	{
		//Check For Third Place For Year...
		if(ThirdFC=='Y')
		{
	  		ThirdFC=FDate[0];	
		}
		else
		{
			ThirdFC=FDate[0].substr(2,2);
		}
	}
	else if(ThirdFC=='M' || ThirdFC=='m')
	{
		//Check For Third Place For Month...
		if(ThirdFC=='M')	
		{
			ThirdFC=MONTH_NAMES[FDate[1]-1];	
		}
		else
		{
			ThirdFC=FDate[1];	
		}
	}
	else
	{
		ThirdFC=FDate[2];	
	}
	FDate=FirstFC+AddChr+SecondFC+AddChr+ThirdFC;
	return FDate;
}


/********************************************************************************************************************************
	Created By		:	Hemita(hemitat@meditab.in)
	Created On		: 	17 April 2006	
	Function Name	:	checkAll
	Arguments		:	name of checkbox, checked status 
	Purpose			:   Function For Select & Deselect All Checkboxes...
*********************************************************************************************************************************/

function checkAll(CHECKBOX_NAME,CHECKED) 
{
	var name=document.getElementsByName(CHECKBOX_NAME);
	var field = name.length;
	var chkval=(CHECKED=="true")?"yes":"no";
	for (i = 0; i < field; i++) 
	{
		var str=eval(name[i]);
		str.checked=eval(CHECKED);
		//str.value=chkval;		
	}
}
function checkAll(CHECKBOX_ARRAY,CHECKALL) 
{
	var name=document.getElementsByName(CHECKBOX_ARRAY);
	var field = name.length;
	for (i = 0; i < field; i++) 
	{
		var str=eval(name[i]);
		str.checked=document.getElementById(CHECKALL).checked;
	}
}


/********************************************************************************************************************************
	Created By		:	Hemita(hemitat@meditab.in)
	Created On		: 	20 April 2006	
	Function Name	:	checkSelect
	Arguments		:	Name of the field
	Purpose			:   Check For Selected Check box.
*********************************************************************************************************************************/
function checkSelect(FIELD_NAME)
{
	var field=document.getElementsByName(FIELD_NAME);
	var chkselect = false;
	for(var i=0;i<field.length;i++)
	{
		var str=eval(field[i]);
		if(str.checked)
		{
			chkselect=true;
			break;
		}
	}
	if(chkselect == false)
	{
		alertMessage+="\nPlease select atleast one checkbox.";
		fldname=FIELD_NAME;
		setStyle(FIELD_NAME);
		return false;
	}
	else
	{
		resetStyle(FIELD_NAME);
		return true;
	}
}


/********************************************************************************************************************************
	Created By		:	Hemita(hemitat@meditab.in)
	Created On		: 	18 April 2006	
	Function Name	:	ftoupper
	Arguments		:	name of the field
	Purpose			:   Function For Convert string in Uppercase
*********************************************************************************************************************************/

function ftoupper(objId)
{
	sId = objId.value
	objId.value = sId.toUpperCase() ;
}


/********************************************************************************************************************************
	Created By		:	Hemita(hemitat@meditab.in)
	Created On		: 	18 April 2006	
	Function Name	:	ftolower
	Arguments		:	name of the field
	Purpose			:   Function For Convert string in lowercase
*********************************************************************************************************************************/

function ftolower(objId)
{
	sId = objId.value
	objId.value = sId.toLowerCase() ;
}


/********************************************************************************************************************************
	Created By		:	Hemita(hemitat@meditab.in)
	Created On		: 	20 April 2006	
	Function Name	:	Trim
	Arguments		:	Sring
	Purpose			:   Function Will Trim the String
*********************************************************************************************************************************/

function Trim(str)
{ 
 lenstr = str.length
 substr = str
 for (i=0;i<lenstr;i++)
 {
	c = str.charAt(i);	
	if(c == ' ' || c == '\n'  || c == '\r')
		substr = str.substring(i+1,str.length);
	else
		break   
 }

 lenstr = substr.length
 str = substr 
 for (i=lenstr-1;i>0;i--)
 {
	c = str.charAt(i);
	if(c == ' ' || c == '\n' || c == '\r')
		substr = str.substring(0,i)		
	else
		break   
 }   
 return substr	
}

/********************************************************************************************************************************
	Created By		:	Hemita(hemitat@meditab.in)
	Created On		: 	21 April 2006	
	Function Name	:	checkURL
	Arguments		:	Name Of the field, message to be displayed, Required Field
	Purpose			:   Function Will Check For Valid URl
*********************************************************************************************************************************/
function checkURL(FIELD_NAME,MESSAGE,REQUIRED)
{
	//Check For Mendatory field
	if(REQUIRED && checkBlank(FIELD_NAME,MESSAGE)) {return false;}
	if(FIELD_NAME.value != "")
	{
		if(FIELD_NAME.value.search(/^((www.)|(((http)(s)?\:\/\/)(www.)?))[\w\/\-\._\-\~?&=:]+\w+$/))
		{
			alertMessage+="\nPlease enter valid "+MESSAGE+".";
			//FIELD_NAME.focus();
			return false;
		}
	}
	return true;

}


/********************************************************************************************************************************
	Created By		:	Hemita(hemitat@meditab.in)
	Created On		: 	21 April 2006	
	Function Name	:	checkSelectBox
	Arguments		:	Name Of the field, message to be displayed
	Purpose			:   Function Will Check For Select box value is selected or not
*********************************************************************************************************************************/
function checkSelectBox(FIELD_NAME,MESSAGE)
{
	//if value is null then alert message...
	if(FIELD_NAME.value == '')
	{
		alertMessage+="\nPlease select atleast one value for "+MESSAGE+".";
		fldname=FIELD_NAME;
		setStyle(FIELD_NAME);
		return true;
	}
	else
	{
		resetStyle(FIELD_NAME);
		return false;
	}
}


/********************************************************************************************************************************
	Created By		:	Hemita(hemitat@meditab.in)
	Created On		: 	21 April 2006	
	Function Name	:	numberCompare
	Arguments		:	Name of First field, Name if Second Field,First Message To BE displayed, Second Message To BE Displayed
	Purpose			:   Function Will ompare two numbers
*********************************************************************************************************************************/
function numberCompare(NUMBER1,NUMBER2,MESSAGE1,MESSAGE2)
{
	if(NUMBER1.value > NUMBER2.value)
	{
		alertMessage+="\n"+MESSAGE1+" is greater than "+MESSAGE2;
		fldname=FIELD_NAME;
		setStyle(FIELD_NAME);
		return false;
	}
	return true;
}


/********************************************************************************************************************************
	Created By		:	Amit John(amitj@meditab.in)
	Created On		: 	12 May 2006	
	Function Name	:	checkMultiSelectBox
	Arguments		:	Name Of the field, message to be displayed, Required Field
	Purpose			:   Function Will Check For at least one Checked Check Box
*********************************************************************************************************************************/

function checkMultiSelectBox(FIELD_NAME,MESSAGE,REQUIRED)
{
	var blncflag=false;					// boolean flag for the check box
	var i=0;
	var fldname="";

	if(FIELD_NAME.length!=undefined)	// if multiple check boxes
	{
		for(i=0;i<FIELD_NAME.length && blncflag==false;i++){	
			fldname=FIELD_NAME[i];
			if(FIELD_NAME[i].checked)
			{	blncflag=true;	}
		}
	}
	else								// if single check box
	{		fldname=FIELD_NAME;
			if(FIELD_NAME.checked)
			{	blncflag=true;	}
	}
	
		if(blncflag==false)//(alertfield!=fldname.name && alertfield!=undefined))
		{	

			if(alertfield!=fldname.name || alertfield=="")
			{
				if(alertfield=="")
				{
					alertfield=fldname.name;
				}
				
				alertMessage+="\nPlease select the " + MESSAGE+" ";								
				//setStyle(fldname);
			}
				if(alertfield!="" && alertfield!=fldname.name)
				{
					alertfield=fldname.name;
				}
			
			return false;
		}
		else
		{	
			resetStyle(fldname);		
			return true;	
		}
}



/********************************************************************************************************************************
	Created By		:	Amit John(amitj@meditab.in)
	Created On		: 	12 June 2006	
	Function Name	:	checkMultiDropDownBox
	Arguments		:	Name Of the field, message to be displayed, Required Field
	Purpose			:   Function Will Check For at least one selected Select Box
*********************************************************************************************************************************/


function checkMultiDropDownBox(FIELD_NAME,MESSAGE,REQUIRED)
{
	var blncflag=false;					// boolean flag for the select box
	var i=0;
	var fldname="";
	var maxcount=document.getElementsByName(FIELD_NAME.name).length;
	itercount=itercount+1;
	
		// if multiple options or not
		if(FIELD_NAME.length!=undefined)	
		{
			for(i=0;i<FIELD_NAME.length && blncflag==false;i++)
			{	
					fldname=FIELD_NAME[i];
					// if a non null option has been selected
					if(fldname.selected && fldname.value!="")
					{
						selcount=selcount+1;
						blncflag=true;
					}
			}
		}
		else								
		{	
				fldname=FIELD_NAME;
				if(FIELD_NAME.selected)
				{	blncflag=true;	}
		}

		// if no select boxes of the select array have been set then set the alert message
		if(blncflag==false && selcount==0 && itercount==maxcount)
		{
			if(alertfield!=fldname.name || alertfield=="")
			{
				if(alertfield=="")
				{
					fldname=FIELD_NAME;
					alertfield=fldname.name;
				}
				// if none of array of select boxes have been set
				if(selcount==0 && itercount==maxcount)
				{
					selcount=selcount+1;
					alertMessage+="\nPlease select the " + MESSAGE+" ";								
				}
			}

			// if none of array of select boxes have been set
			if(selcount==0 && itercount==maxcount)
			{
				return false;
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


/********************************************************************************************************************************
	Created By		:	Amit John(amitj@meditab.in)
	Created On		: 	2 May 2006	
	Function Name	:	checkFileName
	Arguments		:	Name Of the field, message to be displayed, Required Field, Valid File Types Extensions
	Purpose			:   Function Will Check For at Valid File Types and required files
*********************************************************************************************************************************/

function checkFileName(FIELD_NAME,MESSAGE,REQUIRED,EXTENSIONS)
{
	//Check For Mendatory field
	if(REQUIRED && checkBlank(FIELD_NAME,MESSAGE)) {return false;}
	if(FIELD_NAME.value != '' && EXTENSIONS!='')
	{
		var arrfiletypes=new Array();				// the array of valid file types
		arrfiletypes=EXTENSIONS.split(',');			// get the valid file types in the array
		var blnvalidflag=false;						// this boolean flag indicates a valid file type
		var arrfilename=new Array();				// separate filename and the extension
		arrfilename=FIELD_NAME.value.split('.');	// get the file extension in an array
		if(arrfilename.length>1)
		{
			for(var c=0;c<arrfiletypes.length && blnvalidflag==false;c++) // check the file extension validity
			{
				var strMainExt=arrfilename[1].toUpperCase();
				var strExt=arrfiletypes[c].toUpperCase();
				if(strMainExt==strExt)
				{
					blnvalidflag=true;					// file type is valid so set the flag
					break;
				}
			}
		}
		else
		{
			blnvalidflag=false;					// file type is not valid so set the flag	
		}
		
		if(blnvalidflag == false)					// file is not a valid file type
		{
			alertMessage+="\nPlease select a valid "+MESSAGE+" of following types "+EXTENSIONS;
			fldname=FIELD_NAME;
			setStyle(FIELD_NAME);
			return false;
		}
		else										// file is valid
		{	
			resetStyle(FIELD_NAME);				
			return true;	
		}

	}
}



/********************************************************************************************************************************
	Created By		:	Amit John(amitj@meditab.in)
	Created On		: 	12 May 2006	
	Function Name	:	setStyle
	Arguments		:	Name Of the field
	Purpose			:   Function Will set style for the control
*********************************************************************************************************************************/

function setStyle(FIELD_NAME)
{
	if(FIELD_NAME!=undefined)
	{
		if(navigator.appName=="Netscape")
		{
				if(strFocus=="")
				{
					if(document.getElementById(FIELD_NAME.id)!=undefined)
					{
						strFocus="document.getElementById('"+FIELD_NAME.id +"').focus();";
					}
					else if(document.getElementsByName(FIELD_NAME.name)!=undefined)
					{
						if(document.getElementsByName(FIELD_NAME.name).length>1)
						{
							strFocus="document.getElementsByName('"+FIELD_NAME.name +"')[0].focus();";
						}
						else
						{
							strFocus="document.getElementsByName('"+FIELD_NAME.name +"').focus();";	
						}
					}
				}
			//Cross browser script set the class attribute of the control 
			//FIELD_NAME.setAttribute('className','err');	
			if(document.getElementById(FIELD_NAME.name)!=undefined)
			{
				strStyle+="document.getElementById('"+FIELD_NAME.name +"').className='err';\n";			
			}
			if(document.getElementsByName(FIELD_NAME.name)!=undefined)
			{
				strStyle+="document.getElementsByName('"+FIELD_NAME.name + "').className='err';\n";					
			}
		}
		else
		{
				if(strFocus=="")
				{
					if(document.getElementById(FIELD_NAME.id)!=undefined)
					{
						strFocus="document.getElementById('"+FIELD_NAME.id +"').focus();";
					}
					else if(document.getElementsByName(FIELD_NAME.name)!=undefined)
					{
						if(document.getElementsByName(FIELD_NAME.name).length>1)
						{
							strFocus="document.getElementsByName('"+FIELD_NAME.name +"')[0].focus();";
						}
						else
						{
							strFocus="document.getElementsByName('"+FIELD_NAME.name +"').focus();";	
						}

					}
				}		
			//Cross browser script set the class attribute of the control 
			//FIELD_NAME.className='err';			
			if(document.getElementById(FIELD_NAME.name)!=undefined)
			{
				strStyle+="document.getElementById('"+FIELD_NAME.name + "').className='err';\n";
			}
			if(document.getElementsByName(FIELD_NAME.name)!=undefined)
			{
				strStyle+="document.getElementsByName('"+FIELD_NAME.name + "').className='err';\n";	
			}
		}
	}

}



/********************************************************************************************************************************
	Created By		:	Amit John (amitj@meditab.in)
	Created On		: 	12 May 2006	
	Function Name	:	resetStyle
	Arguments		:	ID Of the form
	Purpose			:   Function Will set style for the control
*********************************************************************************************************************************/

function resetStyle(FIELD_NAME)
{
	if(FIELD_NAME!=undefined)
	{
		if(navigator.appName=="Netscape")
		{

			// cross browser script set the class attribute of the control 
			//FIELD_NAME.setAttribute('className','err');	
			if(document.getElementById(FIELD_NAME.name)!=undefined)
			{
				strStyle+="document.getElementById('"+FIELD_NAME.name +"').className='comn-input';\n";			
			}
			if(document.getElementsByName(FIELD_NAME.name)!=undefined)
			{
				strStyle+="document.getElementsByName('"+FIELD_NAME.name + "').className='comn-input';\n";					
			}
		}
		else
		{
			// cross browser script set the class attribute of the control 
			//FIELD_NAME.className='err';			
			if(document.getElementById(FIELD_NAME.name)!=undefined)
			{
				strStyle+="document.getElementById('"+FIELD_NAME.name + "').className='comn-input';\n";
			}
			if(document.getElementsByName(FIELD_NAME.name)!=undefined)
			{
				strStyle+="document.getElementsByName('"+FIELD_NAME.name + "').className='comn-input';\n";	
			}
		}
	}

}

/*********************************************** Functions Ends ***************************************************************/

/********************************************************************************************************************************
	Created By		:	Megha Patodia
	Created On		: 	17 May 2006	
	Function Name	:	popup
	Arguments		:	url of the popup page, width of the popup window, height of the popup window
	Purpose			:   Function will open the popup window
*********************************************************************************************************************************/

function popup(url,width,height)
{
	leftPosition=(screen.width)?(screen.width-width)/2:100;
	topPosition=(screen.height)?(screen.height-height)/2:100;
	testwindow=window.open(url+"&width="+width+"&height="+height, '', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width='+width+',height='+height+',left='+leftPosition+',top = '+topPosition+'');
	testwindow.window.focus();	
	return false;
}

/*********************************************** Functions Ends ***************************************************************/

/********************************************************************************************************************************
	Created By		:	Sandeep Chhaya
	Created On		: 	19 june 2006	
	Function Name	:	onFocusMask(strTextBox,strFormat) 
	Arguments		:	This function pass text box value and string format like data format("##-##-####");
	Purpose			:   Function will set Max Length on Set Focus.
*********************************************************************************************************************************/

function onFocusMask(strTextBox,strFormat) 
{
	var strVal = strTextBox.value;
	if(strVal.length == 0 || strVal == null) 
	{
		var i = strFormat.indexOf('#');
		strTextBox.value = strFormat.substring(0,i);
	}
	setCaretAtEnd(strTextBox);
	// set just in case.
	strTextBox.maxlength = strFormat.length;
}
/*********************************************** Functions Ends ***************************************************************/
/********************************************************************************************************************************
	Created By		:	Sandeep Chhaya
	Created On		: 	19 june 2006	
	Function Name	:	onBlurMask(strTextBox) 
	Arguments		:	String Text box value of blur Field
	Purpose			:   Function will pass blank value when no digit founds.
*********************************************************************************************************************************/

function onBlurMask(strTextBox) 
{
	var strVal = strTextBox.value;
	// if no digits....nada entered.....blank it.
	if(strReOneOrMoreDigits.test(strVal) == false) 
	{
		strTextBox.value = '';
	}
}
/*********************************************** Functions Ends ***************************************************************/
/********************************************************************************************************************************
	Created By		:	Sandeep Chhaya
	Created On		: 	19 june 2006	
	Function Name	:	doMask(strTextBox,strEvt,strFormat) 
	Arguments		:	String Text box value , pass "EVENT" for events property and Pass Maks Format like ("##-##-####");
	Purpose			:   Function will pass all the value to Masking function get Mask Value
*********************************************************************************************************************************/

function doMask(strTextBox,strEvt,strFormat) 
{
	var keyCode = strEvt.which ?strEvt.which : strEvt.keyCode;
	
	// enter, backspace, delete and tab keys are allowed thru
	if(keyCode == 13 || keyCode == 8 || keyCode == 9 || keyCode == 46)
		return true;
	
	// keyCodes so that it can be used
	var keyCharacter = cleanKeyCode(keyCode);
	
	// grab the strTextBox value and the mask
	var strVal = strTextBox.value;
	var mask = strFormat;
	
	// simple Regex to check if key is a digit
	if(strReOneOrMoreDigits.test(keyCharacter) == false)
		return false;

	// get value minus any masking by removing all non-numerics
	strVal = strVal.replace(strReNoDigits,'');			
	// add current keystroke
	//strVal += keyCharacter;
	// mask it...val holds the existing TextBox.value + the current keystroke
	strTextBox.value = MaskValue(strVal,mask);
	setCaretAtEnd(strTextBox);
	return false;
}
/*********************************************** Functions Ends ***************************************************************/
/********************************************************************************************************************************
	Created By		:	Sandeep Chhaya
	Created On		: 	19 june 2006	
	Function Name	:	MaskValue((strValue,strMask) 
	Arguments		:	String Text box value , Pass Maks Format
	Purpose			:   Function will return Mask Formated value to DoMask Function.
*********************************************************************************************************************************/

function MaskValue(strValue,strMask) 
{
	var retVal = strMask;
	var strVal = strValue;
	//loop thru mask and replace #'s with current value one at a time
	for(var i=0;i<strVal.length;i++) 
	{
		retVal = retVal.replace(/#/i, strVal.charAt(i));
	}
	retVal = retVal.replace(/#/gi, "");
	return retVal;
}
/*********************************************** Functions Ends ***************************************************************/
/********************************************************************************************************************************
	Created By		:	Sandeep Chhaya
	Created On		: 	19 june 2006	
	Function Name	:	cleanKeyCode(intKey)
	Arguments		:	int Key Code Genrated By Event Property.
	Purpose			:   Function will Return Formated String Key Code
*********************************************************************************************************************************/

function cleanKeyCode(intKey)
{
	switch(intKey)
	{
		case 96: return "0"; break;
		case 97: return "1"; break;
		case 98: return "2"; break;
		case 99: return "3"; break;
		case 100: return "4"; break;
		case 101: return "5"; break;
		case 102: return "6"; break;
		case 103: return "7"; break;
		case 104: return "8"; break;
		case 105: return "9"; break;
		default: return String.fromCharCode(intKey); break;
	}
}
/*********************************************** Functions Ends ***************************************************************/
/********************************************************************************************************************************
	Created By		:	Sandeep Chhaya
	Created On		: 	19 june 2006	
	Function Name	:	setCaretAtEnd (strField) 
	Arguments		:	String field value
	Purpose			:   String field set value Charcter at the End.
*********************************************************************************************************************************/

function setCaretAtEnd (strField) 
{
  if (strField.createTextRange) 
  {
    var strRange = strField.createTextRange();
    strRange.moveStart('character', strField.value.length);
    strRange.collapse();
    strRange.select();
  }
}
/*********************************************** Functions Ends ***************************************************************/


	/**
		This method is used to check whether the entered value is hour or not
	*/
	function checkValidHour(strField)
	{
		strFieldValue=strField.value;
		if(!isInteger(strFieldValue) || strField.value>24 || strField.value==0)
		{
			strField.value="";
			return false;
		}
		else
		{
			return true;
		}
	}
	
	
	function onBlurTimeMask(strTextBox) 
	{
		var strVal = strTextBox.value;
		
		// if no digits....nada entered.....blank it.
		
		if(strReOneOrMoreDigits.test(strVal) == false) 
		{
			strTextBox.value = '';
			return false;
		}
		if(strVal.indexOf(":")!=strVal.lastIndexOf(":"))
		{
			strTextBox.value = '';
			return false;	
		}
		var blnchange=true;
		
		for(i=0;i<strVal.length;i++)
		{
			var c = strVal.charAt(i);
			
        	if (((c < "0") || (c > "9")) && c!=":")
			{
				blnchange=false;
				strVal = strVal.replace(strVal.charAt(i),'0');
			}
		}
		
		if(!blnchange)
		{
			strTextBox.value = strVal;
			return false;
		}
		
		var arrVal=strVal.split(":");
		if(arrVal[0]>12) arrVal[0]="00";
		if(arrVal[1]>59) arrVal[1]="00";
		strTextBox.value = arrVal[0]+":"+arrVal[1];
				
	}
	
/*********************************************** Functions Ends ***************************************************************/
/**
	* This method is used to create the XMLHttpRequest Object
	* XMLHttpRequest this is the core AJAX object. It handles all the requests and responses
	* @return the http object.
*/
function createRequestObject()
{
	//declare the variable to hold the object.
	var objRequest; 
	//find the browser name
	var strBrowser = navigator.appName;
	
	//Check the type of browser and acc. create the object.
	if(strBrowser == "Microsoft Internet Explorer"){
		objRequest = new ActiveXObject("Microsoft.XMLHTTP");
	}else{
		objRequest = new XMLHttpRequest();
	}
	
	//return the object
	return objRequest;
}

/**
	* This method is used to call the page which will return the requested HTML control through ajax.
	* @param	string		strFieldName	having the name of the field
	* @param	string		strFieldId		having the table constant anf field Id.
	* @param	string		strRequestvars	having the var name and the html field name whose value is to be passed in that var
*/

var strCallHttpResponseOFunction = "";
function getHTMLControl(strFieldName,strFieldId,strRequestVars)
{	
	
	// Created an Instance of the XMLHttpRequest object
	var objHttp = createRequestObject();

	//Set the url of the file which will return the HTML control
	strUrl="index.php?file=./../../base/meditab/med_getHTMLControl";
	
	//Add the selected values in the query string
	strUrl+="&"+strFieldName+"="+document.getElementById("hid_sel"+strFieldName).value;
	
	//Add the field id in the query string
	strUrl+="&strFieldId="+strFieldId;

	//Add the request variables to the query string if required
	if(strRequestVars!='')
	{
		arrRequestVars	=	strRequestVars.split(",");
		for(intRequestVars=0; intRequestVars<arrRequestVars.length; intRequestVars++)
		{
			arrVars	=	arrRequestVars[intRequestVars].split("=");		
			strVarName	=	arrVars[0];
			strVarValue	=	document.getElementById(arrVars[1]).value;
			
			strUrl+="&"+strVarName+"="+strVarValue;

		}
	}

	try{
		if(strUrl!=undefined && (objHttp.readyState==0 || objHttp.readyState==4))
		{
			objHttp.open('get', strUrl);
			objHttp.onreadystatechange =	function() { handleHttpResponse(objHttp); };
			objHttp.send(null);				
		}
	}
	catch(e)
	{
		return false;
	}
}

/**
	* This is a handle to the http request which will get the request from the http object and replace the contents of the corresponding division.
	* @param	object	objHttp is the http object for this particular response.
*/
function handleHttpResponse(objHttp)
{
	//The last time the function is called when the response is ready.		
	if(objHttp.readyState == 4 && objHttp.status == 200)
	{	
		strHtmlControl	=	objHttp.responseText;		
		if(strHtmlControl.search(/<input type="hidden" name="hid_bln_session_expire" id="hid_bln_session_expire" value="true" >/)!=-1)
		{
			alert(strSesstionExpireMsg);
			window.location = "index.php";
		}
		else
		{
			arrHtmlControl	=	strHtmlControl.split("!@#***#@!");
			strDivName		=	arrHtmlControl[1];
			document.getElementById(strDivName).innerHTML = arrHtmlControl[2];
			eval(strCallHttpResponseOFunction);
			strCallHttpResponseOFunction="";		
		}
	}
}

function getHTMLControlMulti(strFieldName,strFieldId,strRequestVars,strCombo)
{	
	var arrControl;
	arrControl = strCombo.split("&&");
	var objHttp = createRequestObject();
	// Created an Instance of the XMLHttpRequest object
	//Set the url of the file which will return the HTML control
	strUrl="index.php?file=./../../base/meditab/med_getHTMLControl";
	
	//Add the selected values in the query string
	strUrl+="&"+strFieldName+"="+document.getElementById("hid_sel"+strFieldName).value;
	
	//Add the field id in the query string
	strUrl+="&strFieldId="+strFieldId;

	//Add the request variables to the query string if required
	if(strRequestVars!='')
	{
		arrRequestVars	=	strRequestVars.split(",");
		for(intRequestVars=0; intRequestVars<arrRequestVars.length; intRequestVars++)
		{
			arrVars	=	arrRequestVars[intRequestVars].split("=");		
			strVarName	=	arrVars[0];
			strVarValue	=	document.getElementById(arrVars[1]).value;
			
			strUrl+="&"+strVarName+"="+strVarValue;
		}
	}
	try{
		if(strUrl!=undefined && (objHttp.readyState==0 || objHttp.readyState==4))
		{
			objHttp.open('get', strUrl);
			objHttp.onreadystatechange =	function() { handleMultiHttpResponse(objHttp,arrControl); };
			objHttp.send(null);
						
		}
	}
	catch(e)
	{
		return false;
	}
}

/**
 * This is a handle to the http request which will get the request from the http object and replace the contents of the corresponding division.
 * @param	object	objHttp is the http object for this particular response.
*/
function handleMultiHttpResponse(objHttp,arrControl)
{
	//The last time the function is called when the response is ready.		
	if(objHttp.readyState == 4 && objHttp.status == 200)
	{	
		strHtmlControl	=	objHttp.responseText;
		if(strHtmlControl.search(/<input type="hidden" name="hid_bln_session_expire" id="hid_bln_session_expire" value="true" >/)!=-1)
		{
			alert(strSesstionExpireMsg);
			window.location = "index.php";
		}
		else
		{
			arrHtmlControl	=	strHtmlControl.split("!@#***#@!");
			strDivName		=	arrHtmlControl[1];
			document.getElementById(strDivName).innerHTML = arrHtmlControl[2];
			var arrNewControl = Array();
			arrNewControl = arrControl[0].split("','");
			var strCombo = "";
			for(var intControl = 1; intControl < arrControl.length; intControl++)
			{
				strCombo += arrControl[intControl] +"&&";
			}
			strCombo = strCombo.substr(0,strCombo.length-2);
			arrNewControl[0] = arrNewControl[0].substr(1,arrNewControl[0].length);
				arrNewControl[2] = arrNewControl[2].substr(0,arrNewControl[2].length-1);
			if(strCombo != "")
			{
				getHTMLControlMulti(arrNewControl[0],arrNewControl[1],arrNewControl[2],strCombo);
			}
			else
			{
				getHTMLControl(arrNewControl[0],arrNewControl[1],arrNewControl[2]);
			}
		}
	}
}

function zoomTxtArea(strTxtAreaName,rowsno,intDefaultRows)
{
	strTxtArea	=	document.getElementById(strTxtAreaName);
	if(strTxtArea.rows!=rowsno)
	{
		strTxtArea.rows=rowsno;
		document.getElementById('zoom_'+strTxtAreaName).src='images/zoom_up.gif';
	}
	else
	{
		strTxtArea.rows=intDefaultRows;
		document.getElementById('zoom_'+strTxtAreaName).src='images/zoom_down.gif';
		
	}
}
	
/**
  * This function will submit the search form
 **/
function submitSearchForm(objFrm)
{
	if(checkValidSearch(objFrm) == false)
	{
		return false; 
	}
	//if some extra validation required
	if(window.extraSearchValid) //check whether the function for extra validation exist or not
	{
		if(extraSearchValid()==false) //if the function returns false then return false
		{
			return false;	
		}
	}
	//if function doesnot exist or return true return true
	objFrm.submit();
	return false;
	
}
/**
  * This function will check date validation for search form
 **/
function checkValidSearch(objFrm)
{
	var FORMID = objFrm.name;
	var frmname=document.getElementById(FORMID);
	var blnflag=true;
	var strDtTo = "";
	var strDtToMsg = "";
	var strDtFrom = "";
	var strDtFromMsg = "";
	alertMessage = "";	// reset the alert nessage
	alertfield = "";
	strStyle = "";
	strFocus = "";
	selcount = 0;
	itercount = 0;
	var frmcnt=document.getElementById(FORMID).elements.length;
	for(var i=0;i<frmcnt;i++)
	{
		try
		{
			MESSAGE=eval("LBL_"+frmname.elements[i].name);
		}
		catch(e)
		{
			MESSAGE="Field";
		}
		// handling of various textbox validations for search
		if(frmname.elements[i].type=='text' && !frmname.elements[i].name.match(/\[]/))		
		{	
			// first 3 characters of the textbox name are to identify the search variables
			var chkSerch = frmname.elements[i].name.substr(0,3);
			if(chkSerch == "Sr_")
			{
				// forth and fifth carector to check type of validation
				var ChkType=frmname.elements[i].name.substr(3,2);
				
				//sixth character is to identify required field...
				var RChr=frmname.elements[i].name.substr(5,1);
				
				var REQUIRED="";
				if(RChr=="R")
				{
					REQUIRED = true;
				}					
				else
				{
					REQUIRED = false;	
				}
				//Generate name of the field...
				FIELD_NAME=eval("document."+FORMID+"."+frmname.elements[i].name);			
				switch(ChkType)
				{
					case "Dt" :
						if(frmname.elements[i].name.substr(frmname.elements[i].name.length-4,4) == 'from' && strDtFrom == "")
						{
							strDtFrom 		= 	FIELD_NAME;
							strDtFromMsg 	= 	MESSAGE;
						}
						else if(frmname.elements[i].name.substr(frmname.elements[i].name.length-2,2) == 'to')
						{
							strDtTo 		= 	FIELD_NAME;
							strDtToMsg 		= 	MESSAGE;
						}
						if(!checkDate(FIELD_NAME,MESSAGE,REQUIRED))
						{
							blnflag = false;
						}
						break;
				}
				if(strDtTo != "" && strDtFrom != "")
				{
					if(!dateCompare(strDtFrom,strDtTo,strDtFromMsg,strDtToMsg))
					{	
						return false;
					}
				}
			}
		}
	}
	if(blnflag == false && alertMessage!="")
	{
		alertMessage="Following errors:\n\n" + alertMessage;	
		alert(alertMessage);	
		eval(strStyle);
		eval(strFocus);
		return false;
	}
	else
	{
		eval(strStyle);
		return true;
	}
}

//function to assign the value of this textarea to the resp. textarea of the opener
function getTxtAreaValue(strControl)
{
	var oEditorarent	=	document.getElementById(strControl+'___Frame').contentWindow.window;
	var FCK				=	oEditorarent.FCK ;
	var FCKConfig		=	oEditorarent.FCKConfig ;
	strTxtAreaName		=	document.getElementById(strControl);
	strTxtAreaVal		=	FCK.GetXHTML( FCKConfig.FormatSource);
	strTxtAreaName.value=	strTxtAreaVal;
	return strTxtAreaName.value;
}		

//Function to canvert time to seconds
function compareTime(strFirstTime,strSecondTime,MESSAGE1,MESSAGE2)
{

	//Convert first time into seconds
	strFirstTimeSec		=	(strFirstTime.substring(0,strFirstTime.indexOf(':'))-0) * 60 + (strFirstTime.substring(strFirstTime.indexOf(':')+1,strFirstTime.length)-0);

	//Convert second time into seconds
	strSecondTimeSec	=	(strSecondTime.substring(0,strSecondTime.indexOf(':'))-0) * 60 + (strSecondTime.substring(strSecondTime.indexOf(':')+1,strSecondTime.length)-0);

	//Compare both the times
	if(strFirstTimeSec > strSecondTimeSec)
	{	
		alert(MESSAGE2+" cannot be less than "+MESSAGE1+".");	
		return false;
	}
	return true;
}


// Created an Instance of the XMLHttpRequest object
var http = createRequestObject(); 
var strDivName="";
var strCallHttpResponseOFunction = "";
function getPageData(strUrl,strDiv)
{	
	try
	{
		if(strUrl!=undefined && (http.readyState==0 || http.readyState==4))
		{
			strDivisionName=strDiv;
			http.open('get', strUrl);
			http.onreadystatechange = handleHttpResponseObject;
			http.send(null);				
		}
	}
	catch(e)
	{
		return false;
	}
}

function getPageDataEFun(strUrl,strDiv,strEfunction)
{	
	// Created an Instance of the XMLHttpRequest object
	var http = createRequestObject(); 
	try
	{
		if(strUrl!=undefined && (http.readyState==0 || http.readyState==4))
		{
			http.open('get', strUrl);
			http.onreadystatechange = function() { handleHttpResponseObj(http,strDiv,strEfunction); };
			http.send(null);				
		}
	}
	catch(e)
	{
		return false;
	}
}

// Common function to get Element
// Generalized for all browser
function getElement(element)
{
	var elm;
	if (document.getElementById) 
	{
		elm = document.getElementById(element);
	}
	else if (document.all) 
	{
		elm = document.all[element];
	}
	else if (document.layers) 
	{
		elm = document.layers[element];
	}
	return elm;
}

function handleHttpResponseObject()
{
	// the last time the function is called when the response is ready.		
	if(http.readyState == 4 && http.status == 200)
	{	
		$strHtml=http.responseText;			
		if($strHtml.search(/<input type="hidden" name="hid_bln_session_expire" id="hid_bln_session_expire" value="true" >/)!=-1)
		{
			alert(strSesstionExpireMsg);
			window.location = "index.php";
		}
		else
		{		
			document.getElementById(strDivisionName).innerHTML = $strHtml;
			addJavaScript(document.getElementById(strDivisionName));
			eval(strCallHttpResponseOFunction);
			strCallHttpResponseOFunction="";
		}
	}
}

function handleHttpResponseObj(http,strDiv,strEfunction)
{
	// the last time the function is called when the response is ready.		
	if(http.readyState == 4 && http.status == 200)
	{	
		$strHtml=http.responseText;	
		document.getElementById(strDiv).innerHTML = $strHtml;
		
		addJavaScript(document.getElementById(strDiv));
		if(document.getElementById('expire')!=undefined)
		{
			if(document.getElementById('expire').value=='true')
			{
				window.location="index.php";	
			}
		}	
		eval(strEfunction);
		eval(strCallHttpResponseOFunction);
		strCallHttpResponseOFunction="";
	}
}


function getHPageData(strUrl,strDiv,strHandle)
{	
	try
	{
		if(strUrl!=undefined && (http.readyState==0 || http.readyState==4))
		{
			if(strDiv!='')	
			{
				strDivisionName=strDiv;
			}
			http.open('get', strUrl);
			http.onreadystatechange = eval(strHandle);
			http.send(null);				
		}
	}
	catch(e)
	{
		return false;
	}
}


function addJavaScript(strHtmlPage)
{
	var strScript=strHtmlPage.getElementsByTagName('script');
	var strHead = document.getElementsByTagName("head")[0];
	if(document.getElementById("addScript"))
	{
		var node = document.getElementById('addScript');
		strHead.removeChild(node);
	}
	var scriptNode = document.createElement('script');
	scriptNode.id = 'addScript';
	scriptNode.type = 'text/javascript';
	scriptNode.language = 'javascript';
	var strText;
	strText = "";
	for(var x=0;x<strScript.length;x++)
	{
		with(window) 
		{ 
			strText = strText + " " + (strScript[x].text).replace('<!--','').replace('-->','');
		} 
	}
	if (window.navigator.appName.toLowerCase().indexOf("microsoft") > -1) 
	{   /// if ie
		scriptNode.text = strText;
	}
	else 
	{   // if mozzila
		scriptNode.innerHTML = strText;
	}
//	if(strText.match(/function (\s*)extraValid/i))
//	{
//		blnExtraValid=true
//	}
//	else
//	{
//		blnExtraValid=false
//	}
	
	document.getElementsByTagName("head")[0].appendChild(scriptNode);
}

/**
Function to show/hide an div based on condition
* @param strControlName	string name of the control on which condition is there
* @param strConditionVal  string value of the condition
* @param strDivName		string name of the div
*/
function showHideDiv(strControlName,strConditionVal,strDivName)
{

	CONTROL_NAME	=	eval(document.getElementById(strControlName));
	blnShowDiv		=	false;
	arrConditionVal	=	new Array();
	arrConditionVal	=	strConditionVal.split(",");
	
	if(CONTROL_NAME!=null)
	{		
		for(intConditionVal=0; intConditionVal<arrConditionVal.length; intConditionVal++)
		{
			if(CONTROL_NAME.value == arrConditionVal[intConditionVal])
			{
				blnShowDiv	=	true;
				break;
			}
		}
	
		if(blnShowDiv==true)
			strDivStyle	=	"inline";
		else
			strDivStyle	=	"none";
			
		//Get the names of div's
		arrDivName	=	strDivName.split(",");
		for(intDivName=0;	intDivName<arrDivName.length; intDivName++)
		{
			DIV_NAME		=	eval(document.getElementById(arrDivName[intDivName]));
			if(DIV_NAME!=null)
				DIV_NAME.style.display=strDivStyle;
		}
	}
}

/**
Function to check whether the duration is valid or not
*/
function checkValidDuration(HOUR_FIELD,MIN_FIELD,strHourMsg,strMinMsg,intHourLimit,intMinLimit)
{
	if(HOUR_FIELD.value.search(/^[0-9]*$/))
	{
		alertMessage += "\nPlease enter valid "+strHourMsg+".";
		setStyle(HOUR_FIELD);
	}
	else
		resetStyle(HOUR_FIELD);
		
	if(MIN_FIELD.value.search(/^[0-9]*$/))
	{
		alertMessage += "\nPlease enter valid "+strMinMsg+".";
		setStyle(MIN_FIELD);
	}
	else
		resetStyle(MIN_FIELD);
		
	if( HOUR_FIELD.value == "" && MIN_FIELD.value == "")
	{
		alertMessage += "\nPlease enter the value for Duration.";
		setStyle(MIN_FIELD);
	}
		
	if((HOUR_FIELD.value == "00" && MIN_FIELD.value == "00") || (HOUR_FIELD.value == "0" && MIN_FIELD.value == "0") ||(HOUR_FIELD.value == "00" && MIN_FIELD.value == "0") || (HOUR_FIELD.value == "0" && MIN_FIELD.value == "00") || (HOUR_FIELD.value == "" && (MIN_FIELD.value == "00" || MIN_FIELD.value == "0")) || ((HOUR_FIELD.value == "0" || HOUR_FIELD.value == "00") && MIN_FIELD.value == "" ))
	{
		alertMessage += "\nDuration should not be zero.";
		setStyle(HOUR_FIELD);
	}

	if(intHourLimit != '' && intHourLimit != 0 && HOUR_FIELD.value > intHourLimit)
	{
		alertMessage += "\n"+strHourMsg+" between [00-"+intHourLimit+"] hours.";
		setStyle(HOUR_FIELD);
	}
	
	if(intMinLimit == '' || intMinLimit == 0)
		intMinLimit=59;
	
	if(MIN_FIELD.value > intMinLimit)
	{
		alertMessage += "\n"+strMinMsg+" between [00-"+intMinLimit+"] minutes.";
		setStyle(MIN_FIELD);
	}

	eval(strStyle);
	
	if(alertMessage != "")
	{
		alertMsg = "Following errors:\n" + alertMessage;	
		alert(alertMsg);	
		return false;
	}
	else
		return true;
}

/**
Function will return the absolute top position of div id or td id........better use td id
*/
function getAbsoluteTop(strObjectId) 
{
	// Get an object top position from the upper left viewport corner
	// Tested with relative and nested objects
	objDiv	=	document.getElementById(strObjectId)
	
	// Get top position from the parent object
	intTop	=	objDiv.offsetTop           
	
	while(objDiv.offsetParent!=null) 
	{ 
		// Parse the parent hierarchy up to the document element
		oParent = objDiv.offsetParent  // Get parent object reference
		intTop += oParent.offsetTop // Add parent top position
		objDiv = oParent
	}
	// Return top position
	return intTop
}

//---------------------------------------------------------------------------------\\
// Customized checking of blank field with custom message used in extraValid function
function checkCBlank(FIELD_NAME,MESSAGE,REQUIRED)
{
	//if value is null then alert message...
	if(Trim(FIELD_NAME.value) == '')
	{
		if(REQUIRED)
		{
			alert("Please enter value for "+MESSAGE+".");
			if(FIELD_NAME.type!='textarea')
			{
				setStyle(FIELD_NAME);
				FIELD_NAME.focus();
				eval(strStyle);
			}
		}
		return true;
	}
	else
	{	
		if(FIELD_NAME.type!='textarea')
		{
			resetStyle(FIELD_NAME);
			eval(strStyle);
		}
		return false;	
	}
}

// Checks time format in customized validations
function checkTime(FIELD_TIME,MESSAGE,REQUIRED)
{
	arrTime	=	FIELD_TIME.value.split(":");
	if(checkCBlank(FIELD_TIME,MESSAGE,REQUIRED) && REQUIRED)
	{
		setStyle(FIELD_TIME);
		eval(strStyle);
		FIELD_TIME.focus();
		return false;
	}
	else if(!checkCBlank(FIELD_TIME,MESSAGE,REQUIRED))
	{
		if(arrTime[0]>12 || arrTime[1]>59)
		{
			alert("Please enter valid " + MESSAGE + ".");
			setStyle(FIELD_TIME);
			eval(strStyle);
			FIELD_TIME.focus();
			return false;
		}
	}
	resetStyle(FIELD_TIME);
	eval(strStyle);
	return true;
}

// Function which will return no of selected options in any Multiple Select control 
function getComboCount(FIELD_NAME)
{
	var intCount = 0;
	for (i=FIELD_NAME.options.length-1;i>=0;i--)
	{
		if(FIELD_NAME.options[i].selected)
			intCount++;
	}
	return intCount;
}

// This function will validate combo for minimum selection of options and will alert used for customized validation
function validCombo(FIELD_NAME,MESSAGE,MIN_COUNT)
{
	intCount = getComboCount(FIELD_NAME);
	if(intCount < MIN_COUNT)
	{
		alert("Please select atleast " + MIN_COUNT + " " + MESSAGE);
		setStyle(FIELD_NAME);
		FIELD_NAME.focus();
		eval(strStyle);
		return false;
	}
	else
		return true;
}

// This function will submit the form after assigning all values to hidden variables in the form passed as argument
// example of usage: submitCForm("frm_meeting","wm_id=intWmId&hid_week_id=intWeekId&file=med_weekly_report");
function submitCForm(strFormName,strArgs)
{
	arrArgs	= strArgs.split('&');
	for(intElement=0;intElement<arrArgs.length;intElement++)
	{
		arrElement = arrArgs[intElement].split('=');
		document.getElementById(arrElement[0]).value = arrElement[1];
	}
	document.getElementById(strFormName).submit();
}

// Custom ajax function which will directly returns data from the url and stops execution till that time...
function getURLData(strUrl)
{		
	
	try
	{		
		if(strUrl!=undefined && (http.readyState==0 || http.readyState==4))
		{
			http.open('get', strUrl,false);
			http.send(null);
			
			return http.responseText;
		}
	}
	catch(e)
	{
		return false;
	}
}

// This function will set selected elements of the given combo...
function setMultipleSelected(objTargetElement,arrSelectValues)
{
	for(var i = 0; i < objTargetElement.length; i++) 
	{
		for(var j = 0; j < arrSelectValues.length; j++)
			if(objTargetElement.options[i].value == arrSelectValues[j])
				objTargetElement.options[i].selected = true;
	}
}