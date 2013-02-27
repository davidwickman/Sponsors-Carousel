<?php
/*
Plugin Name: Sponsors Carousel
Plugin URI: http://wordpress.org/extend/plugins/sponsors-carousel
Description: Sponsors logos on javascript carousel.
Version: 2.02
Author: Sergey Panasenko
Author URI: http://nitay.dp.ua
*/

/*  Copyright 2011  Sergey Panasenko  (email: sergey.panasenko@gmail.com)
    Copyright 2012  elija (http://wordpress.org/support/profile/elija)

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


/*****************************
* Options Page
*/

// Options
$scwp_plugin_name = __("Sponsors Carousel", 'sponsors-carousel');
$scwp_plugin_filename = basename(__FILE__); //"sponsors-carousel.php";

load_plugin_textdomain('sponsors-carousel', NULL, dirname(plugin_basename(__FILE__))."/languages");

add_shortcode('sponsors_carousel', 'sponsors_carousel');
add_action('wp_head', 'sponsors_carousel_header');

add_action('admin_init', 'scwp_admin_init');
add_action('admin_menu', 'add_scwp_option_page');


add_option("scwp_animation_speed", "fast", "", "yes");
add_option("scwp_show_titles", "true", "", "yes");
add_option("scwp_scroll_amount", "2", "", "yes");
add_option("scwp_default_link", "", "", "yes");
add_option("scwp_list", "", "", "yes");

//  New option for opening links in new window / tab
//	Default new
add_option("scwp_link_target", "new");

//  New option for auto scrolling speed
//	Default off
add_option("scwp_auto_scroll", 0);

/*****************************
* Enqueue jQuery & Scripts
*/
if (!is_admin()) {
	add_action('init', 'sponsors_carousel_enqueue_scripts');
}

function sponsors_carousel_enqueue_scripts() {
	if ( function_exists('plugin_url') )
		$plugin_url = plugin_url();
	else
		$plugin_url = get_option('siteurl') . '/wp-content/plugins/' . plugin_basename(dirname(__FILE__));

	// jquery
	wp_deregister_script('jquery');
	wp_register_script('jquery', ($plugin_url  . '/jquery-1.4.2.min.js'), false, '1.4.2');
	wp_enqueue_script('jquery');
	
}


function sponsors_carousel_header() {
	if ( function_exists('plugin_url') )
		$plugin_url = plugin_url();
	else
		$plugin_url = get_option('siteurl') . '/wp-content/plugins/' . plugin_basename(dirname(__FILE__));

	echo '<link href="' . $plugin_url . '/skins/tango/skin.css" rel="stylesheet" type="text/css" />' . "\n";
	echo '<script type="text/javascript" src="' . $plugin_url . '/jquery.jcarousel.min.js"></script>' . "\n";
}

function sponsors_carousel($output=null, $attr=null) {

	/**
	* Get list
	*/
	global $post;
	
	$scwp_array = explode (' ', get_option('scwp_list')) ;
	
	if ( empty($scwp_array) )
		return '';
	/**
	* Start output
	*/
	$output = "\t
	<!-- Begin Sponsors Carousel -->";
	$randomid ="".rand();
	$output .= '<ul id="mycarousel'.$randomid.'" class="jcarousel-skin-tango" >';
	$thumb_size = array(125,75);
	foreach ( $scwp_array as $id) {
//		wp_get_attachment_link($id, $size, true);
		if (get_post($id)->post_excerpt <> '')$link = get_post($id)->post_excerpt; 
		else $link = get_option('scwp_default_link');
		//  Relay link building code to include target
		$output .= "<li><a ";
		if ('new' == get_option('scwp_link_target')) {
			$output .= 'target="_blank"';
		}
		$output .= " id=\"item".$i. "\" href=\"".$link."\" class=\"jcarousel-item\">";
 		if (get_option('scwp_show_titles')=='true') $output .= wp_get_attachment_image( $id,$thumb_size ); 
		else $output .= wp_get_attachment_image( $id,$thumb_size, false, array(title=>"") ); 
		$output .= "</a></li>\n";
		$n++;
		
	}
	$output .= "</ul>\n";


	/**
	* Initialize
	*/
	$output .= "
<script type='text/javascript'>\n



 	jQuery(document).ready(function() {";
 	
	    if (get_option('scwp_animation_speed') == 'slow' || get_option('scwp_animation_speed') == 'fast') {
 		   $speed = '"'.stripslashes(get_option('scwp_animation_speed')).'"';
 		}
 		else {
 		  $speed = stripslashes(get_option('scwp_animation_speed'));
 		}
		if (get_option('scwp_auto_scroll')=='1')
		    $auto = "0.001
		    ,easing: 'linear'";
		else
		    $auto = stripslashes(get_option('scwp_auto_scroll'));
		
 		$output .= "jQuery('#mycarousel".$randomid."').jcarousel({
 			scroll: ".stripslashes(get_option('scwp_scroll_amount'))."
 			,animation: ".$speed."
 			,wrap: 'circular'
 			 ,auto: " . $auto . "
			
 		});
 		
 
 	});
 	</script>
 	";

	
	$output .= "
		<br style='clear: both;' />
	
	<!-- End Sponsors Carousel -->\n
	";


	return $output;

}





