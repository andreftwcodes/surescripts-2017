<?php






$fbFieldSep = '|';
define('FB_COL_SEP', '|');

define('EAR_COL_SEP', '|');







$rxFile['FSL']['HeaderID'] = "FHD";
$rxFile['CRF']['HeaderID'] = "XHD";	
$rxFile['ALT']['HeaderID'] = "AHD";
$rxFile['DCL']['HeaderID'] = "LHD";	
$rxFile['COV']['HeaderID'] = "GHD";
$rxFile['COP']['HeaderID'] = "CHD";

$rxFile['FSL']['FooterID'] = "FTR";
$rxFile['CRF']['FooterID'] = "XTR";
$rxFile['ALT']['FooterID'] = "ATR";
$rxFile['DCL']['FooterID'] = "LTR";
$rxFile['COV']['FooterID'] = "GTR";
$rxFile['COP']['FooterID'] = "CTR";


$rxFile['FSL']['Table'] = 'formulary_status_header';

$rxFile['ALT']['Table'] = 'formulary_alternate_header';

$rxFile['COV']['Table'] = 'formulary_coverage_header';
$rxFile['COP']['Table'] = 'formulary_copay_header';

$rxFileTable['FSL'] = $rxFile['FSL']['Table'];
$rxFileTable['ALT'] = $rxFile['ALT']['Table'];
$rxFileTable['COV'] = $rxFile['COV']['Table'];
$rxFileTable['COP'] = $rxFile['COP']['Table'];







$fbHeader = array();
$fbFooter = array();


$fbHeader['COMMON'] = array();
$fbHeader['COMMON'][] = array('FIELD' => 'sender_id', 'POS' => 2);
$fbHeader['COMMON'][] = array('FIELD' => 'source_name', 'POS' => 5);
$fbHeader['COMMON'][] = array('FIELD' => 'transmission_date', 'POS' => 7, 'CAST' => 'DATE');
$fbHeader['COMMON'][] = array('FIELD' => 'transmission_time', 'POS' => 8, 'CAST' => 'TIME');
$fbHeader['COMMON'][] = array('FIELD' => 'transmission_action', 'POS' => 10);
$fbHeader['COMMON'][] = array('FIELD' => 'extract_date', 'POS' => 11, 'CAST' => 'DATE');
$fbHeader['COMMON'][] = array('FIELD' => 'file_type', 'POS' => 12);


$fbFooter['COMMON'] = array();
$fbFooter['COMMON'][] = array('FIELD' => 'record_type', 'POS' => 0, 'IGNORE' => 'Y');
$fbFooter['COMMON'][] = array('FIELD' => 'total_records', 'POS' => 0, 'IGNORE' => 'Y');




$fbHeader['FSL'] = array();
$fbHeader['FSL'][] = array('FIELD' => 'formulary_id', 'POS' => 1);
$fbHeader['FSL'][] = array('FIELD' => 'formulary_name', 'POS' => 2);
$fbHeader['FSL'][] = array('FIELD' => 'nl_rx_branded_status', 'POS' => 3);
$fbHeader['FSL'][] = array('FIELD' => 'nl_rx_generics_status', 'POS' => 4);
$fbHeader['FSL'][] = array('FIELD' => 'nl_otc_branded_status', 'POS' => 5);
$fbHeader['FSL'][] = array('FIELD' => 'nl_otc_generics_status', 'POS' => 6);
$fbHeader['FSL'][] = array('FIELD' => 'nl_supplies_status', 'POS' => 7);
$fbHeader['FSL'][] = array('FIELD' => 'relative_cost_limit', 'POS' => 8);
$fbHeader['FSL'][] = array('FIELD' => 'list_action', 'POS' => 9);
$fbHeader['FSL'][] = array('FIELD' => 'effective_date', 'POS' => 10, 'CAST' => 'DATE');

$fbFooter['FSL'] = array();
$fbFooter['FSL'][] = array('FIELD' => 'total_records', 'POS' => 1);


$fbHeader['ALT'][] = array('FIELD' => 'alternative_id', 'POS' => 1);
$fbHeader['ALT'][] = array('FIELD' => 'list_action', 'POS' => 2);
$fbHeader['ALT'][] = array('FIELD' => 'effective_date', 'POS' => 3, 'CAST' => 'DATE');

$fbFooter['ALT'][] = array('FIELD' => 'total_records', 'POS' => 1);


$fbHeader['COV'][] = array('FIELD' => 'coverage_id', 'POS' => 1);
$fbHeader['COV'][] = array('FIELD' => 'coverage_type', 'POS' => 2);
$fbHeader['COV'][] = array('FIELD' => 'list_action', 'POS' => 3);
$fbHeader['COV'][] = array('FIELD' => 'effective_date', 'POS' => 4, 'CAST' => 'DATE');

