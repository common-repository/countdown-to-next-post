<?php
/*
Plugin Name: Countdown to Next Post
Plugin URI: http://www.scottmulligan.ca/?page_id=1431
Plugin Description: This plugin will allow you to display a countdown timer that counts down to your next scheduled post.
Version: 1.0
Author: Scott Mulligan
Author URI: http://www.scottmulligan.ca

Countdown to Next Post - Adds a widget or code that counts down to your next sheduled post.
Copyright (c) 2009 Scott Mulligan

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
*/
		
	$currentLocale = get_locale();
	if(!empty($currentLocale)) {
		$moFile = dirname(__FILE__) . "/scott_countdownTimer-" . $currentLocale . ".mo";
		if(@file_exists($moFile) && is_readable($moFile)) load_textdomain('scott_countdownTimer', $moFile);
	}

	/**
	 * Displays the option page
	 *
	 * @access public
	 */
	function countdown_to_next_post_subpanel(){

		if (isset($_POST['scott_countdownTimer_update']))														//If the user has submitted the form, do the following
		{

		
			/*Check all posts to see if any are scheduled for a future post date*/		      
			global $wpdb;
			$testresult = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'future'");
			$testresult2 = $wpdb->get_results("SELECT post_date,post_title FROM $wpdb->posts WHERE post_status='future'");

			$minval = 0;
			$counter = 0;
			foreach ($testresult2 as $p) {
      			$mintime = strtotime($p->post_date);
      			$titledisplay = $p->post_title;

      		    if ($counter == 0)
      			{
      				$counter = 1; 
      				$minval = $mintime;
      				$mintitle = $titledisplay ;
      			}

      		    elseif ($mintime < $minval)
			{
      				$minval = $mintime; 
      				$mintitle = $titledisplay ;

			}	
				 		
			}
			 
			/* End Checking posts for scheduled posts */

			/*Begin One Time Events*/
			$oneTimeEvent_count = 1;												
			$j=0;																								
																							
			$timer_results["oneTime"][$j] = array(	"date" => $minval,	//Date of the event converted to UNIX time
								"text" => $mintitle,													
			); 															
					
			/*End One Time Events*/
		

			$scottOptions = array(	"deleteOneTimeEvents" 	=> $_POST['deleteOneTimeEvents'],
									"checkUpdate" 			=> $_POST['checkUpdate'],
									"timeOffset"			=> $_POST['timeOffset'],
									"displayFormatPrefix" 	=> $_POST['displayFormatPrefix'],
									"displayFormatSuffix" 	=> $_POST['displayFormatSuffix'],
									"displayStyle" 			=> $_POST['displayStyle'],
									"displayYear" 				=> $_POST['displayYear'],
									"displayMonth" 			=> $_POST['displayMonth'],
									"displayWeek" 				=> $_POST['displayWeek'],
									"displayDay" 				=> $_POST['displayDay'],
									"displayHour" 				=> $_POST['displayHour'],
									"displayMinute" 			=> $_POST['displayMinute'],
									"displaySecond" 			=> $_POST['displaySecond'],
									"stripZero" 			=> $_POST['stripZero'],
									"enableJS"				=> $_POST['enableJS'],
									"timeSinceTime"			=> (int)$_POST['timeSinceTime'],
									"postTitlePrefix"			=> $_POST['postTitlePrefix'],
									"serialDataFilename"	=> $_POST['serialDataFilename'],
									"text" => "",	
									"noposts" => $_POST["noPostsSet"],	
									"postTitle" => $mintitle,
									"titleYN" => $_POST['titleYN'],	
									"postTitleSuffix" => $_POST['postTitleSuffix'],	
									"numberOfPosts" => $testresult,			
			); //Create the array to store the countdown options

			update_option("scott_database", $timer_results); //Update the WPDB for the data
			
			update_option("scott_values", $scottOptions);//Update the WPDB for the options

			echo '<div id="message" class="updated fade"><p>'. __('Settings successfully updated.', 'scott_countdownTimer') .'</p></div>';					//Report to the user that the data has been updated successfully
		}
		global $scott_timer_dates, $scott_timer_getOptions;
		$scott_timer_dates = get_option("scott_database"); //Get the events from the WPDB to make sure a fresh copy is being used
		$scott_timer_getOptions = get_option("scott_values");//Get the options from the WPDB to make sure a fresh copy is being used
		/*If the user wants, cycle through the array to find out if they have already occured, if so: set them to NULL*/
		$scott_oneTimeDates=$scott_timer_dates["oneTime"];
		if($scott_timer_getOptions["deleteOneTimeEvents"] && (count($scott_oneTimeDates[0])!=0) ){
			foreach($scott_timer_dates["oneTime"] as $key => $value){
				if(($value["date"]<=time())&&($value["timeSince"]=="")){
				$scott_timer_dates["oneTime"][$key]["text"]=NULL;
				}
			}
		}
		?>

			<script type="text/javascript">
			
			jQuery(document).ready( function($) {
				// close postboxes that should be closed
				jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				
				// postboxes
				<?php
				global $wp_version;
				if(version_compare($wp_version,"2.7-alpha", "<")){
					echo "add_postbox_toggles('fergcorp-countdown-timer');"; //For WP2.6 and below
				}
				else{
					echo "postboxes.add_postbox_toggles('fergcorp-countdown-timer');"; //For WP2.7 and above
				}
				?>
			
			});

			
			function showHideContent(id, show){ //For hiding sections
				var elem = document.getElementById(id);
				if (elem){
					if (show){
						elem.style.display = 'block';
						elem.style.visibility = 'visible';
					}
					else{
						elem.style.display = 'none';
						elem.style.visibility = 'hidden';
					}
				}
			}

			</script>

			<div class="wrap" id="scott_countdownTimer_div">

				<h2>Countdown to Next Post</h2>
            
				<div id="poststuff">        
                    
				<?php
				
                		function scott_timer_resources_meta_box(){
							?>
                            <table width="90%" border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td><ul><li><a href="http://www.scottmulligan.ca/?page_id=1431" target="_blank"><?php _e('Plugin Homepage','scott_countdownTimer'); ?></a></li></ul></td>
                                    <td><ul><li><a href="http://scottmulligan.ca/" target="_blank"><?php _e('Author Homepage','scott_countdownTimer'); ?></a></li></ul></td>
                                    <td><ul><li><a href="http://www.scottmulligan.ca/?p=802" target="_blank"><?php _e('Map of Seinfeld Locations (Yes, I know its random!)','scott_countdownTimer'); ?></a></li></ul></td>								  
                                  </tr>
                                </table>

				<p><?php _e("My name is Scott Mulligan and this is my first WordPress plugin. I hope you find this plugin useful and please let me know if you find any bugs or have any suggestions. Thank you!", 'scott_countdownTimer'); ?></p>
                            
                            
				<?php
				}
				add_meta_box("scott_timer_resources", __('Resources'), "scott_timer_resources_meta_box", "fergcorp-countdown-timer");
						
                        ?>
   
                        <form method="post" name="scott_countdownTimer" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        
			    <?php echo '<input type="hidden" name="scott_timer_noncename" id="scott_timer_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ) . '" />'; ?>
                            <?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
                            <?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>

                            <?php
			    function scott_timer_installation_meta_box(){
			    ?>
                            <p><?php printf(__("<strong>Thank you for downloading this plugin!</strong> There are 3 different ways to use this plugin.", 'scott_countdownTimer')); ?></p>
			<p><?php printf(__("<strong>1. Sidebar Widget</strong><br />To insert the <em>Countdown to Next Post</em> counter into your sidebar, you can use the <a %s>sidebar widget</a>.", 'scott_countdownTimer'), "href='".admin_url('widgets.php')."'"); ?></p>			
			<p><?php _e("<strong>2. Manually Insert into Sidebar</strong><br />You also have the option of manually inserting the following code into your sidebar.php file:", 'scott_countdownTimer'); ?></p>
										
			<p>
			<code>&lt;li&gt;<br />
				&lt;h2&gt;Next Post&lt;/h2&gt;<br />
				&lt;ul&gt;<br />
				&lt;li&gt;<br />
				&lt;?php function_exists('scott_timer')?scott_timer():NULL; ?&gt;<br />
				&lt;/li&gt;<br />
				&lt;/ul&gt;<br />
				&lt;/li&gt;
			</code>
			</p>
			<p>
			If the timer does not seem to fit in with the rest of your sidebar items, you can always change the code a bit so that it displays correctly. For instance you can change the title name (Next Post) or change the size of the title by switching the &lt;h2&gt; tags to something else. 
			</p>

                        <p><?php printf(__("<strong>3. Insert into a page or post</strong><br />You can also insert the <em>Countdown to Next Post</em> counter into a page or post by adding the following shortcode into the HTML editor for the post or page.", 'scott_countdownTimer'), "title='".__('A shortcode is a WordPress-specific code that lets you do nifty things with very little effort. Shortcodes can embed files or create objects that would normally require lots of complicated, ugly code in just one line. Shortcode = shortcut.', 'scott_countdownTimer')."'", "style=''" ); ?></p>
                       	<code>
				[countdown_to_next_post]                        
			</code>
                                                         
                            <?php		
							}
                        	add_meta_box('scott_timer_installation', __('Installation'), 'scott_timer_installation_meta_box', 'fergcorp-countdown-timer', 'advanced', 'default');

				function scott_timer_management_meta_box(){
				global $scott_timer_getOptions;
			    ?>
			<ul>
			<li><?php _e('Enable JavaScript countdown:', 'scott_countdownTimer'); ?> <input name="enableJS" type="radio" value="1" <?php print($scott_timer_getOptions["enableJS"]==1?"checked='checked'":NULL)?> /><?php _e('Yes', 'scott_countdownTimer'); ?> :: <input name="enableJS" type="radio" value="0" <?php print($scott_timer_getOptions["enableJS"]==0?"checked='checked'":NULL)?>/><?php _e('No', 'scott_countdownTimer'); ?></li>
			</ul>
                                
			<?php /*<p><?php //_e('Countdown Timer exports your events so they can be used by other applications, such as Facebook. The location of your file is:', 'scott_countdownTimer'); ?></p>
			<ul>
                        <li><input name="serialDataFilename" type="hidden" value="<?php print($scott_timer_getOptions["serialDataFilename"]); ?>" size="50"/> <a href="<?php print(plugins_url(dirname(plugin_basename(__FILE__)) . "/" . $scott_timer_getOptions["serialDataFilename"])); ?>" target="_blank"><?php //print(plugins_url(dirname(plugin_basename(__FILE__)) . "/". $scott_timer_getOptions["serialDataFilename"])); ?></a></li>
		        </ul>
			*/ ?>
			<?php
			}
				add_meta_box("scott_timer_management", __('JavaScript Management', 'scott_countdownTimer'), "scott_timer_management_meta_box", "fergcorp-countdown-timer");
				function scott_timer_display_options_meta_box(){
				global $scott_timer_getOptions;
			?>
			<p><?php _e('<strong>This setting controls what units of time are displayed.</strong>', 'scott_countdownTimer'); ?></p>
								
			<p><?php _e('Years:', 'scott_countdownTimer'); ?> <input name="displayYear" type = "radio" value = "1" <?php print($scott_timer_getOptions["displayYear"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'scott_countdownTimer'); ?> :: <input name="displayYear" type = "radio" value = "0" <?php print($scott_timer_getOptions["displayYear"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'scott_countdownTimer'); ?></p>
			<p><?php _e('Months:', 'scott_countdownTimer'); ?> <input name="displayMonth" type = "radio" value = "1" <?php print($scott_timer_getOptions["displayMonth"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'scott_countdownTimer'); ?> :: <input name="displayMonth" type = "radio" value = "0" <?php print($scott_timer_getOptions["displayMonth"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'scott_countdownTimer'); ?></p>
			<p><?php _e('Weeks:', 'scott_countdownTimer'); ?> <input name="displayWeek" type = "radio" value = "1" <?php print($scott_timer_getOptions["displayWeek"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'scott_countdownTimer'); ?> :: <input name="displayWeek" type = "radio" value = "0" <?php print($scott_timer_getOptions["displayWeek"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'scott_countdownTimer'); ?></p>
			<p><?php _e('Days:', 'scott_countdownTimer'); ?> <input name="displayDay" type = "radio" value = "1" <?php print($scott_timer_getOptions["displayDay"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'scott_countdownTimer'); ?> :: <input name="displayDay" type = "radio" value = "0" <?php print($scott_timer_getOptions["displayDay"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'scott_countdownTimer'); ?></p>
			<p><?php _e('Hours:', 'scott_countdownTimer'); ?> <input name="displayHour" type = "radio" value = "1" <?php print($scott_timer_getOptions["displayHour"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'scott_countdownTimer'); ?> :: <input name="displayHour" type = "radio" value = "0" <?php print($scott_timer_getOptions["displayHour"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'scott_countdownTimer'); ?></p>
			<p><?php _e('Minutes:', 'scott_countdownTimer'); ?> <input name="displayMinute" type = "radio" value = "1" <?php print($scott_timer_getOptions["displayMinute"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'scott_countdownTimer'); ?> :: <input name="displayMinute" type = "radio" value = "0" <?php print($scott_timer_getOptions["displayMinute"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'scott_countdownTimer'); ?></p>
			<p><?php _e('Seconds:', 'scott_countdownTimer'); ?> <input name="displaySecond" type = "radio" value = "1" <?php print($scott_timer_getOptions["displaySecond"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'scott_countdownTimer'); ?> :: <input name="displaySecond" type = "radio" value = "0" <?php print($scott_timer_getOptions["displaySecond"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'scott_countdownTimer'); ?></p>
			<p><?php _e('Strip non-significant zeros:', 'scott_countdownTimer'); ?> <input name="stripZero" type = "radio" value = "1" <?php print($scott_timer_getOptions["stripZero"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'scott_countdownTimer'); ?> :: <input name="stripZero" type = "radio" value = "0" <?php print($scott_timer_getOptions["stripZero"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'scott_countdownTimer'); ?></p>
								
			<?php
			}
				add_meta_box("scott_timer_display_options", __('Countdown Time Display'), "scott_timer_display_options_meta_box", "fergcorp-countdown-timer");
							
				function scott_timer_display_format_options_meta_box(){
				global $scott_timer_getOptions;
			?>
							
				<p><?php _e('<strong>Post Title Prefix</strong><br />If the <em>Show Next Post Title</em> box is check below then this content will be displayed directly before the title of the next post.<br /> ex: <code>&lt;em&gt;</code>', 'scott_countdownTimer'); ?></p>
				<br />
				<p><?php _e('<strong>Show Next Post Title</strong><br />Check this box if you would like the title of the next post to be displayed before the countdown.', 'scott_countdownTimer'); ?></p>
				<br />
				<p><?php _e('<strong>Post Title Suffix</strong><br />If the <em>Show Next Post Title</em> box is check above then this content will be displayed directly after the title of the next post.<br /> ex: <code>&lt;/em&gt;, &lt;br /&gt;</code>', 'scott_countdownTimer'); ?></p>
				<br />
				<p><?php _e('<strong>Alternate Text If No Posts Are Scheduled</strong><br />This text will be displayed if there are no posts scheduled to be posted in the future.<br /> ex: <code>No Upcoming Posts, No posts are currently scheduled</code>', 'scott_countdownTimer'); ?></p>
				<br />
				<p><?php _e('<strong>Fill in the options below or leave some blank if you want. (You can use HTML tags!)</strong><br />', 'scott_countdownTimer'); ?></p>									                                 
                                <p><?php _e('Post Title Prefix', 'scott_countdownTimer'); ?> <input type="text" value="<?php echo htmlspecialchars(stripslashes($scott_timer_getOptions["postTitlePrefix"])); ?>" name="postTitlePrefix" /></p>  
                                <p><?php _e('Show Next Post Title', 'scott_countdownTimer'); ?> <input type="checkbox" value="1" name="titleYN" <?php if ($scott_timer_getOptions["titleYN"] == 1) {echo checked;}?> /></p>		   				   
				<p><?php _e('Post Title Suffix', 'scott_countdownTimer'); ?> <input type="text" value="<?php echo htmlspecialchars(stripslashes($scott_timer_getOptions["postTitleSuffix"])); ?>" name="postTitleSuffix" /></p>   
                            	<p><?php _e('Alternate Text If No Posts Are Scheduled', 'scott_countdownTimer'); ?> <input type="text" value="<?php echo htmlspecialchars(stripslashes($scott_timer_getOptions["noposts"])); ?>" name="noPostsSet" /></p>    
                
				<?php
				}
					add_meta_box("scott_timer_display_format_options", __('Output Format'), "scott_timer_display_format_options_meta_box", "fergcorp-countdown-timer");

					function scott_timer_example_display_meta_box(){
						echo "<ul>";
						scott_timer();
						echo "</ul>";
                                		scott_timer_js();
					}
					add_meta_box("scott_timer_example_display", __('Example Display'), "scott_timer_example_display_meta_box", "fergcorp-countdown-timer");

					do_meta_boxes('fergcorp-countdown-timer','advanced',null);                           
							   
					?>

					<div>
					<p class="submit">
					<input type="submit" name="scott_countdownTimer_update" value="<?php _e('Update Settings', 'scott_countdownTimer'); ?> &raquo;" />
					</p>
					</div>
					</form>
				</div>
       
            </div>
	<?php

	}
	/**
	 * scott_countdownTimer helper function
	 *
	 * @param $eventLimit int The maximum number of events to echo or return, sorted by date
	 * @param $output string If set to 'echo', will echo the results with no return; If set to 'return', will return the results with no echo.
	 * @access public
	 * @return string If set, will return the formated output ready for display
	*/
	function scott_timer($eventLimit = -1, $output = "echo"){
		scott_countdownTimer($eventLimit, $output);
	}

	/**
	 * Returns/echos the formated output for the countdown
	 *
	 * @param $eventLimit int The maximum number of events to echo or return, sorted by date
	 * @param $output string If set to 'echo', will echo the results with no return; If set to 'return', will return the results with no echo.
	 * @access public
	 * @return string If set, will return the formated output ready for display
	*/
	function scott_countdownTimer($eventLimit = -1, $output = "echo"){ //'echo' will print the results, 'return' will just return them
		global $scott_timer_getOptions, $scott_timer_noEventsPresent;
		$scott_timer_noEventsPresent = FALSE;
		
		$scott_timer_dates = get_option("scott_database");//Get our text, times, and settings from the database
		$scott_timer_getOptions = get_option("scott_values");//Get the options from the WPDB
		
		if($scott_timer_dates!=''){
			if(count($scott_timer_dates["oneTime"][0])!=0){
				foreach($scott_timer_dates["oneTime"] as $key => $value){
					if(($value["date"]<=time())&&($value["timeSince"]=="")){
					$scott_timer_dates["oneTime"][$key]["text"]=NULL;
					}
				}
			}
			else{
				$scott_timer_noEventsPresent = TRUE;//because there are no dates at all!
			}
		}
		else{
				$scott_timer_noEventsPresent = TRUE;//because there are no dates at all!
		}


		$eventCount = count($scott_timer_dates["oneTime"]);
		for($x=0; $x<$eventCount; $x++){
			for($z=0; $z<$eventCount-1; $z++){
				if(($scott_timer_dates["oneTime"][$z+1]["date"] < $scott_timer_dates["oneTime"][$z]["date"]) && (array_key_exists($z+1, $scott_timer_dates["oneTime"]))){
					$temp = $scott_timer_dates["oneTime"][$z];
					$scott_timer_dates["oneTime"][$z] = $scott_timer_dates["oneTime"][$z+1];
					$scott_timer_dates["oneTime"][$z+1] = $temp;
				}
			}
		}
		if($eventLimit != -1)	//If the eventLimit is set
			$eventCount = $eventLimit;

		//This is the part that does the actual outputting. If you want to preface data, this an excellent spot to do it in.
		if($scott_timer_noEventsPresent == FALSE){
			$scott_timer_noEventsPresent = TRUE; //Reset the test
		$i=0;
				if($output == "echo")
					echo scott_timer_format(stripslashes($scott_timer_getOptions["text"]), $scott_timer_dates["oneTime"][$i]["date"], 0, $scott_timer_dates["oneTime"][$i]["timeSince"], $scott_timer_getOptions["timeSinceTime"], stripslashes($scott_timer_dates["oneTime"][$i]["link"]), $scott_timer_getOptions["timeOffset"], stripslashes($scott_timer_getOptions["displayFormatPrefix"]), stripslashes($scott_timer_getOptions["displayFormatSuffix"]), stripslashes($scott_timer_getOptions["displayStyle"]));
				elseif($output == "return"){
					$scottReturn .= scott_timer_format(stripslashes($scott_timer_getOptions["text"]), $scott_timer_dates["oneTime"][$i]["date"], 0, $scott_timer_dates["oneTime"][$i]["timeSince"], $scott_timer_getOptions["timeSinceTime"], stripslashes($scott_timer_dates["oneTime"][$i]["link"]), stripslashes($scott_timer_getOptions["timeOffset"]), stripslashes($scott_timer_getOptions["displayFormatPrefix"]), stripslashes($scott_timer_getOptions["displayFormatSuffix"]), stripslashes($scott_timer_getOptions["displayStyle"]));
				}
				if(($scott_timer_dates["oneTime"][$i]["text"]==NULL) && (isset($scott_timer_dates["oneTime"][$i]))){
					$eventCount++;
				}
		}
		
		if(($scott_timer_getOptions["numberOfPosts"] == 0)){
			if($output == "echo"){
				echo $scott_timer_getOptions["displayFormatPrefix"].__($scott_timer_getOptions["noposts"], 'scott_countdownTimer').$scott_timer_getOptions["displayFormatSuffix"];
			}
			elseif($output == "return"){
				$scottReturn .= $scott_timer_getOptions["displayFormatPrefix"].__($scott_timer_getOptions["noposts"], 'scott_countdownTimer').$scott_timer_getOptions["displayFormatSuffix"];
			}
		}

		if($output == "return")
				return $scottReturn;
	}

	/**
	 * Returns an individual countdown element
	 *
	 * @param $text string 
	 * @param $time int Unix time of the event
	 * @param $offset int Server offset of the time
	 * @param $timeSince int If the event should be displayed if it has already passed
	 * @param $timeSinceTime int If $timeSince is set, how long should it be displayed for in seconds
	 * @param $link string
	 * @param $timeFormat string Forming of the onHover time display
	 * @param $displayFormatPrefix string HTML tags to prefix the event element
	 * @param $displayFormatSuffix string HTML tags to suffix the event element
	 * @param $displayStyle string CSS styles to apply to the event element
	 * @access private
	 * @return string
	*/
	
	function scott_timer_format($text, $time, $offset, $timeSince=0, $timeSinceTime=0, $link=NULL, $timeFormat = "j M Y, G:i:s", $displayFormatPrefix = "<li>", $displayFormatSuffix = "</li>", $displayStyle = ""){
		global $scott_timer_noEventsPresent, $scott_timer_getOptions, $scott_timer_nonceTracker, $scott_timer_dates;
		if(!isset($scott_timer_nonceTracker)){
			$scott_timer_nonceTracker = array();
		}
		$time_left = $time - time() + $offset;
		$scottContent = "";
		if(($scott_timer_getOptions["numberOfPosts"] != 0)&&($time_left < 0)){
		
			return "Posting... please wait and refresh";

		}
		elseif($time_left > 0){

			$scott_timer_noEventsPresent = FALSE;
			$nonceTracker = "y".md5(rand()); //XHTML prevents IDs from starting with a number, so append a 'x' on the front just to make sure it dosn't
			$scott_timer_nonceTracker[count($scott_timer_nonceTracker)] = array("id"			=> $nonceTracker,
																										"targetDate"	=> $time,);
			if ($scott_timer_getOptions["titleYN"])
			{																							
					$scottContent = $displayFormatPrefix.$scott_timer_getOptions["postTitlePrefix"].$scott_timer_getOptions["postTitle"].stripslashes($scott_timer_getOptions["postTitleSuffix"])."\n";
				

				if($timeFormat == "")
					$scottContent .= "<span id = '$nonceTracker'>".sprintf(__("in %s", 'scott_countdownTimer'), scott_timer_fuzzyDate($time, (time() + $offset), $time))."</span>".$displayFormatSuffix;
				else
					$scottContent .= "<abbr title = \"".gmdate($timeFormat, $time + (get_option('gmt_offset') * 3600))."\" style=\"". $displayStyle ."\"><span id = '$nonceTracker'>".sprintf(__("in %s", 'scott_countdownTimer'), scott_timer_fuzzyDate($time, (time() + $offset), $time))."</span></abbr>".$displayFormatSuffix;
					return $scottContent;
			}
			else
			{
				
				if($timeFormat == "")
					$scottContent .= "<span id = '$nonceTracker'>".sprintf(__("in %s", 'scott_countdownTimer'), scott_timer_fuzzyDate($time, (time() + $offset), $time))."</span>".$displayFormatSuffix;
				else
					$scottContent .= "<abbr title = \"".gmdate($timeFormat, $time + (get_option('gmt_offset') * 3600))."\" style=\"". $displayStyle ."\"><span id = '$nonceTracker'>".sprintf(__("in %s", 'scott_countdownTimer'), scott_timer_fuzzyDate($time, (time() + $offset), $time))."</span></abbr>".$displayFormatSuffix;
					return $scottContent;
			}
		}
		else{
			return NULL;
		}
	}
	
	if(!function_exists("cal_days_in_month")){
		/**
		 * Returns the number of days in a given month and year, taking into account leap years.
		 * The is a replacement function should cal_days_in_month not be availible
		 *
		 * @param $calendar int ignored
	 	 * @param $month int month (integers 1-12) 
		 * @param $year int year (any integer)
		 * @access private
		 * @author David Bindel (dbindel at austin dot rr dot com) (http://us.php.net/manual/en/function.cal-days-in-month.php#38666)
		 * @author ben at sparkyb dot net
		 * @return int The content of the post with the appropriate dates inserted (if any)
		*/
		function cal_days_in_month($calendar, $month, $year){
			// calculate number of days in a month 
			return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
		}
	}

	/**
	 * Returns the numerical part of a single countdown element
	 *
	 * @param $targetTime
	 * @param $nowTime
	 * @param $realTargetTime
	 * @access private
	 * @return string
	*/
	function scott_timer_fuzzyDate($targetTime, $nowTime, $realTargetTime){
		global $scott_timer_getOptions;

		$rollover = 0;
		$s = '';
		$sigNumHit = false;

		$nowYear = date("Y", $nowTime);
		$nowMonth = date("m", $nowTime);
		$nowDay = date("d", $nowTime);
		$nowHour = date("H", $nowTime);
		$nowMinute = date("i", $nowTime);
		$nowSecond = date("s", $nowTime);

		$targetYear = date("Y", $targetTime);
		$targetMonth = date("m", $targetTime);
		$targetDay = date("d", $targetTime);
		$targetHour = date("H", $targetTime);
		$targetMinute = date("i", $targetTime);
		$targetSecond = date("s", $targetTime);

		$resultantYear = $targetYear - $nowYear;
		$resultantMonth = $targetMonth - $nowMonth;
		$resultantDay = $targetDay - $nowDay;
		$resultantHour = $targetHour - $nowHour;
		$resultantMinute = $targetMinute - $nowMinute;
		$resultantSecond = $targetSecond - $nowSecond;
		
		if($resultantSecond < 0){
			$resultantMinute--;
			$resultantSecond = 60 + $resultantSecond;
		}

		if($resultantMinute < 0){
			$resultantHour--;
			$resultantMinute = 60 + $resultantMinute;
		}

		if($resultantHour < 0){

			$resultantDay--;
			$resultantHour = 24 + $resultantHour;
		}

		if($resultantDay < 0){
			$resultantMonth--;
			$resultantDay = $resultantDay + cal_days_in_month(CAL_GREGORIAN, $nowMonth, $nowYear); //Holy crap! When did they introduce this function and why haven't I heard about it??
		}

		if($resultantMonth < 0){
			$resultantYear--;
			$resultantMonth = $resultantMonth + 12;
		}

		//Year
		if($scott_timer_getOptions['displayYear']){
			if($sigNumHit || !$scott_timer_getOptions['stripZero'] || $resultantYear){
				if($resultantYear==1){
					$s = sprintf(__("%d year, ", "scott_countdownTimer"), $resultantYear)." ";
				} else{
					$s = sprintf(__("%d years, ", "scott_countdownTimer"), $resultantYear)." ";
				}
				$sigNumHit = true;
			}
		}
		else{
			$rollover = $resultantYear*31536000;
		}

		//Month
		if($scott_timer_getOptions['displayMonth']){
			if($sigNumHit || !$scott_timer_getOptions['stripZero'] || intval($resultantMonth + ($rollover/2628000)) ){
				$resultantMonth = intval($resultantMonth + ($rollover/2628000));
				if($resultantMonth==1){
					$s .= sprintf(__("%d month, ", "scott_countdownTimer"), $resultantMonth)." ";
				} else{
					$s .= sprintf(__("%d months, ", "scott_countdownTimer"), $resultantMonth)." ";
				}
				$rollover = $rollover - intval($rollover/2628000)*2628000; //(12/31536000)
				$sigNumHit = true;
			}
		}
		else{
			
			//If we don't want to show months, let's just calculate the exact number of seconds left since all other units of time are fixed (i.e. months are not a fixed unit of time)
						
			$rollover = $rollover + $resultantMonth*2592000;
			
			$totalTime = $targetTime - $nowTime;
			
			//If we showed years, but not months, we need to account for those.
			if($scott_timer_getOptions['displayYear']){
				$totalTime = $totalTime - $resultantYear*31536000;
			}
			
			//Re calculate the resultant times
			$resultantWeek = intval( $totalTime/(86400*7) );
			 
			$resultantDay = intval( $totalTime/86400 );
			
			$resultantHour = intval( ($totalTime - $resultantDay*86400)/3600 );
			
			$resultantMinute = intval( ($totalTime - $resultantDay*86400 - $resultantHour*3600)/60 );
			
			$resultantSecond = intval( ($totalTime - $resultantDay*86400 - $resultantHour*3600 - $resultantMinute*60) );
			
			//and clear any rollover time
			$rollover = 0;
		}

		//Week (weeks are counted differently becuase we can just take 7 days and call it a week...so we do that)
		if($scott_timer_getOptions['displayWeek']){
			if($sigNumHit || !$scott_timer_getOptions['stripZero'] || ( ($resultantDay + intval($rollover/86400) )/7)){
				$resultantWeek = $resultantWeek + intval($rollover/86400)/7;
				if((intval( ($resultantDay + intval($rollover/86400) )/7))==1){
					$s .= sprintf(__("%d week, ", "scott_countdownTimer"), (intval( ($resultantDay + intval($rollover/86400) )/7)))." ";
				} else{
					$s .= sprintf(__("%d weeks, ", "scott_countdownTimer"), (intval( ($resultantDay + intval($rollover/86400) )/7)))." ";
				}
				$rollover = $rollover - intval($rollover/86400)*86400;
				$resultantDay = $resultantDay - intval( ($resultantDay + intval($rollover/86400) )/7 )*7;
				$sigNumHit = true;
			}
		}

		//Day
		if($scott_timer_getOptions['displayDay']){
			if($sigNumHit || !$scott_timer_getOptions['stripZero'] || ($resultantDay + intval($rollover/86400)) ){
				$resultantDay = $resultantDay + intval($rollover/86400);
				if($resultantDay==1){
					$s .= sprintf(__("%d day, ", "scott_countdownTimer"), $resultantDay)." ";
				} else{
					$s .= sprintf(__("%d days, ", "scott_countdownTimer"), $resultantDay)." ";
				}
				$rollover = $rollover - intval($rollover/86400)*86400;
				$sigNumHit = true;
			}
		}
		else{
			$rollover = $rollover + $resultantDay*86400;
		}

		//Hour
		if($scott_timer_getOptions['displayHour']){
			if($sigNumHit || !$scott_timer_getOptions['stripZero'] || ($resultantHour + intval($rollover/3600)) ){
				$resultantHour = $resultantHour + intval($rollover/3600);
				if($resultantHour==1){
					$s .= sprintf(__("%d hour, ", "scott_countdownTimer"), $resultantHour)." ";
				} else{
					$s .= sprintf(__("%d hours, ", "scott_countdownTimer"), $resultantHour)." ";
				}
				$rollover = $rollover - intval($rollover/3600)*3600;
				$sigNumHit = true;
			}
		}
		else{
			$rollover = $rollover + $resultantHour*3600;
		}

		//Minute
		if($scott_timer_getOptions['displayMinute']){
			if($sigNumHit || !$scott_timer_getOptions['stripZero'] || ($resultantMinute + intval($rollover/60)) ){
				$resultantMinute = $resultantMinute + intval($rollover/60);
				if($resultantMinute==1){
					$s .= sprintf(__("%d minute, ", "scott_countdownTimer"), $resultantMinute)." ";
				} else{
					$s .= sprintf(__("%d minutes, ", "scott_countdownTimer"), $resultantMinute)." ";
				}
				$rollover = $rollover - intval($rollover/60)*60;
				$sigNumHit = true;
			}
		}
		else{
			$rollover = $rollover + $resultantMinute*60;
		}

		//Second
		if($scott_timer_getOptions['displaySecond']){
			$resultantSecond = $resultantSecond + $rollover;
			if($resultantSecond==1){
				$s .= sprintf(__("%d second, ", "scott_countdownTimer"), $resultantSecond)." ";
			} else{
				$s .= sprintf(__("%d seconds, ", "scott_countdownTimer"), $resultantSecond)." ";
			}
		}
		
		//Catch blank statements
		if($s==""){
			if($scott_timer_getOptions['displaySecond']){
				$s = sprintf(__("%d seconds, ", "scott_countdownTimer"), "0");
			}
			elseif($scott_timer_getOptions['displayMinute']){
				$s = sprintf(__("%d minutes, ", "scott_countdownTimer"), "0");
			}
			elseif($scott_timer_getOptions['displayHour']){
				$s = sprintf(__("%d hours, ", "scott_countdownTimer"), "0");
			}	
			elseif($scott_timer_getOptions['displayDay']){
				$s = sprintf(__("%d days, ", "scott_countdownTimer"), "0");
			}	
			elseif($scott_timer_getOptions['displayWeek']){
				$s = sprintf(__("%d weeks, ", "scott_countdownTimer"), "0");
			}
			elseif($scott_timer_getOptions['displayMonth']){
				$s = sprintf(__("%d months, ", "scott_countdownTimer"), "0");
			}
			else{
				$s = sprintf(__("%d years, ", "scott_countdownTimer"), "0");
			}
		}
		
		return preg_replace("/(,? *)$/is", "", $s);

	}


	/**
	 * Returns the content of the post with dates inserted (if any)
	 *
	 * @param $displayContent string The content of the post
	 * @access public
	 * @return string The content of the post with the appropriate dates inserted (if any)
	*/
	function scott_countdownTimer_loop($displayContent){
		global $scott_timer_getOptions;
																						//Filter function for including the countdown with The Loop
		if(preg_match("<!--scott_countdownTimer(\([0-9]+\))-->", $displayContent)){																//If the string is found within the loop, replace it
			$displayContent = preg_replace("/<!--scott_countdownTimer(\(([0-9]+)\))?-->/e", "scott_countdownTimer($2, 'return')", $displayContent);	//The actual replacement of the string with the timer
		}
		elseif(preg_match("<!--scott_countdownTimer-->", $displayContent)){																		//If the string is found within the loop, replace it
			$displayContent = preg_replace("/<!--scott_countdownTimer-->/e", "scott_countdownTimer('-1', 'return')", $displayContent);				//The actual replacement of the string with the timer
		}

		if(preg_match("<!--scott_countdownTimer_single\((.*?)\)-->", $displayContent)){
			$displayContent = preg_replace("/<!--scott_countdownTimer_single\(('|\")(.*?)('|\")\)-->/e", "scott_timer_format('', strtotime('$2'), ".( date('Z') - (get_settings('gmt_offset') * 3600) ).", true, '0', '', '".$scott_timer_getOptions['timeOffset']."', '', '', '')", $displayContent);
		}

		return $displayContent;																													//Return theContent
	}
	add_filter('the_content', 'scott_countdownTimer_loop', 1);
	
	/**
	 * Processes shortcodes
	 *
	 * @param $atts array Attributes of the shortcode
	 * @access public
	 * @return string countdown timer(s)
	*/	
	// [fergcorp_cdt max=##]
	function scott_function($atts) {
		extract(shortcode_atts(array(
			'max' => '-1',
		), $atts));
	
		return scott_countdownTimer(1, 'return');
	}
	add_shortcode('countdown_to_next_post', 'scott_function');
	
	/**
	 * Creates a PHP-based one-off time for use outside the loop
	 *
	 * @param $date string Any string parsable by PHP's strtotime function
	 * @access public
	*/
	function scott_timer_single($date){
		global $scott_timer_getOptions;
		return scott_timer_format('', strtotime($date), ( date('Z') - (get_settings('gmt_offset') * 3600) ), true, '0', '', $scott_timer_getOptions['timeOffset'], '', '', '');
	
	}


	/**
	 * Sets the defaults for the timer
	 *
	 * @access public
	*/
	function scott_countdownTimer_install(){
		$plugin_data = get_plugin_data(__FILE__);
		$theOptions = get_option("scott_values");

		if(get_option("widget_scott_timer") == NULL){	//Create default details for the widget if needed
			update_option("widget_scott_timer", array("title"=>"Next Post", "count"=>"1"));
		}

		$scottOptions = array(	"deleteOneTimeEvents"	=> "0",
								"checkUpdate"			=> "1",
								"timeOffset"			=> "F jS, Y, g:i a",
								"displayFormatPrefix"	=> "",
								"displayFormatSuffix"	=> "",
								"displayStyle"			=> "",
								"displayYear"				=> "0",
								"displayMonth"				=> "0",
								"displayWeek"				=> "0",
								"displayDay"				=> "1",
								"displayHour"				=> "1",
								"displayMinute"			=> "1",
								"displaySecond"			=> "1",
								"stripZero"				=> "1",
								"enableJS"				=> "1",
								"timeSinceTime"			=> "0",
								"postTitlePrefix"			=> "",
								"serialDataFilename"	=> "scott_timer_serialData_".wp_generate_password(8,false).".txt",
								"text" => "",	
								"noposts" => "No Scheduled Posts",	
								"titleYN" => "1",	
								"postTitleSuffix" => "<br />",	
		);

		//Check to see what options exists and add the ones that don't, keeping the values for the ones that do
		foreach($scottOptions as $key => $value){
			if(array_key_exists($key, $theOptions)){
				$newWidgetOptionsArray["$key"] = $theOptions["$key"];
			}
			else{
				$newWidgetOptionsArray["$key"] = $value;
			}
		}

		update_option("scott_values", $newWidgetOptionsArray); //Update the WPDB for the options
		update_option("scott_timer_version", $plugin_data["Version"]);
	}


	if(!function_exists('widget_scott_timer_init')){

		/**
		 * Initialize the widget
		 *
		 * @access public
		*/
		function widget_scott_timer_init() {

			// Check for the required plugin functions. This will prevent fatal
			// errors occurring when you deactivate the dynamic-sidebar plugin.
			if ( !function_exists('wp_register_sidebar_widget') || !function_exists('wp_register_widget_control') )
				return;

			/**
			 * Saves options and prints the widget's config form.
			 *
			 * @access private
			*/
			function widget_scott_timer_control() {
				$widgetOptions = $newWidgetOptions = get_option('widget_scott_timer');
				if ( $_POST['countdown-submit'] ) {
					$newWidgetOptions['title'] = strip_tags(stripslashes($_POST['widget-title']));
					$newWidgetOptions['count'] = (int) $_POST['countdown-count'];
				}
				if ( $widgetOptions != $newWidgetOptions ) {
					$widgetOptions = $newWidgetOptions;
					update_option('widget_scott_timer', $widgetOptions);
				}
			?>
						<div style="text-align:left">
						<label for="widget-title" style="line-height:35px;display:block;"><?php _e('Widget title:', 'scott_countdownTimer'); ?> <input type="text" id="widget-title" name="widget-title" value="<?php echo wp_specialchars($widgetOptions['title'], true); ?>" /></label>
						
						<?php $link = "href='".admin_url('tools.php?page=countdown_to_next_post.php')."'" ?>
						
						<input type="hidden" name="countdown-submit" id="countdown-submit" value="1" />
						<small><strong><?php _e('Note:', 'widget_scott_timer'); ?></strong> <?php _e("You can find more options on the <em>Countdown to Next Post</em> <a $link>settings page</a>.", 'scott_countdownTimer'); ?></small>
						</div>
			<?php
			}

			/**
			 * Outputs the widget version of the countdown to Next Post
			 *
			 * @access private
			*/
			function widget_scott_timer($args) {

				$widgetOptions = get_option('widget_scott_timer');

				extract($args);

				$title = $widgetOptions['title'];

				// These lines generate our output.
				echo $before_widget . $before_title . $title . $after_title;

				?>
				<ul>
				<li>
					<?php scott_countdownTimer(1, "echo"); ?>
				</li>
				</ul>
				<?php
				echo $after_widget;
			}

			// This registers our widget so it appears with the other available
			// widgets and can be dragged and dropped into any active sidebars.
			$widget_ops = array('description' => __('Adds a countdown for your next scheduled post', 'scott_countdownTimer'));
			wp_register_sidebar_widget('scott_timer', 'Countdown to Next Post', 'widget_scott_timer', $widget_ops);
			wp_register_widget_control('scott_timer', 'Countdown to Next Post', 'widget_scott_timer_control');

		}

	// Run our code later in case this loads prior to any required plugins.
	add_action('widgets_init', 'widget_scott_timer_init');
}

	/**
	 * Echos the JavaScript for the timer
	 *
	 * @access public
	*/
	function scott_timer_js(){
		global $scott_timer_nonceTracker;
		global $scott_timer_getOptions;

		echo "<script type=\"text/javascript\">\n";
		echo "<!--\n";

		//Pass on what units of time should be used
		echo "var getOptions = new Array();\n";
		echo "getOptions['displayYear'] = ".$scott_timer_getOptions['displayYear'].";\n";
		echo "getOptions['displayMonth'] = ".$scott_timer_getOptions['displayMonth'].";\n";
		echo "getOptions['displayWeek'] = ".$scott_timer_getOptions['displayWeek'].";\n";
		echo "getOptions['displayDay'] = ".$scott_timer_getOptions['displayDay'].";\n";
		echo "getOptions['displayHour'] = ".$scott_timer_getOptions['displayHour'].";\n";
		echo "getOptions['displayMinute'] = ".$scott_timer_getOptions['displayMinute'].";\n";
		echo "getOptions['displaySecond'] = ".$scott_timer_getOptions['displaySecond'].";\n";
		echo "getOptions['stripZero'] = ".$scott_timer_getOptions['stripZero'].";\n";

		//Pass on language variables
		echo "var scott_timer_js_language = new Array();\n";
		echo "scott_timer_js_language['year'] = '".addslashes(__('%d year, ', 'scott_timer'))."';\n";
		echo "scott_timer_js_language['years'] = '".addslashes(__('%d years, ', 'scott_timer'))."';\n";
		echo "scott_timer_js_language['month'] = '".addslashes(__('%d month, ', 'scott_timer'))."';\n";
		echo "scott_timer_js_language['months'] = '".addslashes(__('%d months, ', 'scott_timer'))."';\n";
		echo "scott_timer_js_language['week'] = '".addslashes(__('%d week, ', 'scott_timer'))."';\n";
		echo "scott_timer_js_language['weeks'] = '".addslashes(__('%d weeks, ', 'scott_timer'))."';\n";
		echo "scott_timer_js_language['day'] = '".addslashes(__('%d day, ', 'scott_timer'))."';\n";
		echo "scott_timer_js_language['days'] = '".addslashes(__('%d days, ', 'scott_timer'))."';\n";
		echo "scott_timer_js_language['hour'] = '".addslashes(__('%d hour, ', 'scott_timer'))."';\n";
		echo "scott_timer_js_language['hours'] = '".addslashes(__('%d hours, ', 'scott_timer'))."';\n";
		echo "scott_timer_js_language['minute'] = '".addslashes(__('%d minute, ', 'scott_timer'))."';\n";
		echo "scott_timer_js_language['minutes'] = '".addslashes(__('%d minutes, ', 'scott_timer'))."';\n";
		echo "scott_timer_js_language['second'] = '".addslashes(__('%d second, ', 'scott_timer'))."';\n";
		echo "scott_timer_js_language['seconds'] = '".addslashes(__('%d seconds, ', 'scott_timer'))."';\n";
		echo "scott_timer_js_language['ago'] = '".addslashes(__('%s ago', 'scott_timer'))."';\n";
		echo "scott_timer_js_language['in'] = '".addslashes(__('in %s', 'scott_timer'))."';\n";

		//Pass on details about each timer
		echo "var scott_timer_js_events = new Array();\n";
		for($i=0; $i < count($scott_timer_nonceTracker); $i++){
				echo "scott_timer_js_events[$i] = new Array()\n";
				echo "scott_timer_js_events[$i]['id'] 		= \"".$scott_timer_nonceTracker[$i]['id']."\";\n";
				echo "scott_timer_js_events[$i]['targetDate'] 	= \"".$scott_timer_nonceTracker[$i]['targetDate']."\";\n";

		}
		echo "scott_timer_js();\n";
		echo "//-->\n";
		echo "</script>\n";
	}

	/**
	 * Adds the management page in the admin menu
	 *
	 * @access public
	 */
	function scott_countdownTimer_optionsPage(){																		//Action function for adding the configuration panel to the Management Page
		if(function_exists('add_management_page')){
				$scott_timer_add_management_page = add_management_page('Countdown to Next Post', 'Countdown to Next Post', 10, basename(__FILE__), 'countdown_to_next_post_subpanel');
				add_action( "admin_print_scripts-$scott_timer_add_management_page", 'scott_timer_LoadUserScripts' );
				add_action( "admin_print_scripts-$scott_timer_add_management_page", 'scott_timer_LoadAdminScripts' );
		}
	}

	add_action('admin_menu', 'scott_countdownTimer_optionsPage');	//Add Action for adding the options page to admin panel
	register_activation_hook( __FILE__, 'scott_countdownTimer_install');

	$scott_timer_getOptions = get_option("scott_values");	//Get the options from the WPDB 
	
	if($scott_timer_getOptions["enableJS"]) {
		add_action('wp_footer', 'scott_timer_js');
	}

	add_action('wp_head', 'scott_timer_LoadUserScripts', 1); //Priority needs to be set to 1 so that the scripts can be enqueued before the scripts are printed, since both actions are hooked into the wp_head action.

	/**
	 * Loads the appropriate scripts when in the admin page
	 *
	 * @access private
	 */
	function scott_timer_LoadAdminScripts() {
	    wp_enqueue_script('postbox'); //These appear to be new functions in WP 2.5
	}
	
	/**
	 * Loads the appropriate scripts
	 *
	 * @access private
	 */
	function scott_timer_LoadUserScripts() {
		$scott_timer_getOptions = get_option("scott_values");
		$scott_timer_getVersion = get_option("scott_timer_version");
		if($scott_timer_getOptions["enableJS"]) {
			wp_enqueue_script('scott_timer', plugins_url(dirname(plugin_basename(__FILE__)) . "/". 'scott_timer_java.js'), FALSE, $scott_timer_getVersion);
			wp_enqueue_script('webkit_sprintf', plugins_url(dirname(plugin_basename(__FILE__)) . "/" . 'webtoolkit.sprintf.js'), FALSE, $scott_timer_getVersion);
		}
	}
	
	
	
	/**
 * Called when post is saved - Check to see if your post is for the future and if it is it checks to see if it is the next post.
 */
function predatefuturepost_update_post_meta($id) {
  
 
			/*Check all posts to see if any are scheduled for a future post date*/		      
			global $wpdb;
			$testresult = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'future'");
			

			$testresult2 = $wpdb->get_results("SELECT post_date,post_title FROM $wpdb->posts WHERE post_status='future'");

			$minval = 0;
			$counter = 0;
			foreach ($testresult2 as $p) {
      			$mintime = strtotime($p->post_date);
      			$titledisplay = $p->post_title;

      		    if ($counter == 0)
      			{
      				$counter = 1; 
      				$minval = $mintime;
      				$mintitle = $titledisplay;
      			}
      		    elseif ($mintime < $minval){
      				$minval = $mintime;
      				$mintitle = $titledisplay;
 
				}	
				 		
			}
			
			/* End Checking posts for scheduled posts */


			/*Begin One Time Events*/
			$oneTimeEvent_count = 1;												
			$j=0;																								
																									
			$timer_results["oneTime"][$j] = array(	"date" => $minval,	//Date of the event converted to UNIX time
								"text" => $mintitle,													
			); 															
			
			$scottOptions = get_option("scott_values");//Get the options from the WPDB to make sure a fresh copy is being used
			
			$scottOptions['postTitle'] = $mintitle;
			$scottOptions['numberOfPosts'] = $testresult;

			update_option("scott_values", $scottOptions);//Update the WPDB for the options

			update_option("scott_database", $timer_results); //Update the WPDB for the data
			
			global $scott_timer_dates, $scott_timer_getOptions;
			$scott_timer_dates = get_option("scott_database"); //Get the events from the WPDB to make sure a fresh copy is being used
			$scott_timer_getOptions = get_option("scott_values");//Get the options from the WPDB to make sure a fresh copy is being used

}
add_action('save_post','predatefuturepost_update_post_meta');
?>