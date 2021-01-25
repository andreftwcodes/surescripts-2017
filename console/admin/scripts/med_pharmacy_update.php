<?PHP

	
	
	include_once("./base/med_module.php"); 
		
	
	$objModule 		= 	new MedModule();
	
	
	$intPharmacyRequestId		=	$objPage->getRequest('pharmacy_request_id');
	$intPharmacyMasterId		=	$objPage->getRequest('pharmacy_master_id');
	
	
	if($intPharmacyRequestId != '')
		$rsPharmacy		=	$objModule->getDetailsFromPharmacyRequests($intPharmacyRequestId,"*");
	else
		$rsPharmacy		=	$objModule->getDetailsFromPharmacyMaster($intPharmacyMasterId,"*");
		
	
	$arrHiddens			=	array(
									"file" 							=> "med_pharmacy_action",
									"hid_table_id" 					=> "8",
									"hid_page_type" 				=> "A",
									"hid_mt_tran_id" 				=> $intTranId,
									"TaRtxt_store_name" 			=> $rsPharmacy[0]['store_name'],
									"Tatxt_store_number" 			=> $rsPharmacy[0]['store_number'],
									"TaRtxt_ncpdpid" 				=> $rsPharmacy[0]['ncpdpid'],
									"Tatxt_dea" 					=> $rsPharmacy[0]['dea'],
									"Tatxt_npi" 					=> $rsPharmacy[0]['npi'],
									"Tatxt_file_id" 				=> $rsPharmacy[0]['file_id'],
									"Tatxt_hin" 					=> $rsPharmacy[0]['hin'],
									"Tatxt_bin"						=> $rsPharmacy[0]['bin'],
									"Tatxt_medicaid_number" 		=> $rsPharmacy[0]['medicaid_number'],
									"Tatxt_medicare_number" 		=> $rsPharmacy[0]['medicare_number'],
									"Tatxt_mutually_defined" 		=> $rsPharmacy[0]['mutually_defined'],
									"Tatxt_naic_code" 				=> $rsPharmacy[0]['naic_code'],
									"Tatxt_payer_id" 				=> $rsPharmacy[0]['payer_id'],
									"Tatxt_ppo_number" 				=> $rsPharmacy[0]['ppo_number'],
									"Tatxt_prior_authorization" 	=> $rsPharmacy[0]['prior_authorization'],
									"Tatxt_promotion_number" 		=> $rsPharmacy[0]['promotion_number'],
									"Tatxt_secondary_coverage" 		=> $rsPharmacy[0]['secondary_coverage'],
									"Tatxt_social_security" 		=> $rsPharmacy[0]['social_security'],
									"Tatxt_state_license" 			=> $rsPharmacy[0]['state_license'],
									"Tatxt_first_name" 				=> $rsPharmacy[0]['first_name'],
									"Tatxt_middle_name"				=> $rsPharmacy[0]['middle_name'],
									"Tatxt_last_name" 				=> $rsPharmacy[0]['last_name'],
									"Tatxt_suffix" 					=> $rsPharmacy[0]['suffix'],
									"Tatxt_prefix" 					=> $rsPharmacy[0]['prefix'],
									"TaRtxt_address_line1" 			=> $rsPharmacy[0]['address_line1'],
									"Tatxt_address_line2" 			=> $rsPharmacy[0]['address_line2'],
									"Tatxt_cross_street" 			=> $rsPharmacy[0]['cross_street'],
									"TaRtxt_city" 					=> $rsPharmacy[0]['city'],
									"Rslt_state" 					=> $rsPharmacy[0]['state'],
									"InRtxt_zip" 					=> $rsPharmacy[0]['zip'],
									"TaRtxt_phone_primary" 			=> $rsPharmacy[0]['phone_primary'],
									"TaRtxt_fax" 					=> $rsPharmacy[0]['fax'],
									"Tatxt_email" 					=> $rsPharmacy[0]['email'],
									"Taslt_phone_alt1_qualifier" 	=> $rsPharmacy[0]['phone_alt1_qualifier'],
									"Tatxt_phone_alt1" 				=> $rsPharmacy[0]['phone_alt1'],
									"Taslt_phone_alt2_qualifier" 	=> $rsPharmacy[0]['phone_alt2_qualifier'],
									"Tatxt_phone_alt2" 				=> $rsPharmacy[0]['phone_alt2'],
									"Taslt_phone_alt3_qualifier" 	=> $rsPharmacy[0]['phone_alt3_qualifier'],
									"Tatxt_phone_alt3" 				=> $rsPharmacy[0]['phone_alt3'],
									"Taslt_phone_alt4_qualifier" 	=> $rsPharmacy[0]['phone_alt4_qualifier'],
									"Tatxt_phone_alt4" 				=> $rsPharmacy[0]['phone_alt4'],
									"Taslt_phone_alt5_qualifier" 	=> $rsPharmacy[0]['phone_alt5_qualifier'],
									"Tatxt_phone_alt5" 				=> $rsPharmacy[0]['phone_alt5'],
									"Taslt_phone_alt6_qualifier" 	=> $rsPharmacy[0]['phone_alt6_qualifier'],
									"Tatxt_phone_alt6" 				=> $rsPharmacy[0]['phone_alt6'],
									"Tatxt_partner_account" 		=> $rsPharmacy[0]['partner_account'],
									"Tatxt_service_level" 			=> $rsPharmacy[0]['service_level'],
									"DtRtxt_active_start_time" 		=> date("m-d-Y",strtotime($rsPharmacy[0]['active_start_time'])),
									"Tatxt_active_start_time_one" 	=> date("H:i:s",strtotime($rsPharmacy[0]['active_start_time'])),
									"DtRtxt_active_end_time" 		=> date("m-d-Y",strtotime($rsPharmacy[0]['active_end_time'])),
									"Tatxt_active_end_time_one" 	=> date("H:i:s",strtotime($rsPharmacy[0]['active_end_time'])),
									"Tarad_twenty_four_hour_flag" 	=> $rsPharmacy[0]['twenty_four_hour_flag'],
									"Tatxt_fax_portal" 				=> $rsPharmacy[0]['fax_portal'],
									"hid_pharm_service_action"		=>	"UPDATE_PHARMACY",
									"smt_submit" 					=> "Save"	
								);

	$objPage->restoreSearchResult($arrHiddens);
?>