$fbFooter['COV'][] = array('FIELD' => 'total_records', 'POS' => 1);


$fbHeader['COP'][] = array('FIELD' => 'copay_id', 'POS' => 1);
$fbHeader['COP'][] = array('FIELD' => 'copay_list_type', 'POS' => 2);
$fbHeader['COP'][] = array('FIELD' => 'list_action', 'POS' => 3);
$fbHeader['COP'][] = array('FIELD' => 'effective_date', 'POS' => 4, 'CAST' => 'DATE');

$fbFooter['COP'][] = array('FIELD' => 'total_records', 'POS' => 1);



$fbDetail['ALT'] = 'formulary_alternate_detail';
$fbDetail['COP'] = array(
			'DS' => 'formulary_copay_detail_ds', 
			'SL' => 'formulary_copay_detail_sl'  
			);
$fbDetail['COV'] = array(
			'AL' => 'formulary_coverage_detail_al', 
			'DE' => 'formulary_coverage_detail_de_pa_mn_st',
			'PA' => 'formulary_coverage_detail_de_pa_mn_st',
			'MN' => 'formulary_coverage_detail_de_pa_mn_st',
			'ST' => 'formulary_coverage_detail_de_pa_mn_st',
			'GL' => 'formulary_coverage_detail_gl', 
			'QL' => 'formulary_coverage_detail_ql', 
			'RD' => 'formulary_coverage_detail_rd', 
			'RS' => 'formulary_coverage_detail_rs', 
			'SM' => 'formulary_coverage_detail_sm', 
			'TM' => 'formulary_coverage_detail_tm', 
			);
$fbDetail['FSL'] = 'formulary_status_detail';







$tColumns['formulary_alternate_detail'][1] = 'char_identifier';
$tColumns['formulary_alternate_detail'][2] = 'product_id';
$tColumns['formulary_alternate_detail'][3] = 'product_qualifier';
$tColumns['formulary_alternate_detail'][4] = 'drug_ref_number';
$tColumns['formulary_alternate_detail'][5] = 'drug_ref_qualifier';
$tColumns['formulary_alternate_detail'][6] = 'rxnorm_code';
$tColumns['formulary_alternate_detail'][7] = 'rxnorm_qualifier';
$tColumns['formulary_alternate_detail'][8] = 'alt_product_id';
$tColumns['formulary_alternate_detail'][9] = 'alt_product_qualifier';
$tColumns['formulary_alternate_detail'][10] = 'alt_drug_ref_number';
$tColumns['formulary_alternate_detail'][11] = 'alt_drug_ref_qualifier';
$tColumns['formulary_alternate_detail'][12] = 'alt_rxnorm_code';
$tColumns['formulary_alternate_detail'][13] = 'alt_rxnorm_qualifier';
$tColumns['formulary_alternate_detail'][14] = 'preference_level';


$tColumns['formulary_copay_detail_ds'][1] = 'char_identifier';
$tColumns['formulary_copay_detail_ds'][2] = 'copay_id';
$tColumns['formulary_copay_detail_ds'][3] = 'product_id';
$tColumns['formulary_copay_detail_ds'][4] = 'product_qualifier';
$tColumns['formulary_copay_detail_ds'][5] = 'drug_ref_number';
$tColumns['formulary_copay_detail_ds'][6] = 'drug_ref_qualifier';
$tColumns['formulary_copay_detail_ds'][7] = 'rxnorm_code';
$tColumns['formulary_copay_detail_ds'][8] = 'rxnorm_qualifier';
$tColumns['formulary_copay_detail_ds'][9] = 'pharmacy_type';
$tColumns['formulary_copay_detail_ds'][10] = 'flat_copay_amount';
$tColumns['formulary_copay_detail_ds'][11] = 'percentage_copay_rate';
$tColumns['formulary_copay_detail_ds'][12] = 'first_copay_term';
$tColumns['formulary_copay_detail_ds'][13] = 'min_copay';
$tColumns['formulary_copay_detail_ds'][14] = 'max_copay';
$tColumns['formulary_copay_detail_ds'][15] = 'days_supply';
$tColumns['formulary_copay_detail_ds'][16] = 'copay_tier';
$tColumns['formulary_copay_detail_ds'][17] = 'max_copay_tier';


