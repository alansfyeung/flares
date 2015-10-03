--==================================
-- 206 FLARES 
-- MySQL Schema Dump
-- Environment: local, Date: 2015-10-02 07:44:39
-- Triggered by: `artisan db:save s`
--==================================


CREATE TABLE `activity` (
  `acty_id` int(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `acty_type` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `desc` text COLLATE utf8mb4_bin COMMENT 'Alan Yeung',
  `is_parade_night` int(1) NOT NULL DEFAULT '1',
  `is_half_day` int(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`acty_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin

CREATE TABLE `attendance` (
  `att_id` int(6) NOT NULL AUTO_INCREMENT,
  `regt_num` varchar(10) COLLATE utf8mb4_bin NOT NULL,
  `prev_att_id` int(6) DEFAULT NULL,
  `date` date NOT NULL,
  `acty_id` int(6) DEFAULT NULL,
  `recorded_value` varchar(3) COLLATE utf8mb4_bin NOT NULL,
  `is_late` int(1) NOT NULL DEFAULT '0',
  `leave_id` int(6) DEFAULT NULL,
  `is_sms_sent` int(1) NOT NULL DEFAULT '0',
  `sms_timestamp` datetime DEFAULT NULL,
  `sms_mobile` varchar(12) COLLATE utf8mb4_bin DEFAULT NULL,
  `sms_failure` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `recorded_by` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `comments` text COLLATE utf8mb4_bin,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`att_id`),
  KEY `system_user` (`recorded_by`),
  KEY `prev_att_id` (`prev_att_id`),
  KEY `acty_id` (`acty_id`),
  KEY `leave_id` (`leave_id`),
  CONSTRAINT `fk_att_acty_id` FOREIGN KEY (`acty_id`) REFERENCES `activity` (`acty_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_leave_id` FOREIGN KEY (`leave_id`) REFERENCES `leave` (`leave_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_prev_att_id` FOREIGN KEY (`prev_att_id`) REFERENCES `attendance` (`att_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_recorded_by` FOREIGN KEY (`recorded_by`) REFERENCES `system_users` (`forums_username`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin

CREATE TABLE `leave` (
  `leave_id` int(6) NOT NULL AUTO_INCREMENT,
  `regt_num` varchar(10) COLLATE utf8mb4_bin NOT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `reason` text COLLATE utf8mb4_bin NOT NULL,
  `is_approved` int(1) NOT NULL DEFAULT '0',
  `is_autogen` int(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`leave_id`),
  KEY `regt_num` (`regt_num`),
  CONSTRAINT `fk_regt_num` FOREIGN KEY (`regt_num`) REFERENCES `member` (`regt_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin

CREATE TABLE `master_live` (
  `regt_num` varchar(10) COLLATE utf8mb4_bin NOT NULL,
  `first_name` varchar(100) CHARACTER SET utf8mb4 NOT NULL,
  `last_name` varchar(100) CHARACTER SET utf8mb4 NOT NULL,
  `sex` varchar(1) COLLATE utf8mb4_bin DEFAULT NULL,
  `forums_username` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `forums_userid` int(3) DEFAULT NULL,
  `is_active` int(1) NOT NULL DEFAULT '1',
  `is_fully_enrolled` int(1) NOT NULL DEFAULT '0',
  `coms_username` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `coms_id` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `member_mobile` varchar(12) COLLATE utf8mb4_bin DEFAULT NULL,
  `member_email` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `street_addr` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `suburb` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `state` varchar(3) COLLATE utf8mb4_bin DEFAULT NULL,
  `postcode` int(4) DEFAULT NULL,
  `home_phone` varchar(12) COLLATE utf8mb4_bin DEFAULT NULL,
  `school` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `parent_email` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `parent_mobile` varchar(12) COLLATE utf8mb4_bin DEFAULT NULL,
  `parent_type` varchar(40) COLLATE utf8mb4_bin DEFAULT NULL,
  `parent_custodial` varchar(40) COLLATE utf8mb4_bin DEFAULT NULL,
  `parent_preferred_comm` varchar(40) COLLATE utf8mb4_bin DEFAULT NULL,
  `med_allergies` varchar(400) COLLATE utf8mb4_bin DEFAULT NULL,
  `med_cond` varchar(400) COLLATE utf8mb4_bin DEFAULT NULL,
  `sdr` varchar(400) COLLATE utf8mb4_bin DEFAULT NULL,
  `is_med_lifethreat` int(1) NOT NULL DEFAULT '0',
  `is_med_hmp` int(1) NOT NULL DEFAULT '0',
  `is_idcard_printed` int(1) NOT NULL DEFAULT '0',
  `idcard_expiry` date DEFAULT NULL,
  `idcard_at_bn` int(1) NOT NULL DEFAULT '0',
  `idcard_serial_num` varchar(10) COLLATE utf8mb4_bin DEFAULT NULL,
  `idcard_remarks` varchar(400) COLLATE utf8mb4_bin DEFAULT NULL,
  `is_qual_mb` int(1) NOT NULL DEFAULT '0',
  `is_qual_s303` int(1) NOT NULL DEFAULT '0',
  `is_qual_gf` int(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`regt_num`),
  UNIQUE KEY `unique_forums_username` (`forums_username`) USING BTREE,
  UNIQUE KEY `unique_coms_id` (`coms_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin

CREATE TABLE `member` (
  `regt_num` varchar(10) COLLATE utf8mb4_bin NOT NULL,
  `first_name` varchar(100) CHARACTER SET utf8mb4 NOT NULL,
  `last_name` varchar(100) CHARACTER SET utf8mb4 NOT NULL,
  `sex` varchar(1) COLLATE utf8mb4_bin DEFAULT NULL,
  `forums_username` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `forums_userid` int(3) DEFAULT NULL,
  `is_active` int(1) NOT NULL DEFAULT '0',
  `is_fully_enrolled` int(1) NOT NULL DEFAULT '0',
  `coms_username` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `coms_id` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `member_mobile` varchar(12) COLLATE utf8mb4_bin DEFAULT NULL,
  `member_email` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `street_addr` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `suburb` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `state` varchar(3) COLLATE utf8mb4_bin DEFAULT NULL,
  `postcode` int(4) DEFAULT NULL,
  `home_phone` varchar(12) COLLATE utf8mb4_bin DEFAULT NULL,
  `school` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `parent_email` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `parent_mobile` varchar(12) COLLATE utf8mb4_bin DEFAULT NULL,
  `parent_type` varchar(40) COLLATE utf8mb4_bin DEFAULT NULL,
  `parent_custodial` varchar(40) COLLATE utf8mb4_bin DEFAULT NULL,
  `parent_preferred_comm` varchar(40) COLLATE utf8mb4_bin DEFAULT NULL,
  `med_allergies` varchar(400) COLLATE utf8mb4_bin DEFAULT NULL,
  `med_cond` varchar(400) COLLATE utf8mb4_bin DEFAULT NULL,
  `sdr` varchar(400) COLLATE utf8mb4_bin DEFAULT NULL,
  `is_med_lifethreat` int(1) NOT NULL DEFAULT '0',
  `is_med_hmp` int(1) NOT NULL DEFAULT '0',
  `is_idcard_printed` int(1) NOT NULL DEFAULT '0',
  `idcard_expiry` date DEFAULT NULL,
  `idcard_at_bn` int(1) NOT NULL DEFAULT '0',
  `idcard_serial_num` varchar(10) COLLATE utf8mb4_bin DEFAULT NULL,
  `idcard_remarks` varchar(400) COLLATE utf8mb4_bin DEFAULT NULL,
  `is_qual_mb` int(1) NOT NULL DEFAULT '0',
  `is_qual_s303` int(1) NOT NULL DEFAULT '0',
  `is_qual_gf` int(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`regt_num`),
  UNIQUE KEY `unique_forums_username` (`forums_username`) USING BTREE,
  UNIQUE KEY `unique_coms_id` (`coms_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin

CREATE TABLE `member_picture` (
  `img_id` int(6) NOT NULL AUTO_INCREMENT,
  `regt_num` varchar(10) COLLATE utf8mb4_bin NOT NULL,
  `photo_blob` longblob NOT NULL,
  `caption` text COLLATE utf8mb4_bin,
  `file_name` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`img_id`),
  KEY `regt_num` (`regt_num`),
  CONSTRAINT `fk_regt_num_member_picture` FOREIGN KEY (`regt_num`) REFERENCES `member` (`regt_num`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin

CREATE TABLE `posting_promo` (
  `posting_id` int(6) NOT NULL AUTO_INCREMENT,
  `regt_num` varchar(10) COLLATE utf8mb4_bin NOT NULL,
  `effective_date` date DEFAULT NULL,
  `new_platoon` varchar(10) COLLATE utf8mb4_bin DEFAULT NULL,
  `new_posting` varchar(10) COLLATE utf8mb4_bin DEFAULT NULL,
  `new_rank` varchar(10) COLLATE utf8mb4_bin DEFAULT NULL,
  `is_acting` int(1) NOT NULL DEFAULT '0',
  `promo_auth` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `is_discharge` int(1) NOT NULL DEFAULT '0',
  `recorded_by` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`posting_id`),
  KEY `new_platoon` (`new_platoon`),
  KEY `new_posting` (`new_posting`),
  KEY `new_rank` (`new_rank`),
  KEY `recorded_by` (`recorded_by`),
  KEY `regt_num` (`regt_num`),
  CONSTRAINT `fk_new_platoon` FOREIGN KEY (`new_platoon`) REFERENCES `ref_platoons` (`abbr`) ON UPDATE CASCADE,
  CONSTRAINT `fk_new_posting` FOREIGN KEY (`new_posting`) REFERENCES `ref_postings` (`abbr`) ON UPDATE CASCADE,
  CONSTRAINT `fk_new_rank` FOREIGN KEY (`new_rank`) REFERENCES `ref_ranks` (`abbr`) ON UPDATE CASCADE,
  CONSTRAINT `fk_posting_recorded_by` FOREIGN KEY (`recorded_by`) REFERENCES `system_users` (`forums_username`) ON UPDATE CASCADE,
  CONSTRAINT `fk_posting_regt_num` FOREIGN KEY (`regt_num`) REFERENCES `member` (`regt_num`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=254 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin

CREATE TABLE `ref_misc` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `cond` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin

CREATE TABLE `ref_platoons` (
  `abbr` varchar(10) COLLATE utf8mb4_bin NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `pos` int(4) NOT NULL,
  PRIMARY KEY (`abbr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin

CREATE TABLE `ref_postings` (
  `abbr` varchar(10) COLLATE utf8mb4_bin NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `pos` int(4) NOT NULL,
  PRIMARY KEY (`abbr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin

CREATE TABLE `ref_ranks` (
  `abbr` varchar(10) COLLATE utf8mb4_bin NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `pos` int(4) NOT NULL,
  PRIMARY KEY (`abbr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin

CREATE TABLE `system_users` (
  `forums_username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `access_level` int(2) NOT NULL,
  `last_login_time` datetime DEFAULT NULL,
  `fallback_pwd` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`forums_username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4

