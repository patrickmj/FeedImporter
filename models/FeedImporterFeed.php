<?php




class FeedImporterFeed extends Omeka_Record
{
	
	public $id;
	public $collection_id;
	public $feed_url;
	public $feed_title;
	public $feed_description;
	public $last_import_time;
	public $import_start_time;
	public $import_end_time;
	public $feed_display_name;
	public $feed_display_description;
	public $item_type_id;
	public $import_media;
	public $process_id;
	public $trim_length;
	public $update_frequency;
	public $import_content;
	public $content_as_description;
	public $tags_as_subjects;
	public $tags_as_tags;
	public $tags_linkback;
	public $author_as_creator;
	public $map_authors;
	public $authors_to_users_map;
	public $map_tags;
	public $tags_map;
	public $items_linkback;
	
}
?>