$tColumns['formulary_copay_detail_sl'][1] = 'char_identifier';
$tColumns['formulary_copay_detail_sl'][2] = 'copay_id';
$tColumns['formulary_copay_detail_sl'][3] = 'formulary_status';
$tColumns['formulary_copay_detail_sl'][4] = 'product_type';
$tColumns['formulary_copay_detail_sl'][5] = 'pharmacy_type';
$tColumns['formulary_copay_detail_sl'][6] = 'out_pocket_start_range';
$tColumns['formulary_copay_detail_sl'][7] = 'out_pocket_start_end';
$tColumns['formulary_copay_detail_sl'][8] = 'flat_copay_amount';
$tColumns['formulary_copay_detail_sl'][9] = 'percentage_copay_rate';
$tColumns['formulary_copay_detail_sl'][10] = 'first_copay_term';
$tColumns['formulary_copay_detail_sl'][11] = 'min_copay';
$tColumns['formulary_copay_detail_sl'][12] = 'max_copay';
$tColumns['formulary_copay_detail_sl'][13] = 'days_supply';
$tColumns['formulary_copay_detail_sl'][14] = 'copay_tier';
$tColumns['formulary_copay_detail_sl'][15] = 'max_copay_tier';


$tColumns['formulary_coverage_detail_al'][1] = 'char_identifier';
$tColumns['formulary_coverage_detail_al'][2] = 'coverage_id';
$tColumns['formulary_coverage_detail_al'][3] = 'product_id';
$tColumns['formulary_coverage_detail_al'][4] = 'product_qualifier';
$tColumns['formulary_coverage_detail_al'][5] = 'drug_ref_number';
$tColumns['formulary_coverage_detail_al'][6] = 'drug_ref_qualifier';
$tColumns['formulary_coverage_detail_al'][7] = 'rxnorm_code';
$tColumns['formulary_coverage_detail_al'][8] = 'rxnorm_qualifier';
$tColumns['formulary_coverage_detail_al'][9] = 'min_age';
$tColumns['formulary_coverage_detail_al'][10] = 'min_age_qualifier';
$tColumns['formulary_coverage_detail_al'][11] = 'max_age';
$tColumns['formulary_coverage_detail_al'][12] = 'max_age_qualifier';


$tColumns['formulary_coverage_detail_de_pa_mn_st'][1] = 'char_identifier';
$tColumns['formulary_coverage_detail_de_pa_mn_st'][2] = 'coverage_id';
$tColumns['formulary_coverage_detail_de_pa_mn_st'][3] = 'product_id';
$tColumns['formulary_coverage_detail_de_pa_mn_st'][4] = 'product_qualifier';
$tColumns['formulary_coverage_detail_de_pa_mn_st'][5] = 'drug_ref_number';
$tColumns['formulary_coverage_detail_de_pa_mn_st'][6] = 'drug_ref_qualifier';
$tColumns['formulary_coverage_detail_de_pa_mn_st'][7] = 'rxnorm_code';
$tColumns['formulary_coverage_detail_de_pa_mn_st'][8] = 'rxnorm_code';


$tColumns['formulary_coverage_detail_gl'][1] = 'char_identifier';
$tColumns['formulary_coverage_detail_gl'][2] = 'coverage_id';
$tColumns['formulary_coverage_detail_gl'][3] = 'product_id';
$tColumns['formulary_coverage_detail_gl'][4] = 'product_qualifier';
$tColumns['formulary_coverage_detail_gl'][5] = 'drug_ref_number';
$tColumns['formulary_coverage_detail_gl'][6] = 'drug_ref_qualifier';
$tColumns['formulary_coverage_detail_gl'][7] = 'rxnorm_code';
$tColumns['formulary_coverage_detail_gl'][8] = 'rxnorm_qualifier';
$tColumns['formulary_coverage_detail_gl'][9] = 'gender';


$tColumns['formulary_coverage_detail_ql'][1] = 'char_identifier';
$tColumns['formulary_coverage_detail_ql'][2] = 'coverage_id';
$tColumns['formulary_coverage_detail_ql'][3] = 'product_id';
$tColumns['formulary_coverage_detail_ql'][4] = 'product_qualifier';
$tColumns['formulary_coverage_detail_ql'][5] = 'drug_ref_number';
$tColumns['formulary_coverage_detail_ql'][6] = 'drug_ref_qualifier';
$tColumns['formulary_coverage_detail_ql'][7] = 'rxnorm_code';
$tColumns['formulary_coverage_detail_ql'][8] = 'rxnorm_qualifier';
$tColumns['formulary_coverage_detail_ql'][9] = 'max_amt';
$tColumns['formulary_coverage_detail_ql'][10] = 'max_amt_qualifier';
$tColumns['formulary_coverage_detail_ql'][11] = 'max_amt_time_period';
$tColumns['formulary_coverage_detail_ql'][12] = 'max_amt_time_period_startdate';
$tColumns['formulary_coverage_detail_ql'][13] = 'max_amt_time_period_enddate';
$tColumns['formulary_coverage_detail_ql'][14] = 'max_amt_time_period_unit';


