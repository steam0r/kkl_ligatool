# Export von Tabelle awards
# ------------------------------------------------------------

DROP TABLE IF EXISTS `awards`;

CREATE TABLE `awards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle club_has_awards
# ------------------------------------------------------------

DROP TABLE IF EXISTS `club_has_awards`;

CREATE TABLE `club_has_awards` (
  `club_id` int(11) NOT NULL,
  `award_id` int(11) NOT NULL,
  KEY `FKA1C8BE244FA40E35` (`club_id`),
  KEY `FKA1C8BE249CAFF229` (`award_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle club_properties
# ------------------------------------------------------------

DROP TABLE IF EXISTS `club_properties`;

CREATE TABLE `club_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `property_key` varchar(255) DEFAULT NULL,
  `objectId` int(11) NOT NULL,
  `text` longtext,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle clubs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `clubs`;

CREATE TABLE `clubs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `country_code` varchar(255) DEFAULT NULL,
  `description` longtext,
  `logo` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `short_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle cms_blocks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cms_blocks`;

CREATE TABLE `cms_blocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `content` longtext,
  `content_key` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_key` (`content_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle game_days
# ------------------------------------------------------------

DROP TABLE IF EXISTS `game_days`;

CREATE TABLE `game_days` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `fixture` datetime DEFAULT NULL,
  `number` int(11) NOT NULL,
  `season_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK3BA504847AC3A055` (`season_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle games
# ------------------------------------------------------------

DROP TABLE IF EXISTS `games`;

CREATE TABLE `games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `goals_away` int(11) DEFAULT NULL,
  `goals_home` int(11) DEFAULT NULL,
  `number` int(11) NOT NULL,
  `set_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK5D932C1CAE7AADF` (`set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle league_properties
# ------------------------------------------------------------

DROP TABLE IF EXISTS `league_properties`;

CREATE TABLE `league_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `property_key` varchar(255) DEFAULT NULL,
  `objectId` int(11) NOT NULL,
  `text` longtext,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle leagues
# ------------------------------------------------------------

DROP TABLE IF EXISTS `leagues`;

CREATE TABLE `leagues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `active` bit(1) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `current_season` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `FK301F424B7D3E8C7` (`current_season`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle locations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `locations`;

CREATE TABLE `locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `description` text,
  `lat` varchar(256) NOT NULL,
  `lng` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle match_properties
# ------------------------------------------------------------

DROP TABLE IF EXISTS `match_properties`;

CREATE TABLE `match_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `property_key` varchar(255) DEFAULT NULL,
  `objectId` int(11) NOT NULL,
  `text` longtext,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle matches
# ------------------------------------------------------------

DROP TABLE IF EXISTS `matches`;

CREATE TABLE `matches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `score_away` int(11) DEFAULT NULL,
  `fixture` datetime DEFAULT NULL,
  `score_home` int(11) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `notes` longtext,
  `status` int(11) DEFAULT NULL,
  `away_team` int(11) DEFAULT NULL,
  `game_day_id` int(11) DEFAULT NULL,
  `home_team` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK321E8933727A5FA6` (`away_team`),
  KEY `FK321E893323C909BA` (`game_day_id`),
  KEY `FK321E893399F9DC55` (`home_team`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle messages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `messages`;

CREATE TABLE `messages` (
  `message_type` varchar(31) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `authorId` int(11) NOT NULL,
  `object_id` int(11) DEFAULT NULL,
  `recipientId` int(11) NOT NULL,
  `text` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle player_properties
# ------------------------------------------------------------

DROP TABLE IF EXISTS `player_properties`;

CREATE TABLE `player_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `property_key` varchar(255) DEFAULT NULL,
  `objectId` int(11) NOT NULL,
  `text` longtext,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle player_stats
# ------------------------------------------------------------

DROP TABLE IF EXISTS `player_stats`;

CREATE TABLE `player_stats` (
  `stat_name` varchar(31) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `match_value` int(11) DEFAULT NULL,
  `match_id` int(11) DEFAULT NULL,
  `team_player_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK56455C1141A6370` (`team_player_id`),
  KEY `FK56455C16CAA06BF` (`match_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle players
# ------------------------------------------------------------

DROP TABLE IF EXISTS `players`;

CREATE TABLE `players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `birthdate` datetime DEFAULT NULL,
  `country_code` varchar(255) DEFAULT NULL,
  `description` longtext,
  `draws` int(11) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `losses` int(11) NOT NULL,
  `nick_name` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `wins` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle season_properties
# ------------------------------------------------------------

DROP TABLE IF EXISTS `season_properties`;

CREATE TABLE `season_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `property_key` varchar(255) DEFAULT NULL,
  `objectId` int(11) NOT NULL,
  `text` longtext,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle seasons
# ------------------------------------------------------------

DROP TABLE IF EXISTS `seasons`;

CREATE TABLE `seasons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `active` bit(1) NOT NULL,
  `hide_in_overview` bit(1) DEFAULT 0,
  `end_date` datetime DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `current_game_day` int(11) DEFAULT NULL,
  `league_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK7552F1F0A5B99604` (`current_game_day`),
  KEY `FK7552F1F04CC8CCD5` (`league_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle set_has_away_players
# ------------------------------------------------------------

DROP TABLE IF EXISTS `set_has_away_players`;

CREATE TABLE `set_has_away_players` (
  `set_id` int(11) NOT NULL,
  `team_player_id` int(11) NOT NULL,
  KEY `FKFA59523CAE7AADF` (`set_id`),
  KEY `FKFA59523141A6370` (`team_player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle set_has_home_players
# ------------------------------------------------------------

DROP TABLE IF EXISTS `set_has_home_players`;

CREATE TABLE `set_has_home_players` (
  `set_id` int(11) NOT NULL,
  `team_player_id` int(11) NOT NULL,
  KEY `FK7C742614CAE7AADF` (`set_id`),
  KEY `FK7C742614141A6370` (`team_player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle sets
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sets`;

CREATE TABLE `sets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `score_away` int(11) DEFAULT NULL,
  `score_home` int(11) DEFAULT NULL,
  `number` int(11) NOT NULL,
  `match_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK35D0516CAA06BF` (`match_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle team_player_properties
# ------------------------------------------------------------

DROP TABLE IF EXISTS `team_player_properties`;

CREATE TABLE `team_player_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `property_key` varchar(255) DEFAULT NULL,
  `objectId` int(11) NOT NULL,
  `text` longtext,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle team_players
# ------------------------------------------------------------

DROP TABLE IF EXISTS `team_players`;

CREATE TABLE `team_players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `player_id` int(11) DEFAULT NULL,
  `season_id` int(11) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK5AD4FDD0C5EED8D5` (`team_id`),
  KEY `FK5AD4FDD07AC3A055` (`season_id`),
  KEY `FK5AD4FDD09A14B795` (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle team_properties
# ------------------------------------------------------------

DROP TABLE IF EXISTS `team_properties`;

CREATE TABLE `team_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `property_key` varchar(255) DEFAULT NULL,
  `objectId` int(11) NOT NULL,
  `text` longtext,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle team_scores
# ------------------------------------------------------------

DROP TABLE IF EXISTS `team_scores`;

CREATE TABLE `team_scores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `draw` int(11) NOT NULL,
  `gamesAgainst` int(11) NOT NULL,
  `gamesFor` int(11) NOT NULL,
  `goalsAgainst` int(11) NOT NULL,
  `goalsFor` int(11) NOT NULL,
  `loss` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `win` int(11) NOT NULL,
  `gameDay_id` int(11) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FKACBD5C437EB75B3F` (`gameDay_id`),
  KEY `FKACBD5C43C5EED8D5` (`team_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle teams
# ------------------------------------------------------------

DROP TABLE IF EXISTS `teams`;

CREATE TABLE `teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `country_code` varchar(255) DEFAULT NULL,
  `description` longtext,
  `logo` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `short_name` varchar(255) DEFAULT NULL,
  `club_id` int(11) DEFAULT NULL,
  `season_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK69209B64FA40E35` (`club_id`),
  KEY `FK69209B67AC3A055` (`season_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle user_properties
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_properties`;

CREATE TABLE `user_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `property_key` varchar(255) DEFAULT NULL,
  `objectId` int(11) NOT NULL,
  `text` longtext,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Export von Tabelle users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `birthdate` datetime DEFAULT NULL,
  `country_code` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `level` int(11) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `sex` varchar(255) DEFAULT NULL,
  `userImage` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
