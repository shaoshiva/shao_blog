CREATE TABLE IF NOT EXISTS `shao_blog_post` (
  `post_id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `post_title` varchar(250) NOT NULL,
  `post_summary` text NOT NULL,
  `post_author_alias` varchar(255) DEFAULT NULL,
  `post_author_id` int(10) unsigned DEFAULT NULL,
  `post_created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `post_context` varchar(25) NOT NULL,
  `post_context_common_id` int(11) NOT NULL,
  `post_context_is_main` tinyint(1) NOT NULL DEFAULT '0',
  `post_published` tinyint(1) NOT NULL,
  `post_publication_start` datetime DEFAULT NULL,
  `post_publication_end` datetime DEFAULT NULL,
  `post_read` int(10) unsigned NOT NULL,
  `post_virtual_name` varchar(255) NOT NULL,
  PRIMARY KEY (`post_id`),
  KEY `post_lang` (`post_context`),
  KEY `post_lang_common_id` (`post_context_common_id`,`post_context_is_main`),
  KEY `post_lang_is_main` (`post_context_is_main`),
  KEY `post_virtual_name` (`post_virtual_name`),
  KEY `post_author_id` (`post_author_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

CREATE TABLE IF NOT EXISTS `shao_blog_category` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_title` varchar(255) NOT NULL,
  `cat_virtual_name` varchar(255) NOT NULL,
  `cat_context` varchar(25) NOT NULL,
  `cat_context_common_id` int(11) NOT NULL,
  `cat_context_is_main` tinyint(1) NOT NULL DEFAULT '0',
  `cat_parent_id` int(11) DEFAULT NULL,
  `cat_sort` float DEFAULT NULL,
  `cat_created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cat_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`cat_id`),
  KEY `cat_lang` (`cat_context`),
  KEY `cat_lang_common_id` (`cat_context_common_id`,`cat_context_is_main`),
  KEY `cat_lang_is_main` (`cat_context_is_main`),
  KEY `cat_virtual_name` (`cat_virtual_name`),
  KEY `cat_parent_id` (`cat_parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

CREATE TABLE IF NOT EXISTS `shao_blog_category_post` (
  `post_id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  PRIMARY KEY (`post_id`,`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `shao_blog_tag` (
  `tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag_label` varchar(255) NOT NULL,
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `tag_label` (`tag_label`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

CREATE TABLE IF NOT EXISTS `shao_blog_tag_post` (
  `post_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`post_id`,`tag_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
