<?php
/*
Plugin Name: G-Run Record
Plugin URI: http://www.g-runth.com
Description: To record running statistics from an uploaded picture of a customer.
Version: 1.0.0
Author: Natthapong Noosing
Author URI: http://www.g-runth.com
*/
ob_clean();
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
add_shortcode( 'table-individual-history', 'table_individual_event_history' );
add_shortcode( 'dashboard-event-performance', 'dashboard_event_performance' );
add_shortcode( 'dashboard-event-individual-performance', 'dashboard_event_individual_performance' );
add_shortcode( 'dashboard-event-leaderboard', 'dashboard_event_leaderboard' );
register_activation_hook( __FILE__, 'grun_record_activation' );


/**
 * Enqueue scripts and styles
 */
function grun_record_theme_enqueue_scripts() {
     
    $css_file_url = plugins_url('/css/style.css',  __FILE__ );
    $js_file_url = plugins_url('/js/file.js',  __FILE__ );
    wp_enqueue_style( 'grun_record_css', $css_file_url, array(), '1.0.2');
    wp_enqueue_script( 'grun_record_js', $js_file_url, array('jquery'), '1.0.0');
}


function grun_record_activation() {
    // Get access to global database access class
    global $wpdb;
    // Create table on main blog in network mode or single blog
    grun_record_create_table( $wpdb->get_blog_prefix() );
}

function grun_record_create_table($prefix){

    $creation_record_query =
    'CREATE TABLE ' . $prefix . "grun_record (
    `record_id` BIGINT(20) unsigned NOT NULL,
    `uid` BIGINT(20) unsigned NOT NULL,
    `attachment_url` VARCHAR(2083),
    `distance_raw` DECIMAL(5, 2) DEFAULT NULL,
    `distance` DECIMAL(5, 2) DEFAULT 0,
    `time_in_seconds` int(11) NULL,
    `time_raw` VARCHAR(64) NULL,
    `status` int(3) NOT NULL DEFAULT 0,
    `rejected_reason` VARCHAR(255) DEFAULT NULL,
    `created_time` DATETIME NOT NULL,
     PRIMARY KEY (`record_id`)
    );";
    
  //  $wpdb->query( $creation_record_query );

    $creation_event_record_query =
    'CREATE TABLE ' . $prefix . "grun_events_records (
    `record_id` BIGINT(20) unsigned NOT NULL,
    `eid` BIGINT(20) unsigned NOT NULL,
     PRIMARY KEY (`record_id`, `eid`)
    );";

  //  $wpdb->query( $creation_event_record_query );
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $creation_record_query );
    dbDelta( $creation_event_record_query );
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
        'rewrite' => array('slug' => 'grun-records'),
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

function dashboard_event_performance($atts){

    extract( shortcode_atts( array(
        'gid' => ''
    ), $atts ) );

    include( plugin_dir_path( __FILE__ ) . 'dashboard_event_performance_inc.php');
}

function table_individual_event_history($atts){

    extract( shortcode_atts( array(
        'gid' => ''
    ), $atts ) );

    include( plugin_dir_path( __FILE__ ) . 'individual_record_history_inc.php');
}
function dashboard_event_individual_performance($atts){

    extract( shortcode_atts( array(
        'gid' => ''
    ), $atts ) );

    include( plugin_dir_path( __FILE__ ) . 'dashboard_event_individual_performance_inc.php');
}


function dashboard_event_leaderboard($atts){

    extract( shortcode_atts( array(
        'gid' => '',
        'target' => '',
        'title' => '',
        'limit' => ''
    ), $atts ) );

    include( plugin_dir_path( __FILE__ ) . 'dashboard_event_leaderboard_inc.php');
}


