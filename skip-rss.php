<?php
/*
Plugin Name: Skip RSS
Plugin URI: http://www.blogdemy.com/skip-rss-feed-disable-rss-feed-wordpress-plugin/
Description: Skip post from appearing in rss feed 
Version: 0.6
Author: Parasmani
Author URI: http://www.blogdemy.com
*/


add_filter('posts_where', 'skip_post_from_rss');
add_action('save_post', 'skip_rss_post_action');
add_action('do_meta_boxes', 'skip_rss_add_meta_box', 10, 2);


	function skip_rss_add_meta_box( $page, $context )
	{
		add_meta_box('skip-rss',__('Skip RSS', 'skip-rss'), 'skip_rss_meta_box', 'post');
	}
	
	function skip_rss_meta_box()
	{
		echo '<p>';
		wp_nonce_field('fd_no', 'skip_rss_nonce', false, true );
		echo '</p>';
		global $post;
		$skip_rss_flag = false;
		$skip_rss_meta = get_post_meta($post->ID,'skip_rss_flag',true);
		if ($skip_rss_meta == "true") {
			$skip_rss_flag = true;
		}
?>
		<input type="checkbox" value="true" name="skip_rss_flag" <?php if ($skip_rss_flag) { echo 'checked="checked"'; } ?>/> 
<?php 
		echo 'Skip this post from RSS feed publishing.';
		
	}


	function skip_rss_post_action($id) 
	{
		if(wp_verify_nonce($_REQUEST['skip_rss_nonce'], 'fd_no')) 
		{
			if(isset($_POST["skip_rss_flag"]))
			{
				$skip_rss_flag = $_POST["skip_rss_flag"];			  
				if($skip_rss_flag == 'true') 
					update_post_meta($id, 'skip_rss_flag', $skip_rss_flag);
			}
			else
				delete_post_meta($id, 'skip_rss_flag');
		}
		return $id;
	}

	function skip_post_from_rss($where)
	{
		if (!is_feed()) 
			return $where;

		global $wpdb;
			
		$where .= " AND $wpdb->posts.ID NOT IN ( 
							SELECT distinct(post_id) from $wpdb->postmeta 
							where $wpdb->postmeta.meta_key = 'skip_rss_flag' 
							and $wpdb->postmeta.meta_value = 'true') ";
		
		return $where;
	}

?>
