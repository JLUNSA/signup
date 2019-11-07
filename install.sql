CREATE TABLE `class` (
	`class_id` INT(11) NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
	`detail` VARCHAR(500) NOT NULL COLLATE 'utf8mb4_general_ci',
	`date` DATE NOT NULL,
	`start_select` TIMESTAMP NOT NULL,
	`end_select` TIMESTAMP NOT NULL,
	PRIMARY KEY (`class_id`)
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;
CREATE TABLE `log` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`student_id` VARCHAR(10) NOT NULL COLLATE 'utf8mb4_general_ci',
	`operation` VARCHAR(20) NOT NULL COLLATE 'utf8mb4_general_ci',
	`ip` VARCHAR(50) NOT NULL COMMENT 'v6/v4都可以' COLLATE 'utf8mb4_general_ci',
	`result` VARCHAR(20) NOT NULL COMMENT 'OK/FAILED' COLLATE 'utf8mb4_general_ci',
	`payload` VARCHAR(500) NOT NULL COLLATE 'utf8mb4_general_ci',
	PRIMARY KEY (`id`)
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=2
;
CREATE TABLE `selected_class` (
	`student_id` VARCHAR(10) NOT NULL COLLATE 'utf8mb4_general_ci',
	`subclass_id` INT(11) NOT NULL,
	`select_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	UNIQUE INDEX `索引 1` (`student_id`, `subclass_id`)
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;
CREATE TABLE `subclass` (
	`subclass_id` INT(11) NOT NULL AUTO_INCREMENT,
	`class_id` INT(11) NOT NULL,
	`title` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
	`capacity` INT(11) NOT NULL DEFAULT '0',
	`start_time` TIMESTAMP NOT NULL,
	`end_time` TIMESTAMP NOT NULL,
	PRIMARY KEY (`subclass_id`)
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;
CREATE TABLE `user` (
	`student_id` VARCHAR(10) NOT NULL COLLATE 'utf8mb4_general_ci',
	`name` VARCHAR(30) NOT NULL COLLATE 'utf8mb4_general_ci',
	`department` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
	`email` VARCHAR(100) NOT NULL COLLATE 'utf8mb4_general_ci',
	`password` VARCHAR(100) NOT NULL COLLATE 'utf8mb4_general_ci',
	`photo` VARCHAR(100) NOT NULL COLLATE 'utf8mb4_general_ci',
	`verified` TINYINT(4) NOT NULL DEFAULT '0',
	`is_admin` TINYINT(4) NOT NULL DEFAULT '0',
	`insert_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`student_id`)
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;
