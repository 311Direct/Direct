ALTER TABLE `Attachements` DROP FOREIGN KEY `fk_Attachements`;
ALTER TABLE `Attachements` DROP FOREIGN KEY `fk_Attachements_1`;
ALTER TABLE `Attachements` DROP FOREIGN KEY `fk_Attachements_2`;
ALTER TABLE `Comments` DROP FOREIGN KEY `fk_Comments_1`;
ALTER TABLE `Milestones` DROP FOREIGN KEY `fk_Milestones`;
ALTER TABLE `Milestones` DROP FOREIGN KEY `fk_Milestones_1`;
ALTER TABLE `Milestones` DROP FOREIGN KEY `fk_Milestones_2`;
ALTER TABLE `Notifications` DROP FOREIGN KEY `fk_Messages`;
ALTER TABLE `Projects` DROP FOREIGN KEY `fk_Projects`;
ALTER TABLE `TaskDependencies` DROP FOREIGN KEY `fk_TaskDependencies`;
ALTER TABLE `TaskDependencies` DROP FOREIGN KEY `fk_TaskDependencies_1`;
ALTER TABLE `Tasks` DROP FOREIGN KEY `fk_Tasks`;
ALTER TABLE `Tasks` DROP FOREIGN KEY `fk_Tasks_1`;
ALTER TABLE `Tasks` DROP FOREIGN KEY `fk_Tasks_2`;
ALTER TABLE `Tasks` DROP FOREIGN KEY `fk_Tasks_3`;
ALTER TABLE `Users` DROP FOREIGN KEY `fk_Users`;
ALTER TABLE `ProjectManagers` DROP FOREIGN KEY `fk_ProjectManagers`;
ALTER TABLE `ProjectManagers` DROP FOREIGN KEY `fk_ProjectManagers_1`;
ALTER TABLE `TaskAssignees` DROP FOREIGN KEY `fk_TaskAssignees`;
ALTER TABLE `TaskAssignees` DROP FOREIGN KEY `fk_TaskAssignees_1`;

DROP INDEX `fk_Attachements` ON `Attachements`;
DROP INDEX `fk_Attachements_1` ON `Attachements`;
DROP INDEX `fk_Attachements_2` ON `Attachements`;
DROP INDEX `fk_Comments_1` ON `Comments`;
DROP INDEX `fk_Milestones` ON `Milestones`;
DROP INDEX `user_id` ON `Milestones`;
DROP INDEX `manager_id` ON `Milestones`;
DROP INDEX `fk_Messages` ON `Notifications`;
DROP INDEX `fk_Projects` ON `Projects`;
DROP INDEX `fk_TaskDependencies` ON `TaskDependencies`;
DROP INDEX `fk_TaskDependencies_1` ON `TaskDependencies`;
DROP INDEX `fk_Tasks` ON `Tasks`;
DROP INDEX `fk_Tasks_1` ON `Tasks`;
DROP INDEX `fk_Tasks_2` ON `Tasks`;
DROP INDEX `fk_Tasks_3` ON `Tasks`;
DROP INDEX `fk_Users` ON `Users`;

ALTER TABLE `Attachements`DROP PRIMARY KEY;
ALTER TABLE `Comments`DROP PRIMARY KEY;
ALTER TABLE `Milestones`DROP PRIMARY KEY;
ALTER TABLE `Notifications`DROP PRIMARY KEY;
ALTER TABLE `Projects`DROP PRIMARY KEY;
ALTER TABLE `Roles`DROP PRIMARY KEY;
ALTER TABLE `SearchCriteria`DROP PRIMARY KEY;
ALTER TABLE `TaskDependencies`DROP PRIMARY KEY;
ALTER TABLE `Tasks`DROP PRIMARY KEY;
ALTER TABLE `Users`DROP PRIMARY KEY;
ALTER TABLE `ProjectManagers`DROP PRIMARY KEY;
ALTER TABLE `TaskAssignees`DROP PRIMARY KEY;

