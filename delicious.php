<?php

/*
Plugin Name: del.icio.us for Wordpress
Version: 1.2
Plugin URI: http://rick.jinlabs.com/code/delicious
Description: Displays your recently listened links. Based on <a href="http://cavemonkey50.com/code/pownce/">Pownce for Wordpress</a> by <a href="http://cavemonkey50.com/">Cavemonkey50</a>. 
Author: Ricardo Gonz&aacute;lez
Author URI: http://rick.jinlabs.com/
*/

define('MAGPIE_CACHE_AGE', 120);

// Display del.icio.us recently bookmarked links.

function delicious_bookmarks($username = '', $num = 5, $list = true, $update = true, $tags = false, $filtertag = '' ) {
	include_once(ABSPATH . WPINC . '/rss.php');
	
	$rss = 'http://del.icio.us/rss/'.$username;
	
	if($filtertag != '') { $rss .= '/'.$filtertag; }
	
	$bookmarks = fetch_rss($rss);

	if ($list) echo '<ul class="delicious">';
	
	if ($username == '') {
		if ($list) echo '<li>';
		echo 'Username not configured';
		if ($list) echo '</li>';
	} else {
			if ( empty($bookmarks->items) ) {
				if ($list) echo '<li>';
				echo 'No recently listened links.';
				if ($list) echo '</li>';
			} else {
				foreach ( $bookmarks->items as $bookmark ) {
					$msg = $bookmark['title'];
					$updated = delicious_relative($bookmark['dc']['date']);
					$link = $bookmark['link'];
				
					if ($list) echo '<li class="delicious-item">'; elseif ($num != 1) echo '<p class="delicious">';
            		echo '<a href="'.$link.'" class="delicious-link">'.$msg.'</a>'; // Puts a link to the link.
          
					if ($update) echo ' <span class="delicious-timestamp">' . $updated . '</span>';
					
					if ($tags) {
						echo '<br />';
						echo '<div class="delicious-tags">';
						$tagged = explode(' ', $bookmark['dc']['subject']);
						foreach ($tagged as $tag) {
		            		echo '<a href="http://del.icio.us/tag/'.$tag.'" class="delicious-link-tag">'.$tag.'</a> '; // Puts a link to the tag.
							}
						echo '</div>';
						}
						
					if ($list) echo '</li>'; elseif ($num != 1) echo '</p>';
				
					$i++;
					if ( $i >= $num ) break;
				}
			}
			
			if ($list) echo '</ul>';
		}
	}
// Present the date nicer

function delicious_relative($time) {
	$time = explode('T', substr($time, 0, -1));
	$date = explode('-', $time[0]);
	$time = explode(':', $time[1]);
	$time_orig = @gmmktime($time[0]+$offset, $time[1], $time[2], $date[1], $date[2], $date[0]);
	
	$diff = $just = time()-$time_orig;
    $months = floor($diff/2592000);
    $diff -= $months*2419200;
    $weeks = floor($diff/604800);
    $diff -= $weeks*604800;
    $days = floor($diff/86400);
    $diff -= $days*86400;
    $hours = floor($diff/3600);
    $diff -= $hours*3600;
    $minutes = floor($diff/60);
    $diff -= $minutes*60;
    $seconds = $diff;
    
	if ($just<=0) {
		return 'Just Now!';	
	} else {
	    if ($months>0) {
	        // over a month old, just show date (yyyy/mm/dd format)
	        return 'on '.date('Y/m/d', $time_orig);
	    } else {
	        if ($weeks>0) {
	            // weeks and days
	            $relative_date .= ($relative_date?', ':'').$weeks.' '.__('week').($weeks>1?'s':'');
	            $relative_date .= $days>0?($relative_date?', ':'').$days.' '.__('day').($days>1?'s':''):'';
	        } elseif ($days>0) {
	            // days and hours
	            $relative_date .= ($relative_date?', ':'').$days.' '.__('day').($days>1?'s':'');
	            $relative_date .= $hours>0?($relative_date?', ':'').$hours.' '.__('hour').($hours>1?'s':''):'';
	        } elseif ($hours>0) {
	            // hours and minutes
	            $relative_date .= ($relative_date?', ':'').$hours.' '.__('hour').($hours>1?'s':'');
	            $relative_date .= $minutes>0?($relative_date?', ':'').$minutes.' '.__('minute').($minutes>1?'s':''):'';
	        } elseif ($minutes>0) {
	            // minutes only
	            $relative_date .= ($relative_date?', ':'').$minutes.' '.__('minute').($minutes>1?'s':'');
	        } else {
	            // seconds only
	            $relative_date .= ($relative_date?', ':'').$seconds.' '.__('second').($seconds>1?'s':'');
	        }
	    }
	}
    // show relative date and add proper verbiage
    return $relative_date.' ago';
}