function scwp_admin_init() {
	if ( function_exists('register_setting') ) {
		register_setting('scwp_settings', 'option-1', '');

	}
}



function add_scwp_option_page() {
	global $wpdb;
	global $scwp_plugin_name;
	if( function_exists( 'add_meta_box' )) {
		add_meta_box('linkadvanceddiv', __('Advanced'), 'custom_link_advanced_meta_box', 'link', 'normal', 'core');
		wp_enqueue_script('quicktags');
		add_thickbox();
	}

	add_options_page($scwp_plugin_name . ' ' . __('Options', 'sponsors-carousel'), $scwp_plugin_name, 8, basename(__FILE__), 'scwp_options_page');
	
}

// Options function
function scwp_options_page() {

	if (isset($_POST['info_update'])) 
	{
			
		// Update options
		$scwp_animation_speed = $_POST["scwp_animation_speed"];
		update_option("scwp_animation_speed", $scwp_animation_speed);

		$scwp_show_titles = $_POST["scwp_show_titles"];
		update_option("scwp_show_titles", $scwp_show_titles);
		
		$scwp_scroll_amount = $_POST["scwp_scroll_amount"];
		update_option("scwp_scroll_amount", $scwp_scroll_amount);

		$scwp_default_link = $_POST["scwp_default_link"];
		update_option("scwp_default_link", $scwp_default_link);

		//  Save link target
		$scwp_link_target = $_POST["scwp_link_target"];
		update_option("scwp_link_target", $scwp_link_target);

		//  Auto scrolling speed
		$scwp_auto_scroll = $_POST["scwp_auto_scroll"];
		update_option("scwp_auto_scroll", $scwp_auto_scroll);
 
 		
		$scwp_link_image = $_POST["scwp_link_image"];
		if ($scwp_link_image > "")
		{
			if (substr_count($scwp_link_image,get_option('siteurl')) < 1)
				     $scwp_link_image = get_option('siteurl').$scwp_link_image;
			global $wpdb;
			
			$thepost = $wpdb->get_row( $wpdb->prepare( "SELECT *
			FROM $wpdb->posts WHERE guid = '".$scwp_link_image."'" ) );
			if (isset($thepost->ID))
			{
				$scwp_list = get_option('scwp_list');
				if ($scwp_list>"") $scwp_list.=" ";
				$scwp_list .= $thepost->ID;
				update_option("scwp_list", $scwp_list);
			}

		}

	// Give an updated message
		echo "<div class='updated fade'><p><strong>" . __('Options updated', 'sponsors-carousel') . "</strong></p></div>";
		
	}
	
	if (isset($_GET['move'])) 
	{
			
		// Move images
		$scwp_move = $_GET["move"];
		$scwp_image = $_GET["image"];
		$scwp_id_array = explode (' ', get_option('scwp_list')) ;
		$scwp_key = array_search ($scwp_image, $scwp_id_array);
		if(in_array($scwp_image, $scwp_id_array))
		{
			if ($scwp_move == "up" && $scwp_key > 0) 
			{
				$scwp_id_temp = $scwp_id_array[$scwp_key-1];
				$scwp_id_array[$scwp_key-1] = $scwp_id_array[$scwp_key];
				$scwp_id_array[$scwp_key] = $scwp_id_temp;
			}
		
			if ($scwp_move == "down" && $scwp_key < count($scwp_id_array)-1) 
			{
				$scwp_id_temp = $scwp_id_array[$scwp_key+1];
				$scwp_id_array[$scwp_key+1] = $scwp_id_array[$scwp_key];
				$scwp_id_array[$scwp_key] = $scwp_id_temp;
			}
			$scwp_list ="";
			foreach ($scwp_id_array as $scwp_id)
			{
			if (($scwp_move == "out" && $scwp_id==$scwp_image)==false) 
				$scwp_list .=$scwp_id." ";
			}
			$scwp_list = substr($scwp_list, 0, strlen($scwp_list)-1);
			update_option("scwp_list", $scwp_list);
		}
		
	}

	// Show options page
	?>

		<div class="wrap">
		
			<div class="options">
			<hr>
		
				<form method="post" action="options-general.php?page=<?php global $scwp_plugin_filename; echo $scwp_plugin_filename; ?>">
			
				<h2><?php global $scwp_plugin_name; printf(__('%s Settings', 'sponsors-carousel'), $scwp_plugin_name); ?></h2>
	
					<p><?php _e("Speed:", 'sponsors-carousel'); ?>
					<input type="text" size="10" name="scwp_animation_speed" id="scwp_animation_speed" value="<?php echo stripslashes(get_option('scwp_animation_speed')) ?>" />
					<a title="<?php _e("The speed of the animation. Options: 'fast', 'slow', or a number. 0 is instant, 10000 is very slow.", 'sponsors-carousel') ?>" />?</a>
					</p>
					<p><?php _e("Show Titles:", 'sponsors-carousel'); ?>
					<label>
					<?php
					echo "<input type='radio' ";
					echo "name='scwp_show_titles' ";
					echo "id='scwp_show_titles_0' ";
					echo "value='true' ";
					echo "true" == get_option('scwp_show_titles') ? ' checked="checked"' : "";
					echo " />";
					?>
					<?php _e("Yes, show image titles.", 'sponsors-carousel'); ?>
					</label>
					
					<label>
					<?php
					echo "<input type='radio' ";
					echo "name='scwp_show_titles' ";
					echo "id='scwp_show_titles_1' ";
					echo "value='false' ";
					echo "false" == get_option('scwp_show_titles') ? ' checked="checked"' : "";
					echo " />";
					?>
					<?php _e("No, hide image titles. ", 'sponsors-carousel'); ?>
					</label>
					<a title="<?php _e("Should the title of each image be shown?", 'sponsors-carousel') ?>"/>?</a>
					</p>
                     <p><?php _e("Scroll Amount:", 'sponsors-carousel'); ?>
					<input type="text" size="10" name="scwp_scroll_amount" id="scwp_scroll_amount" value="<?php echo stripslashes(get_option('scwp_scroll_amount')) ?>" />
					<a title="<?php _e("How many items should the carousel scroll", 'sponsors-carousel') ?>" />?</a>
					</p>
 		    <?php
			//  Output new option field for scrolling speed
		    ?>
                    <p><?php _e("Auto scroll:", 'sponsors-carousel'); ?>
					<select type="select" name="scwp_auto_scroll" id="scwp_auto_scroll">
						<option value="0" <?php if ('0' == stripslashes(get_option('scwp_auto_scroll'))) {  echo 'selected="selected"'; }?>><?php _e("Off", 'sponsors-carousel'); ?></option>
						<option value="1" <?php if ('1' == stripslashes(get_option('scwp_auto_scroll'))) {  echo 'selected="selected"'; }?>><?php _e("Continuous", 'sponsors-carousel'); ?></option>
						<option value="3" <?php if ('3' == stripslashes(get_option('scwp_auto_scroll'))) {  echo 'selected="selected"'; }?>><?php _e("Fast", 'sponsors-carousel'); ?></option>
						<option value="6" <?php if ('6' == stripslashes(get_option('scwp_auto_scroll'))) {  echo 'selected="selected"'; }?>><?php _e("Medium", 'sponsors-carousel'); ?></option>
						<option value="10" <?php if ('10' == stripslashes(get_option('scwp_auto_scroll'))) {  echo 'selected="selected"'; }?>><?php _e("Slow", 'sponsors-carousel'); ?></option>
					</select>
					<a title="<?php _e("Select the auto scrolling speed.", 'sponsors-carousel') ?>" />?</a>
					</p>
		    <?php
			//  Output new option field for link target
		    ?>
                    <p><?php _e("Link target:", 'sponsors-carousel'); ?>
					<select type="select" name="scwp_link_target" id="scwp_link_target">
						<option value="new" <?php if ('new' == stripslashes(get_option('scwp_link_target'))) {  echo 'selected="selected"'; }?>><?php _e("New Window or tab", 'sponsors-carousel'); ?></option>
						<option value="" <?php if ('' == stripslashes(get_option('scwp_link_target'))) {  echo 'selected="selected"'; }?>><?php _e("Same Window or tab", 'sponsors-carousel'); ?></option>
					</select>
					<a title="<?php _e("Choose whether the links should open in the current window.", 'sponsors-carousel') ?>" />?</a>
					</p>
                     <p><?php _e("Default link:", 'sponsors-carousel'); ?>
					<input type="text" size="50" name="scwp_default_link" id="scwp_default_link" value="<?php echo stripslashes(get_option('scwp_default_link')) ?>" />
					<a title="<?php _e("Link to sponsors page. You can add custom link in image caption.", 'sponsors-carousel') ?>" />?</a>
					</p>
					<?php _e("New (select *full size* before insert): ", 'sponsors-carousel'); ?>
					<input type="text" name="scwp_link_image" id="scwp_link_image" size="50" value="" style="width: 80%" />
					<?php 
					    if (get_bloginfo ( 'version' )<"3.3")
					      echo  _media_button(__('Add an Image'), 'images/media-button-image.gif?ver=20100531', 'image'); 
					    else
					      echo  _media_button(__('Add an Image'), 'images/media-button-image.gif?ver=20100531', 'image',$scwp_plugin_name); 
					
					?>
					</div>

		
					<script type="text/javascript">
					      function send_to_editor(html) {
						  var source = html.match(/src=\".*\" alt/);
						  source = source[0].replace(/^src=\"/, "").replace(/" alt$/, "");
						  jQuery('#scwp_link_image').val(source);
						  tb_remove();
					      }					
						
					</script>

					<p class="submit">
						<?php if ( function_exists('settings_fields') ) settings_fields('scwp_settings'); ?>
						<input type='submit' name='info_update' value="<?php _e('Save Changes', 'sponsors-carousel'); ?>" />
					</p>
				
				</form>
				
				
			<?php //.options ?>
			
		
		<div id='scwp_logos'>
		<table style="width:90%;td{padding:5px;}">
		<?php 
			if ( function_exists('plugin_url') ) $plugin_url = plugin_url();
				else $plugin_url = get_option('siteurl') . '/wp-content/plugins/' . plugin_basename(dirname(__FILE__));

			$scwp_id_array = explode (' ', get_option('scwp_list')) ;
			
			$scwp_id_num=1;
			if ($scwp_id_array[0]<>"")
			foreach ($scwp_id_array as $scwp_id)
			{
			if ($scwp_id_num % 2 == 1)	echo "<tr style='background-color:#eee'>";
				else echo "<tr style='background-color:#ddd'>";
			echo "<td  style='width:30px'>";
			if ($scwp_id_num<>1) echo " <a href='?page=sponsors-carousel.php&image=".$scwp_id."&move=up'><img src='".$plugin_url."/images/up.png'></a> ";
			echo "</td><td   style='width:30px'>";
			if ($scwp_id_num<count($scwp_id_array)) echo " <a href='?page=sponsors-carousel.php&image=".$scwp_id."&move=down'><img src='".$plugin_url."/images/down.png'></a> ";
			echo "</td>";
			 
			echo "<td style='padding:10px;width:40px;'><a href='media.php?attachment_id=".$scwp_id."&action=edit'>".wp_get_attachment_image( $scwp_id,array(125,75)) ."</a></td>";
			$scwp_id_num++;
			echo "<td style='padding:10px;'>".get_post($scwp_id)->post_excerpt."</td>";
			echo "<td   style='width:40px'>";
			echo " <a href='?page=sponsors-carousel.php&image=".$scwp_id."&move=out'><img src='".$plugin_url."/images/delete.png'></a></td></tr>\n ";
			}
		?>
		</table>
		</div>
	</div>

<?php
}

