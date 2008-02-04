<?php

/*
Plugin Name: del.icio.us for Wordpress
Version: 1.5
Plugin URI: http://rick.jinlabs.com/code/delicious
Description: Displays your recently listened links. Based on <a href="http://cavemonkey50.com/code/pownce/">Pownce for Wordpress</a> by <a href="http://cavemonkey50.com/">Cavemonkey50</a>. 
Author: Ricardo Gonz&aacute;lez
Author URI: http://rick.jinlabs.com/
*/

/*  Copyright 2007  Ricardo González Castro (rick[in]jinlabs.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


define('MAGPIE_CACHE_AGE', 120);

$delicious_options['widget_fields']['title'] = array('label'=>'Title:', 'type'=>'text', 'default'=>'');
$delicious_options['widget_fields']['username'] = array('label'=>'Username:', 'type'=>'text', 'default'=>'');
$delicious_options['widget_fields']['num'] = array('label'=>'Number of links:', 'type'=>'text', 'default'=>'');
$delicious_options['widget_fields']['update'] = array('label'=>'Show timestamps:', 'type'=>'checkbox', 'default'=>false);
$delicious_options['widget_fields']['tags'] = array('label'=>'Show tags:', 'type'=>'checkbox', 'default'=>false);
$delicious_options['widget_fields']['filtertag'] = array('label'=>'Filter Tag(s) [cats+dogs+birds]: ', 'type'=>'text', 'default'=>'');
$delicious_options['widget_fields']['displaydesc'] = array('label'=>'Show descriptions:', 'type'=>'checkbox', 'default'=>false);
$delicious_options['widget_fields']['nodisplaytag'] = array('label'=>'No display tag(s) [cats+dogs+birds]:', 'type'=>'text', 'default'=>'');

$delicious_options['prefix'] = 'delicious';

$delicious_options['rss_url'] = 'http://del.icio.us/rss/';

$delicious_options['tag_url'] = 'http://del.icio.us/tag/';



// Display del.icio.us recently bookmarked links.

function delicious_bookmarks($username = '', $num = 5, $list = true, $update = true, $tags = false, $filtertag = '', $displaydesc = false, $nodisplaytag = '' ) {
	
	global $delicious_options;
	include_once(ABSPATH . WPINC . '/rss.php');
	
	$rss = $delicious_options['rss_url'].$username;
	
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
			echo 'No bookmarks avaliable.';
			if ($list) echo '</li>';
		} else {
			foreach ( $bookmarks->items as $bookmark ) {
				$msg = $bookmark['title'];
				$updated = delicious_relative($bookmark['dc']['date']);
				$link = $bookmark['link'];
				$desc = $bookmark['description'];
			
				if ($list) echo '<li class="delicious-item">'; elseif ($num != 1) echo '<p class="delicious">';
        		echo '<a href="'.$link.'" class="delicious-link">'.$msg.'</a>'; // Puts a link to the... link.
      
				if ($update) echo ' <span class="delicious-timestamp">' . $updated . '</span>';
				
				if ($displaydesc && $desc != '') {
        			echo '<br />';
        			echo '<span class="delicious-desc">'.$desc.'</span>';
				}
				
				if ($tags) {
					echo '<br />';
					echo '<div class="delicious-tags">';
					$tagged = explode(' ', $bookmark['dc']['subject']);
					$ndtags = explode('+', $nodisplaytag);
					foreach ($tagged as $tag) {
					  if (!in_array($tag,$ndtags)) {
       			  echo '<a href="http://del.icio.us/tag/'.$tag.'" class="delicious-link-tag">'.$tag.'</a> '; // Puts a link to the tag.              
            }
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
	
	function widget_delicious($args, $number = 1) {
		
		global $delicious_options;
		
		// $args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys. Default tags: li and h2.
		extract($args);

		// Each widget can store its own options. We keep strings here.
		include_once(ABSPATH . WPINC . '/rss.php');
		$options = get_option('widget_delicious');
		
		// fill options with default values if value is not set
		$item = $options[$number];
		foreach($delicious_options['widget_fields'] as $key => $field) {
			if (! isset($item[$key])) {
				$item[$key] = $field['default'];
			}
		}
		$bookmarks = fetch_rss($delicious_options['rss_url'] . $username);

		// These lines generate our output.
		echo $before_widget . $before_title . $item['title'] . $after_title;
		delicious_bookmarks($item['username'], $item['num'], true, $item['update'], $item['tags'], $item['filtertag'], $item['displaydesc'], $item['nodisplaytag']);
		echo $after_widget;
	}



	// This is the function that outputs the form.
	function widget_delicious_control($number) {
		
		global $delicious_options;
		
		// Get our options and see if we're handling a form submission.
		$options = get_option('widget_delicious');


		if ( isset($_POST['delicious-submit']) ) {

			foreach($delicious_options['widget_fields'] as $key => $field) {
				$options[$number][$key] = $field['default'];
				$field_name = sprintf('%s_%s_%s', $delicious_options['prefix'], $key, $number);

				if ($field['type'] == 'text') {
					$options[$number][$key] = strip_tags(stripslashes($_POST[$field_name]));
				} elseif ($field['type'] == 'checkbox') {
					$options[$number][$key] = isset($_POST[$field_name]);
				}
			}

			update_option('widget_delicious', $options);
		}

		foreach($delicious_options['widget_fields'] as $key => $field) {
			
			$field_name = sprintf('%s_%s_%s', $delicious_options['prefix'], $key, $number);
			$field_checked = '';
			if ($field['type'] == 'text') {
				$field_value = htmlspecialchars($options[$number][$key], ENT_QUOTES);
			} elseif ($field['type'] == 'checkbox') {
				$field_value = 1;
				if (! empty($options[$number][$key])) {
					$field_checked = 'checked="checked"';
				}
			}
			
			printf('<p style="text-align:right;" class="delicious_field"><label for="%s">%s <input id="%s" name="%s" type="%s" value="%s" class="%s" %s /></label></p>',
				$field_name, __($field['label']), $field_name, $field_name, $field['type'], $field_value, $field['type'], $field_checked);
		}
		echo '<input type="hidden" id="delicious-submit" name="delicious-submit" value="1" />';
	}


	function widget_delicious_setup() {
		$options = $newoptions = get_option('widget_delicious');
		
		//echo '<style type="text/css">.delicious_field { text-align:right; } .delicious_field .text { width:200px; }</style>';
		
		if ( isset($_POST['delicious-number-submit']) ) {
			$number = (int) $_POST['delicious-number'];
			$newoptions['number'] = $number;
		}
		
		if ( $options != $newoptions ) {
			update_option('widget_delicious', $newoptions);
			widget_delicious_register();
		}
	}
	
	
	function widget_delicious_page() {
		$options = $newoptions = get_option('widget_delicious');
	?>
		<div class="wrap">
			<form method="POST">
				<h2><?php _e('del.icio.us Widgets'); ?></h2>
				<p style="line-height: 30px;"><?php _e('How many del.icio.us widgets would you like?'); ?>
				<select id="delicious-number" name="delicious-number" value="<?php echo $options['number']; ?>">
	<?php for ( $i = 1; $i < 10; ++$i ) echo "<option value='$i' ".($options['number']==$i ? "selected='selected'" : '').">$i</option>"; ?>
				</select>
				<span class="submit"><input type="submit" name="delicious-number-submit" id="delicious-number-submit" value="<?php echo attribute_escape(__('Save')); ?>" /></span></p>
			</form>
		</div>
	<?php
	}
	
	
	function widget_delicious_register() {
		
		$options = get_option('widget_delicious');
		$dims = array('width' => 300, 'height' => 300);
		$class = array('classname' => 'widget_delicious');

		for ($i = 1; $i <= 9; $i++) {
			$name = sprintf(__('del.icio.us #%d'), $i);
			$id = "delicious-$i"; // Never never never translate an id
			wp_register_sidebar_widget($id, $name, $i <= $options['number'] ? 'widget_delicious' : /* unregister */ '', $class, $i);
			wp_register_widget_control($id, $name, $i <= $options['number'] ? 'widget_delicious_control' : /* unregister */ '', $dims, $i);
		}
		
		add_action('sidebar_admin_setup', 'widget_delicious_setup');
		add_action('sidebar_admin_page', 'widget_delicious_page');
	}

	widget_delicious_register();
}

// Run our code later in case this loads prior to any required plugins.
add_action('widgets_init', 'widget_delicious_init');

?>