function grun_record_form() {
    // make sure user is logged in
    if ( !is_user_logged_in() ) {
        echo '<p>การส่งผลการวิ่ง จำเป็นต้องเข้าสู่ระบบก่อน</p>';
        return;
    }
    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }
    
    global $wpdb;
    $current_uid = get_current_user_id();
    $query = "SELECT em.eid, p.post_title from grun_event_member em INNER JOIN " . $wpdb->get_blog_prefix() 
    . "posts p ON p.ID = em.eid  "
    . "INNER JOIN wp_postmeta pm ON pm.post_id = p.ID AND pm.meta_key = '_allow_send_stat'"
    . " WHERE em.uid = ". $current_uid
    . " AND pm.meta_value = '1' ";
    $events = $wpdb->get_results($query);

    if(empty($events)){
        echo '<p>การส่งผลการวิ่ง จำเป็นต้องสมัครรายการวิ่งก่อน</p>';
        return;
    }

    $pluginpath = plugin_dir_path( __FILE__ );
    $imageurl = plugins_url('/img/no_image_png.png',  __FILE__ ); 
    $no_img = plugins_url('/img/no_image_png.png',  __FILE__ ); 
    $record = array(
        'distance' => 0.00,
        'time' => array(
            'hour' => 0,
            'minute' => 0,
            'second' => 0
        )

    );

    $current_step = 1;

    $distance = 0.00;
    $time = "00:00";
    $errors = array();
    // Upload file
	if($_POST['action'] == 'grun_record_veriify'){

        if($_FILES['run_result']['name'] != ''){
            $upload_overrides = array( 'test_form' => false );
            $uploaded_file = $_FILES['run_result'];
            $record = grun_get_stat_from_img($uploaded_file);

            $movefile = wp_handle_upload($uploaded_file, $upload_overrides);
            $imageurl = $movefile['url'];

            $current_step = 2;
        }
        else{
            $errors['main'] = "กรุณาแนบผลการวิ่ง";
            $event_summary == null;
            $_FILES['run_result']  = null;

            $current_step = 1;
        }
    }
    // Verify
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
                     $min  =  intval($_POST['time_m']==null?0:$_POST['time_m']) * 60;
                     $sec = intval($_POST['time_s']==null?0:$_POST['time_s']);
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
            echo "<div class='row'> ระบบกำลังประมวลผลกรุณารอสักครู่....</div>";
            $redirectaddress =  get_permalink( $post_id); 
            ?>
            <script>window.location="<?php echo $redirectaddress;?>";</script>
            <?php
            exit();
        }
        else {
            // Display message if any required fields are missing
            if(empty( $_POST['distance'])){
                $errors['main'] = "กรุณากรอกระยะทาง";
            } 
            $current_step = 2;
        }
    } // End of Step 2

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
 
<?php if( $current_step == 1){ ?>
    <div class="row">

    </div>
    <div class="row">
      <div class="wcp-form-group">
        <div class="file-upload">
            <div class="image-upload-wrap">
                <input class="file-upload-input" name="run_result"  type='file' onchange="readURL(this);" accept="image/png, image/jpeg, image/gif" />
                <div class="drag-text">
                <h3>ลากรูปภาพมาวางที่นี่หรือกดเพื่อเลือกรูป</h3>
                </div>
            </div>
            <div class="file-upload-content">
                <div style="width: 18rem;">
                     <img src="<?php echo $no_img;?>" alt="your image" class="file-upload-image card-img-top" />
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
    </div>
<?php } ?>
<?php if($current_step == 2){ ?>
    <div class="row">
        <div class="grun-col-md-6">
            <div class="wcp-form-group">
                <label for="wcp-result" class="wcp-form-label">รูปแนบ</label>
                <div class="card attach-image" >
                <img src="<?php echo $imageurl;?>"  class="card-img-top" />
                </div>
            </div>
        </div>
        <div class="grun-col-md-6">
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
            <div class="wcp-form-group">
                <input type="hidden" name="_attachment" value="<?php echo  urlencode($imageurl);?>">
                <input type="hidden" name="action" value="grun_record_send">
                <button type="submit" class="button wcp-button-confirm"><span>แจ้งผลการวิ่ง</span></button>
            </div>
        </div>
    </div>

    
<?php } ?>   
</form>
 <?php }
 
 else{ 
    
  //  include( plugin_dir_path( __FILE__ ) . 'summary_inc.php');
 }
}
?>