DROP TABLE `Attachements`;
DROP TABLE `Comments`;
DROP TABLE `Milestones`;
DROP TABLE `Notifications`;
DROP TABLE `Projects`;
DROP TABLE `Roles`;
DROP TABLE `SearchCriteria`;
DROP TABLE `TaskDependencies`;
DROP TABLE `Tasks`;
DROP TABLE `Users`;
DROP TABLE `ProjectManagers`;
DROP TABLE `TaskAssignees`;

CREATE TABLE `Attachements` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`project_id` int(11) NOT NULL,
`task_id` int(11) NULL DEFAULT NULL,
`filename` varchar(255) NOT NULL,
`filepath` varchar(255) NOT NULL,
`upload_user_id` int(11) NOT NULL,
`type` varchar(255) NULL DEFAULT NULL,
PRIMARY KEY (`id`) ,
INDEX `fk_Attachements` (`project_id`),
INDEX `fk_Attachements_1` (`task_id`),
INDEX `fk_Attachements_2` (`upload_user_id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `Comments` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`engagement_id` int(11) NOT NULL,
`engagement_table` varchar(255) NOT NULL,
`user_id` int(11) NOT NULL,
`comment` text NULL,
`datetime` datetime NOT NULL,
PRIMARY KEY (`id`) ,
INDEX `fk_Comments_1` (`user_id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `Milestones` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11) NULL DEFAULT NULL,
`project_id` int(11) NOT NULL,
`manager_id` int(11) NULL DEFAULT NULL,
`name` varchar(255) NOT NULL,
`status` varchar(255) NOT NULL,
`estimate_budget` bigint(255) NULL DEFAULT NULL,
`estimate_time` bigint(20) NULL DEFAULT NULL,
`real_budget` bigint(255) NULL DEFAULT NULL,
`real_time` bigint(20) NULL DEFAULT NULL,
`description` text NULL,
`create_date` datetime NULL DEFAULT NULL,
PRIMARY KEY (`id`) ,
INDEX `fk_Milestones` (`project_id`),
INDEX `user_id` (`user_id`),
INDEX `manager_id` (`manager_id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `Notifications` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL,
`message` varchar(255) NOT NULL,
`create_date` datetime NOT NULL,
`type` varchar(255) NOT NULL,
PRIMARY KEY (`id`) ,
INDEX `fk_Messages` (`user_id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `Projects` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`projectManager` int(11) NOT NULL,
`create_date` datetime NOT NULL,
`status` varchar(255) NOT NULL,
`estimate_budget` bigint(255) NULL DEFAULT NULL,
`estimate_time` bigint(20) NULL DEFAULT NULL,
`real_budget` bigint(255) NULL DEFAULT NULL,
`real_time` bigint(20) NULL DEFAULT NULL,
`projectTitle` varchar(255) NOT NULL,
`description` text NULL,
`close_date` datetime NULL DEFAULT NULL,
`date_start` datetime NULL DEFAULT NULL,
`date_expected_finish` datetime NULL DEFAULT NULL,
`creator_id` int NULL,
PRIMARY KEY (`id`) ,
INDEX `fk_Projects` (`projectManager`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `Roles` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`role_name` varchar(255) NOT NULL,
`priv_bit_mask` varchar(255) NULL DEFAULT NULL,
PRIMARY KEY (`id`) 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `SearchCriteria` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`item_type` varchar(255) NULL DEFAULT NULL,
`value` varchar(255) NULL DEFAULT NULL,
PRIMARY KEY (`id`) 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `TaskDependencies` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`task_id` int(11) NOT NULL,
`dependent_task_id` int(11) NOT NULL,
PRIMARY KEY (`id`) ,
INDEX `fk_TaskDependencies` (`task_id`),
INDEX `fk_TaskDependencies_1` (`dependent_task_id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `Tasks` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`project_id` int(11) NOT NULL,
`milestone_id` int(11) NULL DEFAULT NULL,
`parent_task_id` int(11) NULL DEFAULT NULL,
`taskTitle` varchar(255) NOT NULL,
`assignee` int(11) NULL DEFAULT NULL,
`priority` varchar(255) NOT NULL,
`status` varchar(255) NOT NULL,
`estimate_budget` bigint(255) NULL DEFAULT NULL,
`estimate_time` bigint(20) NULL DEFAULT NULL,
`real_budget` bigint(255) NULL DEFAULT NULL,
`real_time` bigint(20) NULL DEFAULT NULL,
`due_date` date NULL DEFAULT NULL,
`flags` varchar(5000) NULL,
`description` text NULL,
`create_date` datetime NOT NULL,
PRIMARY KEY (`id`) ,
INDEX `fk_Tasks` (`project_id`),
INDEX `fk_Tasks_1` (`milestone_id`),
INDEX `fk_Tasks_2` (`parent_task_id`),
INDEX `fk_Tasks_3` (`assignee`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `Users` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`username` varchar(255) NOT NULL,
`displayname` varchar(255) NOT NULL,
`password` varchar(255) NOT NULL,
`role_id` int(11) NOT NULL,
PRIMARY KEY (`id`) ,
INDEX `fk_Users` (`role_id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `ProjectManagers` (
`id` int(11) NULL AUTO_INCREMENT,
`project_id` int(11) NULL,
`user_id` int(11) NULL,
PRIMARY KEY (`id`) 
);

CREATE TABLE `TaskAssignees` (
`id` int(11) NULL AUTO_INCREMENT,
`task_id` int(11) NULL,
`user_id` int(11) NULL,
PRIMARY KEY (`id`) 
);


ALTER TABLE `Attachements` ADD CONSTRAINT `fk_Attachements` FOREIGN KEY (`project_id`) REFERENCES `Projects` (`id`);
ALTER TABLE `Attachements` ADD CONSTRAINT `fk_Attachements_1` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`);
ALTER TABLE `Attachements` ADD CONSTRAINT `fk_Attachements_2` FOREIGN KEY (`upload_user_id`) REFERENCES `Users` (`id`);
ALTER TABLE `Comments` ADD CONSTRAINT `fk_Comments_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`);
ALTER TABLE `Milestones` ADD CONSTRAINT `fk_Milestones` FOREIGN KEY (`project_id`) REFERENCES `Projects` (`id`);
ALTER TABLE `Milestones` ADD CONSTRAINT `fk_Milestones_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`);
ALTER TABLE `Milestones` ADD CONSTRAINT `fk_Milestones_2` FOREIGN KEY (`manager_id`) REFERENCES `Users` (`id`);
ALTER TABLE `Notifications` ADD CONSTRAINT `fk_Messages` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`);
ALTER TABLE `Projects` ADD CONSTRAINT `fk_Projects` FOREIGN KEY (`projectManager`) REFERENCES `Users` (`id`);
ALTER TABLE `TaskDependencies` ADD CONSTRAINT `fk_TaskDependencies` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`);
ALTER TABLE `TaskDependencies` ADD CONSTRAINT `fk_TaskDependencies_1` FOREIGN KEY (`dependent_task_id`) REFERENCES `Tasks` (`id`);
ALTER TABLE `Tasks` ADD CONSTRAINT `fk_Tasks` FOREIGN KEY (`project_id`) REFERENCES `Projects` (`id`);
ALTER TABLE `Tasks` ADD CONSTRAINT `fk_Tasks_1` FOREIGN KEY (`milestone_id`) REFERENCES `Milestones` (`id`);
ALTER TABLE `Tasks` ADD CONSTRAINT `fk_Tasks_2` FOREIGN KEY (`parent_task_id`) REFERENCES `Tasks` (`id`);
ALTER TABLE `Tasks` ADD CONSTRAINT `fk_Tasks_3` FOREIGN KEY (`assignee`) REFERENCES `Users` (`id`);
ALTER TABLE `Users` ADD CONSTRAINT `fk_Users` FOREIGN KEY (`role_id`) REFERENCES `Roles` (`role_name`);
ALTER TABLE `ProjectManagers` ADD CONSTRAINT `fk_ProjectManagers` FOREIGN KEY (`project_id`) REFERENCES `Projects` (`id`);
ALTER TABLE `ProjectManagers` ADD CONSTRAINT `fk_ProjectManagers_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`);
ALTER TABLE `TaskAssignees` ADD CONSTRAINT `fk_TaskAssignees` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`);
ALTER TABLE `TaskAssignees` ADD CONSTRAINT `fk_TaskAssignees_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`);

