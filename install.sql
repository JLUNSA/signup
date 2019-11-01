CREATE TABLE `baoming` (
	`jxnum` VARCHAR(10) NOT NULL COLLATE 'utf8mb4_general_ci',
	`name` VARCHAR(20) NOT NULL COLLATE 'utf8mb4_general_ci',
	`department` VARCHAR(20) NOT NULL COLLATE 'utf8mb4_general_ci',
	`email` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
	`passwd` VARCHAR(65) NOT NULL COLLATE 'utf8mb4_general_ci',
	`ip` VARCHAR(60) NOT NULL COLLATE 'utf8mb4_general_ci',
	`reserve_time` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`verified` TINYINT(4) NOT NULL DEFAULT '0',
	`time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`jxnum`)
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;
