# Main DB on HUB
CREATE USER 'twitter'@'localhost' IDENTIFIED BY  '*-H4^1b4*$P9|[h';
CREATE DATABASE IF NOT EXISTS  `twitter` CHARACTER SET utf8 COLLATE utf8_general_ci;
GRANT SELECT, INSERT, DELETE, LOCK TABLES ON `twitter` TO  'twitter'@'localhost' IDENTIFIED BY  '*-H4^1b4*$P9|[h' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;

# Queue table
CREATE TABLE IF NOT EXISTS `queue` (
	`user_id` bigint NOT NULL COMMENT 'Twitter User ID - max size 64 bits',
	PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# User table
CREATE TABLE IF NOT EXISTS `users` (
	`user_id` bigint NOT NULL COMMENT 'Twitter User ID',
	`screen_name` varchar(15) NOT NULL,
	`description` varchar(140) NOT NULL,
	`creation_date` datetime NOT NULL COMMENT 'Date the account was made in UTC',
	`location` varchar(30),
	`total_followers` bigint NOT NULL DEFAULT 0,
	`total_friends` bigint NOT NULL DEFAULT 0,
	PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Tweets table
CREATE TABLE IF NOT EXISTS `tweets` (
	`tweet_id` bigint NOT NULL COMMENT 'Twitter Tweet ID',
	`user_id` bigint NOT NULL COMMENT 'Twitter User ID',
	`tweet` varchar(140) NOT NULL,
	`creation_date` datetime NOT NULL COMMENT 'Date the tweet was made in UTC',
	`source` varchar(50) NOT NULL,
	`retweeted` tinyint NOT NULL COMMENT 'Zero false, non-zero true',
	`retweet_count` int NOT NULL,
	PRIMARY KEY (`tweet_id`),
	FOREIGN KEY (`user_id`) REFERENCES users(user_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;