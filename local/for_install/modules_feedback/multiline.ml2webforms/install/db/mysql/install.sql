CREATE TABLE IF NOT EXISTS `ml2webforms_feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL,
  `name` VARCHAR(500) NOT NULL,
  `email` VARCHAR(500) NOT NULL,
  `phone` VARCHAR(500) NOT NULL,
  `attachment` text NOT NULL,
  `comment` int(11) NOT NULL,
  `agree` text NOT NULL,
  `hobby` VARCHAR(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;