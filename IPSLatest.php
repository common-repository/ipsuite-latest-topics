<?php
/* Plugin Name: IPS 4 Lastest Topics 
Plugin URI: http://Creatives.ir/
Description: Allows You to Show Your IPS 4 Lastest Topics in Your Blog Just With Optimizing Some Easy Steps ...... + Much more Flexiblity
Version: 1.0
Author: ZeroBurnner
Author URI: http://Creatives.com/
License: GPLv2 or later
*/

if( !function_exists('ipslatest_main') ){
  function ipslatest_main($query)
  {
    if ( is_home() && $query->is_main_query() ) {
    $db_user = get_option('ipslatest_db_user');
    $db_name = get_option('ipslatest_db_name');
    $db_pass = get_option('ipslatest_db_pw');
    $db_host = get_option('ipslatest_db_host');
    $prefix = get_option('ipslatest_db_prefix');
    $url = get_option('ipslatest_url');
    $limit = get_option('ipslatest_limit');
    $title = get_option('ipslatest_title');
    $width = get_option('ipslatest_width');
    $direction = get_option('ipslatest_direction');
    /* Translations */
    $translations = array(get_option('ipslatest_translate_topic'),get_option('ipslatest_translate_views'),get_option('ipslatest_translate_time'),get_option('ipslatest_translate_sender'));
    /*$translationTopic =
    $translationViews =
    $translationTime =
    $translationSender =*/
    /* Translations */
    $db = new wpdb( $db_user, $db_pass, $db_name,$db_host );
  $q = $db->get_results( 'SELECT topic_id,author_name FROM '.$prefix.'forums_posts ORDER BY pid DESC LIMIT '.$limit );
  echo '<div id="ipslatest-mainTitle" style="width:'.$width.'">'.$title.$position.'</div>';
  echo '<table id="ipslatestTopics" border=0 style="padding:10px;width:'.$width.'">';
  if($direction == "ltr"){
    echo '<tr class="ipslatest-first-row"> <td class="ipslatest-row-title">'.$translations[0].'</td><td class="ipslatest-row-sender">'.$translations[3].'</td><td class="ipslatest-row-views">'.$translations[1].'</td><td class="ipslatest-row-answers">'.$translations[2].'</td> </tr>';
    foreach ($q as $key => $row) {
      $topicDetails = $db->get_row('SELECT posts,title,tid,views FROM '.$prefix.'forums_topics WHERE tid='.$row->topic_id);
      $userMemberID = $db->get_row('SELECT member_group_id FROM '.$prefix."core_members WHERE name='".$row->author_name."'");
      $userShowingData = $db->get_row('SELECT prefix,suffix FROM core_groups WHERE g_id='.$userMemberID->member_group_id);
      echo '<tr id="ipstopic"> <td class="class="ipslatest-row-title-content"><a rel="follow" class="ipstitles" href="'.$url.'/index.php?/topic/'.$topicDetails->tid.'-'.strtolower(str_replace(" ","-",$topicDetails->title)).'">'. strip_tags($topicDetails->title) .' </a> </td> <td class="ipslatest-row-sender-content"> '.$userShowingData->prefix.$row->author_name.$userShowingData->suffix.' </td> <td class="ipslatest-row-views-content"><span> '.$topicDetails->views.' </span> </td> <td class="ipslatest-row-answers-content"><span> '.$topicDetails->posts.'</span> </td> </tr>';
    }
  }elseif($direction === "rtl"){
    echo '<tr><td class="ipslatest-row-views">'.$translations[2].'</td><td class="ipslatest-row-answers">'.$translations[1].'</td><td class="ipslatest-row-sender">'.$translations[3].'</td><td class="ipslatest-row-title">'.$translations[0].'</td> </tr>';
    foreach ($q as $key => $row) {
      $topicDetails = $db->get_row('SELECT title,tid,views,posts FROM '.$prefix.'forums_topics WHERE tid='.$row->topic_id);
      $userMemberID = $db->get_row('SELECT member_group_id FROM '.$prefix."core_members WHERE name='".$row->author_name."'");
      $userShowingData = $db->get_row('SELECT prefix,suffix FROM core_groups WHERE g_id='.$userMemberID->member_group_id);
      echo '<tr id="ipstopic"> <td class="ipslatest-row-views-content"> <span>'.$topicDetails->views.' </span></td> <td class="ipslatest-row-answers-content"><span>'.$topicDetails->posts.'</span></td> <td id="ipslatest-row-sender-content">'.$userShowingData->prefix.$row->author_name.$userShowingData->suffix.'</td> <td class="ipsTitle"> <a rel="follow" href="'.$url.'/index.php?/topic/'.$topicDetails->tid.'-'.strtolower(str_replace(" ","-",$topicDetails->title)).'">'. strip_tags($topicDetails->title) .' </a> </td> </tr>';
    }
  }
  echo '</table>';
  } 
}
}
add_action( 'wp_enqueue_scripts', 'register_plugin_styles' );
function register_plugin_styles() {
  wp_register_style( 'ipslatestCSS', plugin_dir_url( __FILE__ ).'css/main.css');
  wp_enqueue_style( 'ipslatestCSS' );
}
add_action( 'wp', 'ipslatestMain' );
function ipslatestMain( $query ) {
   $position = get_option('ipslatest_position');
   if($position === "bottom")
    $acton = "loop_end";
   elseif ($position === "top")
    $acton = "loop_start";
   add_action($acton,"ipslatest_main");
}
add_action( 'admin_menu', 'ips4lastest_menu' );
if( !function_exists('ips4lastest_menu') )
{
function ips4lastest_menu(){
  $page_title = 'WordPress IPS 4 Lastest Topic';
  $menu_title = 'IPS 4 Lastest Topics';
  $capability = 'manage_options';
  $menu_slug  = 'IPS-Lastest-Topics-By-ZeroBurnner';
  $function   = 'ips4lastest_admin';
  $icon_url   = 'dashicons-media-code';
  $position   = 4;

  add_menu_page( $page_title,
                 $menu_title, 
                 $capability, 
                 $menu_slug, 
                 $function, 
                 $icon_url, 
                 $position );
  add_action( 'admin_init', 'update_ips4lastest_info' );
}
}
if( !function_exists('ips4lastest_admin') )
{
function ips4lastest_admin(){
?>
  <h1>Wordpress IPS 4 Lastest Topics ...</h1>
  <form method="post" action="options.php">
    <?php settings_fields( 'ips4lastest-settings' ); ?>
    <?php do_settings_sections( 'ips4lastest-settings' ); ?>
    <table class="form-table" style="margin:0 auto;">
      <tr valign="top">
      <th scope="row">Database Host</th>
      <td><input type="text" name="ipslatest_db_host" value="<?php echo htmlspecialchars(get_option('ipslatest_db_host')); ?>"/></td><br>
      </tr>
      <tr>
      <th scope="row">Database Username</th>
      <td><input type="text" name="ipslatest_db_user" value="<?php echo htmlspecialchars(get_option('ipslatest_db_user')); ?>"/></td>
      </tr>
      <tr>
      <th scope="row">Database Name</th>
      <td><input type="text" name="ipslatest_db_name" value="<?php echo htmlspecialchars(get_option('ipslatest_db_name')); ?>"/></td>
      </tr>
      <tr>
       <th scope="row">Database Password</th>
      <td><input type="password" name="ipslatest_db_pw" value="<?php echo htmlspecialchars(get_option('ipslatest_db_pw')); ?>"/></td>
      </tr>
      <tr>
       <th scope="row"> IPS Database's Prefix </th>
      <td><input type="text" name="ipslatest_db_prefix" value="<?php echo htmlspecialchars(get_option('ipslatest_db_prefix')); ?>"/></td>
      </tr>
      <tr>
       <th scope="row"> Main Title </th>
      <td><input type="text" name="ipslatest_title" value="<?php echo htmlspecialchars(get_option('ipslatest_title')); ?>"/></td>
      </tr>
      <tr>
       <th scope="row"> IPS URL [ For Generating Links ] </th>
      <td><input type="text" name="ipslatest_url" value="<?php echo htmlspecialchars(get_option('ipslatest_url')); ?>"/></td>
      </tr>
       <tr>
       <th scope="row"> Main Width <small>!important</small> </th>
      <td><input type="text" name="ipslatest_width" value="<?php echo htmlspecialchars(get_option('ipslatest_width')); ?>"/></td>
      </tr>
       <tr>
       <th scope="row"> Postion</th>
      <td>
        <input type="radio" id="[ID]" name="ipslatest_position" <?php if(get_option('ipslatest_position') == 'top') echo 'checked="checked"'; ?> value="top" />Top<small> Before Blog's Posts</small>
        <input type="radio" id="[ID]" name="ipslatest_position" <?php if(get_option('ipslatest_position') == 'bottom') echo 'checked="checked"'; ?> value="bottom" />Bottom<small> After Blog's Posts</small>
      </td>
      </tr>
       <tr>
       <th scope="row"> Limit <small> To Show How many Results</small> </th>
      <td>
        <input type="number " id="[ID]" name="ipslatest_limit" value="<?php echo htmlspecialchars(get_option('ipslatest_limit')); ?>" />
      </td>
      </tr>
      <th scope="row"> Direction </th>
      <td>
        <input type="radio" id="[ID]" name="ipslatest_direction" <?php if(get_option('ipslatest_direction') == 'rtl') echo 'checked="checked"'; ?> value="rtl" />RTL<small> Shows From Right To Left</small>
    <input type="radio" id="[ID]" name="ipslatest_direction" <?php if(get_option('ipslatest_direction') == 'ltr') echo 'checked="checked"'; ?> value="ltr" />LTR<small> Shows From Left To Right</small>
      </td>
      </tr>
    </table>
    <h2> Language Options </h2>
    <table>
     <tr>
      <th class="row"> " Last Post By " Translation : </th>
      <td> <input type="text" name="ipslatest_translate_sender" value="<?php echo htmlspecialchars(get_option('ipslatest_translate_sender')); ?>">  </td>
     </tr>
     <tr>
      <th class="row"> " Views " Translation : </th>
      <td> <input type="text" name="ipslatest_translate_views" value="<?php echo htmlspecialchars(get_option('ipslatest_translate_views')); ?>">  </td>
     </tr>
     <tr>
      <th class="row"> " Title " Translation : </th>
      <td> <input type="text" name="ipslatest_translate_topic" value="<?php echo htmlspecialchars(get_option('ipslatest_translate_topic')); ?>">  </td>
     </tr>
     <tr>
      <th class="row"> " Answers " Translation : </th>
      <td> <input type="text" name="ipslatest_translate_time" value="<?php echo htmlspecialchars(get_option('ipslatest_translate_time')); ?>">  </td>
     </tr>
    </table>
    <?php submit_button(); ?>
  </form>

<?php
}
}
if( !function_exists('update_ips4lastest_info') )
{
function update_ips4lastest_info() { 
    register_setting( 'ips4lastest-settings', 'ipslatest_db_host' );
    register_setting( 'ips4lastest-settings', 'ipslatest_db_user' );
    register_setting( 'ips4lastest-settings', 'ipslatest_db_name' );
    register_setting( 'ips4lastest-settings', 'ipslatest_db_pw' );
    register_setting( 'ips4lastest-settings', 'ipslatest_db_prefix' );
    register_setting( 'ips4lastest-settings', 'ipslatest_title' );
    register_setting( 'ips4lastest-settings', 'ipslatest_url' );
    register_setting( 'ips4lastest-settings', 'ipslatest_width' );
    register_setting( 'ips4lastest-settings', 'ipslatest_position' );
    register_setting( 'ips4lastest-settings', 'ipslatest_direction' );
    register_setting( 'ips4lastest-settings', 'ipslatest_translate_sender' );
    register_setting( 'ips4lastest-settings', 'ipslatest_translate_views' );
    register_setting( 'ips4lastest-settings', 'ipslatest_translate_topic' );
    register_setting( 'ips4lastest-settings', 'ipslatest_translate_time' );
    register_setting( 'ips4lastest-settings', 'ipslatest_limit' );
}
}