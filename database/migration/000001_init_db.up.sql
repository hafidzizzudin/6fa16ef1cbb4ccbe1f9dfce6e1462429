CREATE TABLE IF NOT EXISTS `email` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` VARCHAR(255) NOT NULL DEFAULT '',
    `is_html` BOOLEAN NOT NULL DEFAULT 0,
    `email_to` VARCHAR(255) NOT NULL DEFAULT '',
    `body` LONGTEXT,
    PRIMARY KEY `pk_id`(`id`)
) ENGINE = InnoDB;

ALTER TABLE
    `email`
ADD
    INDEX `IDX_user_id`(`user_id`);

ALTER TABLE
    `email`
ADD
    INDEX `IDX_email_to`(`email_to`);