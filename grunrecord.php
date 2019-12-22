<?php
/*
Plugin Name: G-Run Record
Plugin URI: http://www.g-runth.com
Description: To record running statistics from an uploaded picture of a customer.
Version: 1.0.0
Author: Natthapong Noosing
Author URI: http://www.g-runth.com
*/
include_once( plugin_dir_path(__FILE__).'/get_stat_inc.php' );

function grun_record_requires_wordpress_version() {
	global $wp_version;
	$plugin = plugin_basename( __FILE__ );
	$plugin_data = get_plugin_data( __FILE__, false );
	if ( version_compare($wp_version, "3.3", "<" ) ) {
		if( is_plugin_active($plugin) ) {
			deactivate_plugins( $plugin );
			wp_die( "'".$plugin_data['Name']."' requires WordPress 3.3 or higher, and has been deactivated! Please upgrade WordPress and try again.<br /><br />Back to <a href='".admin_url()."'>WordPress admin</a>." );
		}
    }
    
}

add_action( 'init', 'grun_record_create_post_type' );
add_action( 'admin_init', 'grun_record_requires_wordpress_version' );
add_filter( 'template_include', 'grun_record_creating_template_include', 1 );
add_action( 'wp_enqueue_scripts', 'grun_record_theme_enqueue_scripts' );
add_shortcode( 'submit-grun-record', 'grun_record_form' );
register_activation_hook( __FILE__, 'grun_record_activation' );


/**
 * Enqueue scripts and styles
 */
function grun_record_theme_enqueue_scripts() {
     
    $css_file_url = plugins_url('/css/style.css',  __FILE__ );
    $js_file_url = plugins_url('/js/file.js',  __FILE__ );
    wp_enqueue_style( 'grun_record_css', $css_file_url);
    wp_enqueue_script( 'grun_record_js', $js_file_url, array('jquery'));
}


function grun_record_activation() {
    // Get access to global database access class
    global $wpdb;
    // Create table on main blog in network mode or single blog
    grun_record_create_table( $wpdb->get_blog_prefix() );
}

function grun_record_create_table($prefix){

    global $wpdb;
    $creation_record_query =
    'CREATE TABLE IF NOT EXISTS ' . $prefix . "grun_record (
    `record_id` BIGINT(20) unsigned NOT NULL,
    `uid` BIGINT(20) unsigned NOT NULL,
    `attachment_url` VARCHAR(2083),
    `distance_raw` DECIMAL(5, 2) DEFAULT NULL,
    `distance` DECIMAL(5, 2) DEFAULT 0,
    `time_in_seconds` int(11) NULL,
    `time_raw` VARCHAR(64) NULL,
    `created_time` DATETIME NOT NULL,
     PRIMARY KEY (`record_id`)
    );";
    
    $wpdb->query( $creation_record_query );

    $creation_event_record_query =
    'CREATE TABLE IF NOT EXISTS ' . $prefix . "grun_events_records (
    `record_id` BIGINT(20) unsigned NOT NULL,
    `eid` BIGINT(20) unsigned NOT NULL,
     PRIMARY KEY (`record_id`, `eid`)
    );";

    $wpdb->query( $creation_event_record_query );

}

function grun_record_create_post_type() {
    register_post_type( 'grun_record',
        array(
        'labels' => array(
        'name' => 'G-Run Record',
        'singular_name' => 'G-Run Record',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New G-Run Record',
        'edit' => 'Edit',
        'edit_item' => 'Edit G-Run Record',
        'new_item' => 'New G-Run Record',
        'view' => 'View',
        'view_item' => 'View BG-Run Record',
        'search_items' => 'Search G-Run Record',
        'not_found' => 'No G-Run Record found',
        'not_found_in_trash' =>
        'No G-Run Record found in Trash',
        'parent' => 'Parent Book Review'
        ),
        'public' => true,
        'menu_position' => 20,
        'supports' =>
        array( 'title', 'editor', 'comments',
        'thumbnail', 'custom-fields' ),
        'taxonomies' => array( '' ),
        'menu_icon' =>
        plugins_url( 'record-16x16.png', __FILE__ ),
        'has_archive' => true
        )
    );
}


function grun_record_creating_template_include( $template_path ) {
    if ( get_post_type() == 'grun_record' ) {
        if ( is_single() ) {
        // checks if the file exists in the theme first,
        // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array ( 'single-grun_record.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/single-grun_record.php';
            }
        }
    }
    return $template_path;
}