$tColumns['formulary_coverage_detail_rd'][1] = 'char_identifier';
$tColumns['formulary_coverage_detail_rd'][2] = 'coverage_id';
$tColumns['formulary_coverage_detail_rd'][3] = 'product_id';
$tColumns['formulary_coverage_detail_rd'][4] = 'product_qualifier';
$tColumns['formulary_coverage_detail_rd'][5] = 'drug_ref_number';
$tColumns['formulary_coverage_detail_rd'][6] = 'drug_ref_qualifier';
$tColumns['formulary_coverage_detail_rd'][7] = 'rxnorm_code';
$tColumns['formulary_coverage_detail_rd'][8] = 'rxnorm_qualifier';
$tColumns['formulary_coverage_detail_rd'][9] = 'resource_link_type';
$tColumns['formulary_coverage_detail_rd'][10] = 'resource_url';


$tColumns['formulary_coverage_detail_rs'][1] = 'char_identifier';
$tColumns['formulary_coverage_detail_rs'][2] = 'coverage_id';
$tColumns['formulary_coverage_detail_rs'][3] = 'resource_link_type';
$tColumns['formulary_coverage_detail_rs'][4] = 'resource_url';


$tColumns['formulary_coverage_detail_sm'][1] = 'char_identifier';
$tColumns['formulary_coverage_detail_sm'][2] = 'coverage_id';
$tColumns['formulary_coverage_detail_sm'][3] = 'product_id';
$tColumns['formulary_coverage_detail_sm'][4] = 'product_qualifier';
$tColumns['formulary_coverage_detail_sm'][5] = 'drug_ref_number';
$tColumns['formulary_coverage_detail_sm'][6] = 'drug_ref_qualifier';
$tColumns['formulary_coverage_detail_sm'][7] = 'rxnorm_code';
$tColumns['formulary_coverage_detail_sm'][8] = 'rxnorm_qualifier';
$tColumns['formulary_coverage_detail_sm'][9] = 'step_product_id';
$tColumns['formulary_coverage_detail_sm'][10] = 'step_product_qualifier';
$tColumns['formulary_coverage_detail_sm'][11] = 'step_drug_ref_number';
$tColumns['formulary_coverage_detail_sm'][12] = 'step_drug_ref_qualifier';
$tColumns['formulary_coverage_detail_sm'][13] = 'step_rxnorm_code';
$tColumns['formulary_coverage_detail_sm'][14] = 'step_rxnorm_qualifier';
$tColumns['formulary_coverage_detail_sm'][15] = 'step_drug_class_id';
$tColumns['formulary_coverage_detail_sm'][16] = 'step_drug_subclass_id';
$tColumns['formulary_coverage_detail_sm'][17] = 'no_of_drug_try';
$tColumns['formulary_coverage_detail_sm'][18] = 'step_order';
$tColumns['formulary_coverage_detail_sm'][19] = 'diagnosis_code';
$tColumns['formulary_coverage_detail_sm'][20] = 'diagnosis_code_qualifier';


$tColumns['formulary_coverage_detail_tm'][1] = 'char_identifier';
$tColumns['formulary_coverage_detail_tm'][2] = 'coverage_id';
$tColumns['formulary_coverage_detail_tm'][3] = 'product_id';
$tColumns['formulary_coverage_detail_tm'][4] = 'product_qualifier';
$tColumns['formulary_coverage_detail_tm'][5] = 'drug_ref_number';
$tColumns['formulary_coverage_detail_tm'][6] = 'drug_ref_qualifier';
$tColumns['formulary_coverage_detail_tm'][7] = 'rxnorm_code';
$tColumns['formulary_coverage_detail_tm'][8] = 'rxnorm_qualifier';
$tColumns['formulary_coverage_detail_tm'][9] = 'message_short';
$tColumns['formulary_coverage_detail_tm'][10] = 'message_long';


$tColumns['formulary_status_detail'][1] = 'change_identifier';
$tColumns['formulary_status_detail'][2] = 'product_id';
$tColumns['formulary_status_detail'][3] = 'product_qualifier';
$tColumns['formulary_status_detail'][4] = 'drug_ref_number';
$tColumns['formulary_status_detail'][5] = 'drug_ref_qualifier';
$tColumns['formulary_status_detail'][6] = 'rxnorm_code';
$tColumns['formulary_status_detail'][7] = 'rxnorm_qualifier';
$tColumns['formulary_status_detail'][8] = 'formulary_status';
$tColumns['formulary_status_detail'][9] = 'relative_cost';

