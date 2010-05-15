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
  `collection_id` int(10) unsigned NULL,
  `task_id` int(10) unsigned NULL,		
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
  PRIMARY KEY (`id`)  
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";
	$db->exec($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `{$db->prefix}feed_importer_imported_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `feed_id` int(10) unsigned NOT NULL, 		
  `import_id` int(10) unsigned NOT NULL,
  `permalink` text COLLATE utf8_unicode_ci ,
  `sp_id` text COLLATE utf8_unicode_ci,
  `item_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)  
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";

	$db->exec($sql);


	$sql = "CREATE TABLE IF NOT EXISTS `{$db->prefix}feed_importer_tag_configs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `original_name` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `pref_name` tinytext COLLATE utf8_unicode_ci NULL,
  `scheme` tinytext COLLATE utf8_unicode_ci NULL,
  `collection_id` int(10) unsigned NULL,
  `collection_id_priority` int(10) unsigned NULL,		  		
  `item_type_id` int(10) unsigned NULL,
  `item_type_id_priority` int(10) unsigned NULL,  
  `feed_id` int(10) unsigned NULL,
  `created` date NOT NULL,
   `skip` int(1) unsigned NULL,
  `count` int(10) unsigned NULL,
  `elements_map` mediumtext COLLATE utf8_unicode_ci NULL,
  `tags_map` mediumtext COLLATE utf8_unicode_ci NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;";


	$db->exec($sql);

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
	
	$sql = "DROP TABLE IF EXISTS `{$db->prefix}feed_importer_tag_configs`";
	$db->exec($sql);
	
	//Remove any FakeCron tasks associated with FeedImporter
	$fakeCronTasks = $db->getTable('FakeCron_Task')->findBy(array('plugin_class'=>"FeedImporter_FakeCronTask") );

	foreach($fakeCronTasks as $fakeCronTask){
		$fakeCronTask->delete();
	}
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
	$router->addRoute(
	    'feed_importer_import_action',
	    new Zend_Controller_Router_Route(
	        'feeds/imports/:action/:id', 
	        array(
	            'module'       => 'feed-importer', 
	            'controller'   => 'imports',  
	            'id'           => '/d+'
	        )
	    )
	);		
	$router->addRoute(
	    'feed_importer_tag_config_action',
	    new Zend_Controller_Router_Route(
	        'feeds/:feed_id/tags/:action', 
	        array(
	            'module'       => 'feed-importer', 
	            'controller'   => 'tag-configs',  
	        )
	    )
	);


	$router->addRoute(
	    'feed_importer_tag_config_action_pages',
	    new Zend_Controller_Router_Route(
	        'feeds/:feed_id/tags/browse/page/:page', 
	        array(
	            'module'       => 'feed-importer', 
	            'controller'   => 'tag-configs',
	            'page'		   => '/d+'
	        )
	    )
	);	
	
			
}
function feed_importer_admin_theme_header($request) 
{
	
	if($request->getModuleName() == 'feed-importer') {
		echo '<script type="text/javascript" src="' . WEB_PLUGIN . '/FeedImporter/views/common/js/feed-importer.js" ></script>';
		switch($request->getControllerName() ) {			
			case 'tag-configs':
				echo '<link href="' . WEB_PLUGIN . '/FeedImporter/views/common/css/tag-configs.css" media="screen" rel="stylesheet" />';				
			break;
			
			case 'feeds':
				echo '<link href="' . WEB_PLUGIN . '/FeedImporter/views/common/css/feed-importer.css" media="screen" rel="stylesheet" />';
			break;			
		}
	}		
}

