ALTER TABLE `b_sheepla_carriers` ADD `comment` TEXT NOT NULL AFTER `cost`;
ALTER TABLE `b_sheepla_carriers` ADD COLUMN `sort` INT(11) NOT NULL DEFAULT '100'  AFTER `deleted` ;