// delicious widget stuff
function widget_delicious_init() {

	if ( !function_exists('register_sidebar_widget') )
		return;

	function widget_delicious($args) {
		
		// $args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys. Default tags: li and h2.
		extract($args);

		// Each widget can store its own options. We keep strings here.
		include_once(ABSPATH . WPINC . '/rss.php');
		$options = get_option('widget_delicious');
		$title = $options['title'];
		$username = $options['username'];
		$num = $options['num'];
		$update = ($options['update']) ? true : false;
		$bookmarks = fetch_rss('http://del.icio.us/rss/'.$username);

		// These lines generate our output. Widgets can be very complex
		// but as you can see here, they can also be very, very simple.
		echo $before_widget . $before_title . $title . $after_title;
		delicious_bookmarks($username, $num, true, $update);
		echo $after_widget;
	}

	// This is the function that outputs the form to let the users edit
	// the widget's title. It's an optional feature that users cry for.
	function widget_delicious_control() {

		// Get our options and see if we're handling a form submission.
		$options = get_option('widget_delicious');
		if ( !is_array($options) )
			$options = array('title'=>'', 'username'=>'', 'num'=>'5', 'update'=>true, 'linked'=>true);
		if ( $_POST['delicious-submit'] ) {

			// Remember to sanitize and format use input appropriately.
			$options['title'] = strip_tags(stripslashes($_POST['delicious_title']));
			$options['username'] = strip_tags(stripslashes($_POST['delicious_username']));
			$options['num'] = strip_tags(stripslashes($_POST['delicious_num']));
			$options['update'] = isset($_POST['delicious_update']);
			update_option('widget_delicious', $options);
		}

		// Be sure you format your options to be valid HTML attributes.
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$username = htmlspecialchars($options['username'], ENT_QUOTES);
		$num = htmlspecialchars($options['num'], ENT_QUOTES);
		$update_checked = ($options['update']) ? 'checked="checked"' : '';

		
		// Here is our little form segment. Notice that we don't need a
		// complete form. This will be embedded into the existing form.
		echo '<p style="text-align:right;"><label for="delicious_title">' . ___('Title:') . ' <input style="width: 200px;" id="delicious_title" name="delicious_title" type="text" value="'.$title.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="delicious_username">' . ___('Username:') . ' <input style="width: 200px;" id="delicious_username" name="delicious_username" type="text" value="'.$username.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="delicious_num">' . ___('Number of links:') . ' <input style="width: 25px;" id="delicious_num" name="delicious_num" type="text" value="'.$num.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="delicious_update">' . ___('Show timestamps:') . ' <input id="delicious_update" name="delicious_update" type="checkbox"'.$update_checked.' /></label></p>';
		echo '<input type="hidden" id="delicious-submit" name="delicious-submit" value="1" />';
	}
	
	// This registers our widget so it appears with the other available
	// widgets and can be dragged and dropped into any active sidebars.
	register_sidebar_widget(array('del.icio.us', 'widgets'), 'widget_delicious');

	// This registers our optional widget control form. Because of this
	// our widget will have a button that reveals a 300x100 pixel form.
	register_widget_control(array('del.icio.us', 'widgets'), 'widget_delicious_control', 300, 180);
}

// Run our code later in case this loads prior to any required plugins.
add_action('widgets_init', 'widget_delicious_init');

?>