function grun_record_form() {
    // make sure user is logged in
    if ( !is_user_logged_in() ) {
        echo '<p>You need to be a site member to be able to ';
        echo 'submit book reviews. Sign up to gain access!</p>';
        return;
    }
    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }
    
    global $wpdb;
    $current_uid = get_current_user_id();
    $query = "SELECT em.eid, p.post_title from grun_event_member em INNER JOIN " . $wpdb->get_blog_prefix() 
    . "posts p ON p.ID = em.eid  WHERE em.uid = ". $current_uid;
    $events = $wpdb->get_results($query);

    $pluginpath = plugin_dir_path( __FILE__ );
    $imageurl = $pluginpath . '/img/no_image_png.png';
    $record = array(
        'distance' => 0.00,
        'time' => array(
            'hour' => 0,
            'minute' => 0,
            'second' => 0
        )

    );
    $distance = 0.00;
    $time = "00:00";
    $time_h = 0;
    $time_m = 0;
    $time_s = 0;
    $errors = array();
    // Upload file
	if(!empty($_FILES['run_result'] ) && $_POST['action'] == 'grun_record_veriify'){

        if($_FILES['run_result']['name'] != ''){
            $upload_overrides = array( 'test_form' => false );
            $uploaded_file = $_FILES['run_result'];
            $record = grun_get_stat_from_img($uploaded_file);

            $movefile = wp_handle_upload($uploaded_file, $upload_overrides);
            $imageurl = $movefile['url'];
        }
        else{
            $errors['main'] = "กรุณาแนบผลการวิ่ง";
            $event_summary == null;
            $_FILES['run_result']  = null;
        }
    }
    elseif($_POST['action'] == 'grun_record_send'){
        // Check that all required fields are present and non-empty
        if ( wp_verify_nonce( $_POST['grun_user_form'], 'add_record_form' ) &&
            !empty( $_POST['record_event'] ) &&
            !empty( $_POST['distance'] )   ) 
        {
            $now = date('Y-m-d H:i:s');
            $post_id = wp_insert_post(array (
                'post_type' => 'grun_record',
                'post_title' => ('ผลการวิ่ง '. $now ),
                'post_content' => ('วิ่งได้ระยะทาง ' .  $_POST['distance'] .' กิโลเมตร'),
                'post_status' => 'publish',
                'comment_status' => 'closed',   // if you prefer
                'ping_status' => 'closed',      // if you prefer
                
            ));
            $time = null;
            if( !empty( $_POST['time_h'] ) || !empty( $_POST['time_m'] ) || !empty( $_POST['time_s'] )){
                try {
                     $hour = intval($_POST['time_h']==null?0:$_POST['time_h']) * 60 *60;
                     $min  =  intval($_POST['time_m']==null?0:$_POST['time_h']) * 60;
                     $sec = intval($_POST['time_s']==null?0:$_POST['time_h']);
                     $time =  $hour + $min + $sec;
                } catch (Exception $e) {
                    echo 'Caught exception: ',  $e->getMessage(), "\n";
                }
            }
            $record_data = array(
                'record_id' => $post_id,
                'attachment_url' => urldecode($_POST['_attachment']),
                'distance_raw' => $_POST['distance_raw'],
                'distance' => $_POST['distance'],
                'time_raw' => $_POST['time_raw'],
                'created_time' => current_time('mysql', 1),
                'time_in_seconds' => $time,
                'uid' => get_current_user_id()
            );
            $wpdb->insert( $wpdb->get_blog_prefix() . 'grun_record', $record_data );

            $record_event_data = array(
                'record_id' => $post_id,
                'eid' =>  $_POST['record_event']
            );
            $wpdb->insert( $wpdb->get_blog_prefix() . 'grun_events_records', $record_event_data );

            // Store book author and rating
            $current_uid = get_current_user_id();
            $q = "SELECT u.ID as uid, em.target_distance, rc.total_distance, rc.total_times, em.bib_img_url, "
                 . "(em.target_distance - rc.total_distance) < 0 AS has_finished, rrc.distance_per_day "
                 . "FROM (SELECT r.uid, ger.eid, SUM(r.distance) as total_distance, SUM(r.time_in_seconds) as total_times "
                 . "FROM ". $wpdb->get_blog_prefix() ."grun_record r "
                 . "INNER JOIN wp_grun_events_records ger ON r.record_id = ger.record_id "
                 . "GROUP BY r.uid, ger.eid "
                 . " ) rc INNER JOIN wp_users u ON u.ID = rc.uid "
                 . "INNER JOIN grun_event_member em ON em.uid = u.ID "
                 . "INNER JOIN ( "
                 . "SELECT gr.uid, ger.eid, (SUM(gr.distance)/COUNT(distinct CAST(gr.created_time AS DATE))) AS distance_per_day "
                 . "FROM wp_grun_record gr "
                 . "INNER JOIN wp_grun_events_records ger ON gr.record_id = ger.record_id "
                 . "GROUP BY gr.uid, ger.eid "
                 . ") rrc ON rrc.eid = rc.eid AND rc.uid = rrc.uid "
                 ." WHERE rc.eid = " . $_POST['record_event'] 
                 ." AND em.uid = " .  get_current_user_id();              
            $event_summary = $wpdb->get_row($q);
       
        }
        else {
            // Display message if any required fields are missing
            $abortmessage = 'Some fields were left empty. Please ';
            $abortmessage .= 'go back and complete the form.';
            wp_die($abortmessage);

        }
    }

 ?>
 <?php if($event_summary == null) { ?>
<form method="post" action="" enctype='multipart/form-data'>
<!-- Nonce fields to verify visitor provenance -->
<?php if(!empty($errors)) { ?>
<div class="alert alert-danger" role="alert">
 <?php echo $errors['main']; ?>
</div>
<?php } ?>
<?php wp_nonce_field( 'add_record_form', 'grun_user_form' ); ?>
 
<?php if(empty($_FILES['run_result'] )){ ?>
      <div class="wcp-form-group">
        <div class="file-upload">
            <div class="image-upload-wrap">
                <input class="file-upload-input" name="run_result"  type='file' onchange="readURL(this);" accept="image/png, image/jpeg, image/gif" />
                <div class="drag-text">
                <h3>ลากรูปภาพมาวางที่นี่หรือกดเพื่อเลือกรูป</h3>
                </div>
            </div>
            <div class="file-upload-content">
                <div class="card" style="width: 18rem;">
                     <img src="#" alt="your image" class="file-upload-image card-img-top" />
                </div>
                <div class="image-title-wrap">
                <div>
                    <span class="image-title">Uploaded Image</span>
                </div>  
                <button type="button" onclick="removeUpload()" class="button wcp-button-confirm">ยกเลิก</button>

                <input type="hidden" name="action" value="grun_record_veriify">
                <button type="submit" class="button wcp-button-confirm"><span>ตรวจสอบผล</span></button>
                </div>
            </div>
        </div>
     </div>
<?php } ?>
<?php if(!empty($_FILES['run_result'] ) && $_FILES['run_result']['name'] != ''){ ?>

    <div class="wcp-form-group">
        <label for="wcp-event" class="wcp-form-label">รายการวิ่ง</label>
        <select id="wcp-event" name="record_event" class="wcp-form-control" >
            <?php 
            foreach ($events as $event) { ?>
            <option value="<?php echo $event->eid; ?>">
            <?php echo $event->post_title; ?>
            <?php } ?>
        </select>
    </div>
    <div class="wcp-form-group">
        <label for="wcp-distance" class="wcp-form-label">ระยะทางที่วิ่ง (กม.)</label>
        <input type="hidden" name="distance_raw" value="<?php echo $record['distance']; ?>">
        <input type="text" id="wcp-distance" name="distance" class="wcp-form-control" value="<?php echo $record['distance']; ?>"/>
    </div>
    <div class="wcp-form-group">
        <label for="wcp-time" class="wcp-form-label">เวลาที่ใช้วิ่ง </label>
        <div class="input-group">
            <input type="hidden" name="time_raw" value="<?php echo $time; ?>">
            <input class="wcp-form-control" type="number" name="time_h" aria-label="ชั่วโมง" placeholder="ชั่วโมง" class="wcp-form-control" value="<?php echo $record['time']['hour']; ?>"/>
            <input class="wcp-form-control" type="number" name="time_m" aria-label="นาที" placeholder="นาที" class="wcp-form-control" value="<?php echo $record['time']['minute']; ?>"/>
            <input class="wcp-form-control" type="number" name="time_s" aria-label="วินาที" placeholder="วินาที" class="wcp-form-control" value="<?php echo $record['time']['second']; ?>"/>
        </div>
    </div>
<?php } ?>
<?php if(empty($_FILES['run_result'] )){ ?>
    
<?php } ?>   
<?php if(!empty($_FILES['run_result'] )){ ?>
    <div class="wcp-form-group">
        <input type="hidden" name="_attachment" value="<?php echo  urlencode($imageurl);?>">
        <input type="hidden" name="action" value="grun_record_send">
        <button type="submit" class="button wcp-button-confirm"><span>แจ้งผลการวิ่ง</span></button>
    </div>
    <br/>
    <div class="wcp-form-group">
         <label for="wcp-result" class="wcp-form-label">รูปแนบ</label>
         <div class="card" style="width: 18rem;">
          <img src="<?php echo $imageurl;?>"  class="card-img-top" />
        </div>
    </div>
<?php } ?>   
</form>
 <?php }
 
 else{ 
    
    include( plugin_dir_path( __FILE__ ) . 'summary_inc.php');
 }
}
?>