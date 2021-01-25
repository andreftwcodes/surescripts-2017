ALTER TABLE `outbound_message_history` ADD `immediate_response_edifact` VARCHAR( 5000 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT 'This column will store only EDIFact part of immediate_response which will be base64_decoded for each try of sending message.';

ALTER TABLE `outbound_message_history` CHANGE `tran_id` `tran_id` BIGINT(20) NOT NULL AUTO_INCREMENT COMMENT '' FIRST, CHANGE `message_id` `message_id` VARCHAR(35) NULL COLLATE latin1_swedish_ci COMMENT '' AFTER `tran_id`, CHANGE `timestamp` `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '' AFTER `message_id`, CHANGE `message_type` `message_type` VARCHAR(50) NOT NULL COLLATE latin1_swedish_ci COMMENT 'DirectoryDownload, OutMessage' AFTER `timestamp`, CHANGE `message_params` `message_params` TEXT NULL COLLATE latin1_swedish_ci COMMENT 'Whatever parameters passed to Message object in serialized form' AFTER `message_type`, CHANGE `message` `message` LONGTEXT NULL COLLATE latin1_swedish_ci COMMENT 'actual xml sent' AFTER `message_params`, CHANGE `immediate_response` `immediate_response` TEXT NULL COLLATE latin1_swedish_ci COMMENT 'immediate response echoed back from Surescripts server once we sent them this message, it\'s going to be complete response with XML wrapper.' AFTER `message`, CHANGE `immediate_response_edifact` `immediate_response_edifact` VARCHAR(5000) NULL COLLATE latin1_swedish_ci COMMENT 'This column will store only EDIFact part of immediate_response which will be base64_decoded for each try of sending message.' AFTER `immediate_response`, CHANGE `om_tran_id` `om_tran_id` VARCHAR(50) NULL COLLATE latin1_swedish_ci COMMENT 'In case if this is OutBound message to be sent from Meditab product then here we should store the tran_id of out_message_transaction' AFTER `immediate_response_edifact`;

ALTER TABLE `prescriber_master` CHANGE `dea` `dea` VARCHAR(25) NULL  ;

ALTER TABLE `out_message_transaction` ADD `status_code` VARCHAR(5) DEFAULT NULL NULL COMMENT 'Status Code returned by Surescripts while we posted Message' AFTER `message_status` ;


16 OCT 09
---------------------------------------------------------------------------------------------------
ALTER TABLE `prescriber_requests` 
ADD `dentist_license_number` VARCHAR( 20 ) NULL NULL AFTER `npi_location` ,
ADD `file_id` VARCHAR( 20 ) NULL NULL AFTER `dentist_license_number` ,
ADD `medicaid_number` VARCHAR( 20 ) NULL NULL AFTER `file_id` ,
ADD `medicare_number` VARCHAR( 20 ) NULL NULL AFTER `medicaid_number` ,
ADD `ppo_number` VARCHAR( 20 ) NULL NULL AFTER `medicare_number` ,
ADD `prior_authorization` VARCHAR( 20 ) NULL NULL AFTER `ppo_number` ,
ADD `social_security` VARCHAR( 20 ) NULL NULL AFTER `prior_authorization` ,
ADD `upin` VARCHAR( 20 ) NULL NULL AFTER `social_security` ,
ADD `mutually_defined` VARCHAR( 20 ) NULL NULL AFTER `upin`;

ALTER TABLE `prescriber_master` 
ADD `dentist_license_number` VARCHAR( 20 ) NULL NULL AFTER `npi_location` ,
ADD `file_id` VARCHAR( 20 ) NULL NULL AFTER `dentist_license_number` ,
ADD `medicaid_number` VARCHAR( 20 ) NULL NULL AFTER `file_id` ,
ADD `medicare_number` VARCHAR( 20 ) NULL NULL AFTER `medicaid_number` ,
ADD `ppo_number` VARCHAR( 20 ) NULL NULL AFTER `medicare_number` ,
ADD `prior_authorization` VARCHAR( 20 ) NULL NULL AFTER `ppo_number` ,
ADD `social_security` VARCHAR( 20 ) NULL NULL AFTER `prior_authorization` ,
ADD `upin` VARCHAR( 20 ) NULL NULL AFTER `social_security` ,
ADD `mutually_defined` VARCHAR( 20 ) NULL NULL AFTER `upin`;

---------------------------------------------------------------------------------------------------

15 June 2010



ALTER TABLE `in_message_transaction` ADD INDEX `from_id` (`from_id`) ;

ALTER TABLE `in_message_transaction` ADD INDEX `to_id` (`to_id`) ;

ALTER TABLE `in_message_transaction` ADD INDEX `message_id` (`message_id`), ADD INDEX `related_to_message_id` (`related_message_id`) ;

ALTER TABLE `in_message_transaction` ADD INDEX `message_status` (`message_status`), ADD INDEX `mt_response_status` (`meditab_response_status`) ;

ALTER TABLE `out_message_transaction` ADD INDEX `meditab_id` (`meditab_id`), ADD INDEX `meditab_tran_id` (`meditab_tran_id`), ADD INDEX `from_id` (`from_id`), ADD INDEX `to_id` (`to_id`), ADD INDEX `message_id` (`message_id`), ADD INDEX `related_message_id` (`related_message_id`), ADD INDEX `message_status` (`message_status`), ADD INDEX `mt_response_status` (`meditab_response_status`) ;

ALTER TABLE `prescriber_master` ADD INDEX `last_modified_date` (`last_modified_date`) ;

ALTER TABLE `pharmacy_master` ADD INDEX `last_modified_date` (`last_modified_date`) ;


---------------------------------------------------------------------------------------------------

16 August 2010 - (Varun Shah)

CREATE TABLE `pharmacy_mos` (
	`ncpdpid` VARCHAR(7) NULL DEFAULT NULL,
	`meditab_id` VARCHAR(15) NULL DEFAULT NULL
)

CREATE TABLE `prescriber_mos` (
	`spi` VARCHAR(13) NULL DEFAULT NULL,
	`meditab_id` VARCHAR(15) NULL DEFAULT NULL
)


---------------------------------------------------------------------------------------------------

# 16 April 2011 Dharmavirsinh Jhala

ALTER TABLE `out_message_transaction`  CHANGE COLUMN `message_status` `message_status` ENUM('Pending','Sent','Error','Temp_Failed') NOT NULL AFTER `edi_message`;



# 20 July 2011 Dharmavirsinh Jhala
# New table added for logging OutMessge posting errors
CREATE TABLE `out_message_errors` (
	`om_error_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`notified` ENUM('Y','N') NOT NULL DEFAULT 'N',
	`mt_tran_id` VARCHAR(50) NULL DEFAULT NULL,
	`message_id` VARCHAR(50) NULL DEFAULT NULL,
	`from_id` VARCHAR(35) NULL DEFAULT NULL,
	`to_id` VARCHAR(35) NULL DEFAULT NULL,
	`error_code` VARCHAR(100) NULL DEFAULT NULL,
	`response` VARCHAR(5000) NULL DEFAULT NULL,
	`sent_time` DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (`om_error_id`)
)
COMMENT='Error log will be sent as an archive email from this table.'
COLLATE='latin1_swedish_ci'
ENGINE=MyISAM;


# 08 Nov 2011 Dharmavirsinh Jhala
ALTER TABLE out_message_post_job
	ADD COLUMN speaker_id TINYINT NULL AFTER last_activity_time;

ALTER TABLE out_message_transaction
	CHANGE COLUMN message_status message_status ENUM('Pending','Sent','Error','Temp_Failed','Old_Rx','Locked') NOT NULL AFTER edi_message;

---------------------------------------------------------------------------------------------------
# 09 Jan 2011 Dharmavirsinh Jhala
ALTER TABLE `out_message_transaction`  ADD COLUMN `speaker_id` TINYINT(4) NULL DEFAULT NULL AFTER `post_attempt`;
# 11 Jan 2011 Dharmavirsinh Jhala
ALTER TABLE `out_message_transaction`  CHANGE COLUMN `meditab_response_status` `meditab_response_status` ENUM('Y','N','M') NOT NULL DEFAULT 'N' COMMENT 'Y: Read, N: Unread, M: Missing - means record is on server but not in IMS db, subject of diagnosis' AFTER `error_note`;