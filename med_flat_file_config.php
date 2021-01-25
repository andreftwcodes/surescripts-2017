<?php
	
	
	
	
	
	$cfgPrescriber[0] =	array('FIELD'=>'spi','LOC'=>13);
	$cfgPrescriber[1] =	array('FIELD'=>'npi','LOC'=>10);
	$cfgPrescriber[2] =	array('FIELD'=>'dea','LOC'=>25);
	$cfgPrescriber[3] =	array('FIELD'=>'state_license_number','LOC'=>25);
	
	$cfgPrescriber[4] =	array('FIELD'=>'specialty_code_primary','LOC'=>3);
	
	$cfgPrescriber[5] =	array('FIELD'=>'prefix_name','LOC'=>10);
	$cfgPrescriber[6] =	array('FIELD'=>'last_name','LOC'=>35);
	$cfgPrescriber[7] =	array('FIELD'=>'first_name','LOC'=>35);
	$cfgPrescriber[8] =	array('FIELD'=>'middle_name','LOC'=>35);
	$cfgPrescriber[9] =	array('FIELD'=>'suffix_name','LOC'=>10);
	
	$cfgPrescriber[10] =	array('FIELD'=>'clinic_name','LOC'=>35);
	$cfgPrescriber[11] =	array('FIELD'=>'address_line1','LOC'=>35);
	$cfgPrescriber[12] =	array('FIELD'=>'address_line2','LOC'=>35);
	$cfgPrescriber[13] =	array('FIELD'=>'city','LOC'=>35);
	$cfgPrescriber[14] =	array('FIELD'=>'state','LOC'=>2);
	$cfgPrescriber[15] =	array('FIELD'=>'zip','LOC'=>11);
	// $cfgPrescriber[16] =	array('FIELD'=>'country','LOC'=>2); // new field
	// $cfgPrescriber[17] =	array('FIELD'=>'standard_address_line1','LOC'=>100); // new field
	// $cfgPrescriber[18] =	array('FIELD'=>'standard_address_line2','LOC'=>100); // new field
	// $cfgPrescriber[19] =	array('FIELD'=>'standard_city','LOC'=>35); // new field
	// $cfgPrescriber[20] =	array('FIELD'=>'standard_state','LOC'=>2); // new field
	// $cfgPrescriber[21] =	array('FIELD'=>'standard_zip','LOC'=>9); // new field
	
	$cfgPrescriber[22] =	array('FIELD'=>'phone_primary','LOC'=>25);
	$cfgPrescriber[23] =	array('FIELD'=>'fax','LOC'=>25);
	$cfgPrescriber[24] =	array('FIELD'=>'email','LOC'=>80);
	
	
	$cfgPrescriber[25] =	array('FIELD'=>'phone_alt_numbers','LOC'=>25);
	// $cfgPrescriber[] =	array('FIELD'=>'phone_alt1','LOC'=>25);
	// $cfgPrescriber[] =	array('FIELD'=>'phone_alt1_qualifier','LOC'=>3);
	// $cfgPrescriber[] =	array('FIELD'=>'phone_alt2','LOC'=>25);
	// $cfgPrescriber[] =	array('FIELD'=>'phone_alt2_qualifier','LOC'=>3);
	// $cfgPrescriber[] =	array('FIELD'=>'phone_alt3','LOC'=>25);
	// $cfgPrescriber[] =	array('FIELD'=>'phone_alt3_qualifier','LOC'=>3);
	// $cfgPrescriber[] =	array('FIELD'=>'phone_alt4','LOC'=>25);
	// $cfgPrescriber[] =	array('FIELD'=>'phone_alt4_qualifier','LOC'=>3);
	// $cfgPrescriber[] =	array('FIELD'=>'phone_alt5','LOC'=>25);
	// $cfgPrescriber[] =	array('FIELD'=>'phone_alt5_qualifier','LOC'=>3);
	
	$cfgPrescriber[26] =	array('FIELD'=>'active_start_time','LOC'=>22,'CAST'=>'DATETIME');
	$cfgPrescriber[27] =	array('FIELD'=>'active_end_time','LOC'=>22,'CAST'=>'DATETIME');
	$cfgPrescriber[28] =	array('FIELD'=>'service_level','LOC'=>5);
	$cfgPrescriber[29] =	array('FIELD'=>'partner_account','LOC'=>35);
	$cfgPrescriber[30] =	array('FIELD'=>'last_modified_date','LOC'=>22,'CAST'=>'DATETIME');
	$cfgPrescriber[31] =	array('FIELD'=>'record_change','LOC'=>1);
	$cfgPrescriber[32] =	array('FIELD'=>'old_service_level','LOC'=>5);
	// $cfgPrescriber[] =	array('FIELD'=>'text_service_level','LOC'=>100);
	// $cfgPrescriber[] =	array('FIELD'=>'text_service_level_change','LOC'=>100);
	$cfgPrescriber[33] =	array('FIELD'=>'version','LOC'=>5);
	// $cfgPrescriber[] =	array('FIELD'=>'npi_location','LOC'=>13);
	
	
	$cfgPrescriber[34]   =	array('FIELD'=>'specialty_type1','LOC'=>35);
	// $cfgPrescriber[]   =	array('FIELD'=>'specialty_type2','LOC'=>35);
	// $cfgPrescriber[]   =	array('FIELD'=>'specialty_type3','LOC'=>35);
	// $cfgPrescriber[]   =	array('FIELD'=>'specialty_type4','LOC'=>35);
	// $cfgPrescriber[]   =	array('FIELD'=>'file_id','LOC'=>35);
	$cfgPrescriber[35]   =	array('FIELD'=>'medicare_number','LOC'=>35);
	$cfgPrescriber[36]   =	array('FIELD'=>'medicaid_number','LOC'=>35);
	// $cfgPrescriber[]   =	array('FIELD'=>'dentist_license_number','LOC'=>35);
	$cfgPrescriber[37]   =	array('FIELD'=>'upin','LOC'=>35);
	// $cfgPrescriber[38]   =	array('FIELD'=>'certificate_to_prescribe','LOC'=>35); // new field
	// $cfgPrescriber[39]   =	array('FIELD'=>'Waiver_ID','LOC'=>35); // new field
	// $cfgPrescriber[40]   =	array('FIELD'=>'REMSHealthCareProviderEntrollmentID','LOC'=>35); // new field
	// $cfgPrescriber[41]   =	array('FIELD'=>'StateControlSubstanceNumber','LOC'=>35); // new field
	$cfgPrescriber[42]   =	array('FIELD'=>'mutually_defined','LOC'=>35);
	// $cfgPrescriber[43]   =	array('FIELD'=>'DirectAddress','LOC'=>35); // new field
	// $cfgPrescriber[44]   =	array('FIELD'=>'UseCases','LOC'=>35); // new field
	// $cfgPrescriber[45]   =	array('FIELD'=>'AvailableRoutes','LOC'=>35); // new field
	// $cfgPrescriber[46]   =	array('FIELD'=>'OrganizationID','LOC'=>35); // new field
	// $cfgPrescriber[47]   =	array('FIELD'=>'Latitude','LOC'=>35); // new field
	// $cfgPrescriber[48]   =	array('FIELD'=>'Longitude','LOC'=>35); // new field
	// $cfgPrescriber[49]   =	array('FIELD'=>'Precise','LOC'=>35); // new field

	// $cfgPrescriber[]   =	array('FIELD'=>'ppo_number','LOC'=>35);
	// $cfgPrescriber[]   =	array('FIELD'=>'social_security','LOC'=>35);
	// $cfgPrescriber[]   =	array('FIELD'=>'prior_authorization','LOC'=>35);
	// $cfgPrescriber[]   =	array('FIELD'=>'instore_ncpdpid','LOC'=>7);
	
	
	
	
	
	
	$cfgPharmacy[0]   =	array('FIELD'=>'ncpdpid','LOC'=>7);
	$cfgPharmacy[1]   =	array('FIELD'=>'store_number','LOC'=>35);
	
	
	$cfgPharmacy[2]   =	array('FIELD'=>'store_name','LOC'=>35); // OrganizationName
	$cfgPharmacy[3]   =	array('FIELD'=>'address_line1','LOC'=>35);
	$cfgPharmacy[4]   =	array('FIELD'=>'address_line2','LOC'=>35);
	$cfgPharmacy[5]   =	array('FIELD'=>'city','LOC'=>35);
	$cfgPharmacy[6]   =	array('FIELD'=>'state','LOC'=>2);
	$cfgPharmacy[7]   =	array('FIELD'=>'zip','LOC'=>11);
	// $cfgPharmacy[8] =	array('FIELD'=>'country','LOC'=>2); // new field
	// $cfgPharmacy[9] =	array('FIELD'=>'standard_address_line1','LOC'=>100); // new field
	// $cfgPharmacy[10] =	array('FIELD'=>'standard_address_line2','LOC'=>100); // new field
	// $cfgPharmacy[11] =	array('FIELD'=>'standard_city','LOC'=>35); // new field
	// $cfgPharmacy[12] =	array('FIELD'=>'standard_state','LOC'=>2); // new field
	// $cfgPharmacy[13] =	array('FIELD'=>'standard_zip','LOC'=>9); // new field
	
	$cfgPharmacy[14]   =	array('FIELD'=>'phone_primary','LOC'=>25);
	$cfgPharmacy[15]   =	array('FIELD'=>'fax','LOC'=>25);
	$cfgPharmacy[16]   =	array('FIELD'=>'email','LOC'=>80);
	
	
	$cfgPharmacy[17] =	array('FIELD'=>'phone_alt_numbers','LOC'=>25);
	// $cfgPharmacy[]   =	array('FIELD'=>'phone_alt1','LOC'=>25);
	// $cfgPharmacy[]   =	array('FIELD'=>'phone_alt1_qualifier','LOC'=>3);
	// $cfgPharmacy[]   =	array('FIELD'=>'phone_alt2','LOC'=>25);
	// $cfgPharmacy[]   =	array('FIELD'=>'phone_alt2_qualifier','LOC'=>3);
	// $cfgPharmacy[]   =	array('FIELD'=>'phone_alt3','LOC'=>25);
	// $cfgPharmacy[]   =	array('FIELD'=>'phone_alt3_qualifier','LOC'=>3);
	// $cfgPharmacy[]   =	array('FIELD'=>'phone_alt4','LOC'=>25);
	// $cfgPharmacy[]   =	array('FIELD'=>'phone_alt4_qualifier','LOC'=>3);
	// $cfgPharmacy[]   =	array('FIELD'=>'phone_alt5','LOC'=>25);
	// $cfgPharmacy[]   =	array('FIELD'=>'phone_alt5_qualifier','LOC'=>3);
	
	$cfgPharmacy[18]   =	array('FIELD'=>'active_start_time','LOC'=>22,'CAST'=>'DATETIME');
	$cfgPharmacy[19]   =	array('FIELD'=>'active_end_time','LOC'=>22,'CAST'=>'DATETIME');
	$cfgPharmacy[20]   =	array('FIELD'=>'service_level','LOC'=>5);
	$cfgPharmacy[21]   =	array('FIELD'=>'partner_account','LOC'=>35);
	$cfgPharmacy[22]   =	array('FIELD'=>'last_modified_date','LOC'=>22,'CAST'=>'DATETIME');
	
	
	$cfgPharmacy[23]   =	array('FIELD'=>'cross_street','LOC'=>35);
	$cfgPharmacy[24]   =	array('FIELD'=>'record_change','LOC'=>1);
	$cfgPharmacy[25]   =	array('FIELD'=>'old_service_level','LOC'=>5);
	// $cfgPharmacy[]   =	array('FIELD'=>'text_service_level','LOC'=>100);
	// $cfgPharmacy[]   =	array('FIELD'=>'text_service_level_change','LOC'=>100);
	$cfgPharmacy[26]   =	array('FIELD'=>'version','LOC'=>5);
	$cfgPharmacy[27]   =	array('FIELD'=>'npi','LOC'=>10);
	
	
	$cfgPharmacy[28]   =	array('FIELD'=>'specialty_type1','LOC'=>35);
	// $cfgPharmacy[]   =	array('FIELD'=>'specialty_type2','LOC'=>35);
	// $cfgPharmacy[]   =	array('FIELD'=>'specialty_type3','LOC'=>35);
	// $cfgPharmacy[]   =	array('FIELD'=>'specialty_type4','LOC'=>35);
	// $cfgPharmacy[]   =	array('FIELD'=>'file_id','LOC'=>35);
	// $cfgPharmacy[29] =	array('FIELD'=>'ReplaceNCPDPID','LOC'=>9); // new field

	$cfgPharmacy[30]   =	array('FIELD'=>'state_license','LOC'=>35);
	// $cfgPharmacy[31] =	array('FIELD'=>'UPIN','LOC'=>9); // new field
	// $cfgPharmacy[32] =	array('FIELD'=>'FacilityID','LOC'=>9); // new field
	$cfgPharmacy[33]   =	array('FIELD'=>'medicare_number','LOC'=>35);
	$cfgPharmacy[34]   =	array('FIELD'=>'medicaid_number','LOC'=>35);
	// $cfgPharmacy[]   =	array('FIELD'=>'ppo_number','LOC'=>35);
	$cfgPharmacy[35]   =	array('FIELD'=>'payer_id','LOC'=>35);
	// $cfgPharmacy[]   =	array('FIELD'=>'bin','LOC'=>35);
	$cfgPharmacy[36]   =	array('FIELD'=>'dea','LOC'=>35);
	$cfgPharmacy[37]   =	array('FIELD'=>'hin','LOC'=>35);
	// $cfgPharmacy[]   =	array('FIELD'=>'secondary_coverage','LOC'=>35);
	// $cfgPharmacy[]   =	array('FIELD'=>'naic_code','LOC'=>35);
	// $cfgPharmacy[]   =	array('FIELD'=>'promotion_number','LOC'=>35);
	// $cfgPharmacy[]   =	array('FIELD'=>'social_security','LOC'=>35);
	// $cfgPharmacy[]   =	array('FIELD'=>'prior_authorization','LOC'=>35);
	$cfgPharmacy[38]   =	array('FIELD'=>'mutually_defined','LOC'=>35);
	// $cfgPharmacy[39] =	array('FIELD'=>'DirectAddress','LOC'=>9); // new field
	// $cfgPharmacy[40] =	array('FIELD'=>'OrganizationType','LOC'=>9); // new field
	// $cfgPharmacy[41] =	array('FIELD'=>'OrganizationID','LOC'=>9); // new field
	// $cfgPharmacy[42] =	array('FIELD'=>'ParentOrganizationID','LOC'=>9); // new field
	// $cfgPharmacy[43] =	array('FIELD'=>'Latitude','LOC'=>9); // new field
	// $cfgPharmacy[44] =	array('FIELD'=>'Longitude','LOC'=>9); // new field
	// $cfgPharmacy[45] =	array('FIELD'=>'Precise','LOC'=>9); // new field
	// $cfgPharmacy[46] =	array('FIELD'=>'UseCase','LOC'=>9); // new field
?>