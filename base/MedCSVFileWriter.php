<?php


set_time_limit(0);
include_once 'MedFileWriter.php';
include_once '../med_config.php';
include_once 'DB.php';
class MedCSVFileWriter extends MedFileWriter
{
	public function writeFile($ColumnSep, $LineSep, $ColumnValueEnclosedBy, array $EscapeCharset)
	{
		$TotalRecords = $this->getCount();
		$Pages = $TotalRecords / $this->Limit;
		
		for($Counter = 0; $Counter < $Pages; $Counter++)
		{
			$Buffer = '';
			$Records = $this->getRecords($Counter * $this->Limit);
			foreach($Records as $Record)
			{
				$Line = '';
				foreach($Record as $ColumnVal)
				{
					$Line[] = $ColumnValueEnclosedBy . str_replace($EscapeCharset['FIND'],$EscapeCharset['REPLACE'],$ColumnVal). $ColumnValueEnclosedBy;
				}
				$Buffer .= implode($ColumnSep,$Line) . $LineSep;
			}
			file_put_contents('c:/x.txt',$Buffer,FILE_APPEND);
		}
	}
}

$sql['SELECT'] = 'mt_tran_id,spi,dea, state_license_number,reference_number_alt1,reference_number_alt1_qualifier,specialty_code_primary,
prefix_name,last_name,first_name,middle_name,suffix_name,clinic_name,address_line1,address_line2,
city,state, zip,phone_primary,fax,email,phone_alt1,phone_alt1_qualifier,
phone_alt2,phone_alt2_qualifier,phone_alt3,phone_alt3_qualifier,phone_alt4,phone_alt4_qualifier,
phone_alt5,phone_alt5_qualifier,phone_alt6,phone_alt6_qualifier,
active_start_time,active_end_time,service_level,partner_account,last_modified_date,
record_change,old_service_level,text_service_level,text_service_level_change,
version,npi,npi_location,mt_log_id,service_level_bits';
$sql['TABLE'] = 'prescriber_master';
$sql['WHERE'] = '';

$x = new MedCSVFileWriter($sql, 10000, 'CSV');
$Find = array("'");
$Replace = array("''");

$x->writeFile(",", "\n", "'", array('FIND'=>$Find, 'REPLACE'=>$Replace));
