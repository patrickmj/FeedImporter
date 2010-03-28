<?php
define('FEED_IMPORTER_PLUGIN_DIR', dirname(__FILE__));


add_plugin_hook('install', 'feed_importer_install');
add_plugin_hook('uninstall', 'feed_importer_uninstall');
add_plugin_hook('define_routes', 'feed_importer_define_routes');
add_plugin_hook('admin_theme_header', 'feed_importer_admin_theme_header');

/*

add_plugin_hook('config', 'feed_importer_config');
add_plugin_hook('config_form', 'feed_importer_config_form');

add_plugin_hook('public_theme_header', 'feed_importer_public_theme_header');
add_plugin_hook('define_acl', 'feed_importer_define_acl');
add_plugin_hook('public_append_to_items_show', 'feed_importer_append_to_items_show');
*/


add_filter('admin_navigation_main', 'feed_importer_admin_navigation_main' );





function feed_importer_install()
{
	$db = get_db();
	
	//Add Feeds table
	$sql = "CREATE TABLE IF NOT EXISTS `{$db->prefix}feed_importer_feeds` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `collection_id` int(10) unsigned NOT NULL,
  `feed_url` text COLLATE utf8_unicode_ci,
  `feed_title` text COLLATE utf8_unicode_ci,
  `feed_description` text COLLATE utf8_unicode_ci,  		
  `import_start_time` datetime DEFAULT NULL,
  `import_end_time` datetime DEFAULT NULL,
  `feed_display_name` text COLLATE utf8_unicode_ci,
  `feed_display_description` text COLLATE utf8_unicode_ci,
  `item_type_id` int(10) unsigned DEFAULT NULL,
  `import_media` tinyint(1) DEFAULT '0',
  `trim_length` int(10) unsigned DEFAULT '300',
  `update_frequency` text COLLATE utf8_unicode_ci,
  `import_content` tinyint(1) DEFAULT NULL,
  `content_as_description` tinyint(1) DEFAULT NULL,
  `tags_as_subjects` tinyint(1) DEFAULT NULL,
  `tags_as_tags` tinyint(1) DEFAULT NULL,
  `tags_linkback` tinyint(1) DEFAULT NULL,
  `author_as_creator` tinyint(1) DEFAULT NULL,
  `map_authors` tinyint(1) DEFAULT NULL,
  `authors_to_users_map` text COLLATE utf8_unicode_ci,
  `map_tags` tinyint(1) DEFAULT NULL,
  `tags_map` text COLLATE utf8_unicode_ci,
  `items_linkback` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;"; 		
	
	$db->exec($sql);		


	$sql ="CREATE TABLE IF NOT EXISTS `{$db->prefix}feed_importer_imports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created`  datetime DEFAULT NULL,		
  `feed_id` int(10) unsigned NOT NULL,
  `status` text COLLATE utf8_unicode_ci,
  `sp_error` text COLLATE utf8_unicode_ci,
  `collection_id` int(10) unsigned ,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";
	$db->exec($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `{$db->prefix}feed_importer_imported_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `import_id` int(10) unsigned NOT NULL,
  `permalink` text COLLATE utf8_unicode_ci ,
  `sp_id` int(10) unsigned ,
  `item_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";

	$db->exec($sql);
//TODO: how to check whether it exists or not?

	try {
		insert_item_type(array('name'=>'Web Page', 'description'=>'A single web page'));	
	} catch(Exception $e) {
		//handle the case where a 'Web Page' has already been created
	}
	
//lookup the elementInfos I want to pass in their IDs
// see globals.php line 455 insert_item_type


}


function feed_importer_uninstall()
{
	$db = get_db();
	$sql = "DROP TABLE IF EXISTS `{$db->prefix}feed_importer_feeds`;";
	$db->exec($sql);	 
	$sql = "DROP TABLE IF EXISTS `{$db->prefix}feed_importer_imports`";
	$db->exec($sql);

	$sql = "DROP TABLE IF EXISTS `{$db->prefix}feed_importer_imported_items`";
	$db->exec($sql);	
}




function feed_importer_admin_navigation_main($tabs)
{
	$tabs['Feeds'] = uri("feeds");
	return $tabs;
	
}

function feed_importer_define_routes($router)
{
	$router->addRoute(
		'feed_importer_browse',
		new Zend_Controller_Router_Route(
			'feeds', 
			array(
				'module'       => 'feed-importer', 
				'controller'   => 'feeds', 
				'action'       => 'browse' 
			)
		)
	);


	$router->addRoute(
	    'feed_importer_action',
	    new Zend_Controller_Router_Route(
	        'feeds/:action/:id', 
	        array(
	            'module'       => 'feed-importer', 
	            'controller'   => 'feeds',  
	            'id'           => '/d+'
	        )
	    )
	);	
	
	
}
function feed_importer_admin_theme_header($request) 
{
	echo '<link href="' . WEB_PLUGIN . '/FeedImporter/views/common/css/feed-importer.css" media="screen" rel="stylesheet" />';
}

