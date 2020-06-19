<?php
    global $wpdb;


    $query = "SELECT u.ID as uid, em.eid, em.target_distance, rc.total_distance, rc.total_times, em.bib_img_url, "
        . "(em.target_distance - rc.total_distance) < 0 AS has_finished, rrc.distance_per_day "
        . "FROM (SELECT r.uid, ger.eid, SUM(r.distance) as total_distance, SUM(r.time_in_seconds) as total_times "
        . "FROM ". $wpdb->get_blog_prefix() ."grun_record r "
        . "INNER JOIN ". $wpdb->get_blog_prefix() ."grun_events_records ger ON r.record_id = ger.record_id "
        . " WHERE r.status = 1 " // 1 = Approved
        . "GROUP BY r.uid, ger.eid "
        . " ) rc INNER JOIN wp_users u ON u.ID = rc.uid "
        . "INNER JOIN grun_event_member em ON em.uid = u.ID "
        . "INNER JOIN ( "
        . "SELECT gr.uid, ger.eid, (SUM(gr.distance)/COUNT(distinct CAST(gr.created_time AS DATE))) AS distance_per_day "
        . "FROM ". $wpdb->get_blog_prefix() ."grun_record gr "
        . "INNER JOIN ". $wpdb->get_blog_prefix() ."grun_events_records ger ON gr.record_id = ger.record_id "
        . " WHERE gr.status = 1 " // 1 = Approved
        . "GROUP BY gr.uid, ger.eid "
        . ") rrc ON rrc.eid = rc.eid AND rc.uid = rrc.uid " 
        ." WHERE rc.uid  = " . get_current_user_id();         
        print($query)  
    $event_summary = $wpdb->get_row($query);



    // ----------------------------

    $event_id = $event_summary->eid; 

    $stdate = get_post_meta($event_id, 'start_date', true);
    $fndate = get_post_meta($event_id, 'end_date', true);

    $std = date_create(date("Y-m-d", strtotime($stdate)));
    $fnd = date_create(date("Y-m-d", strtotime($fndate )));

     
    $today = date_create();

    //difference between two dates
    $total_days = date_diff($std, $fnd);
    $pass_days = date_diff($std, $today);

    $progress_days_percentage = 0;
    if($today >= $std){
        $progress_days_percentage = ($pass_days->format("%a")/$total_days->format("%a"))*100;
    }
    elseif($today>=$fnd){
        $progress_days_percentage = 100;
    }
    $remaining_days = $total_days->format("%a") - $pass_days->format("%a");

?>
<div class="row">

    <div class="grun-col-md-3 grun-col-sm-6 grun-col-xs-12">
        <div class="card mb-3 bg-total-distance grun-widget-chart text-green card-border">
            <div class="icon-wrapper rounded-circle">
                
            </div>
            <div class="widget-numbers"><span class="rounded"><?php echo number_format($event_summary->total_distance, 2); ?> km</span></div>
            <div class="widget-subheading">ระยะทางสะสม</div>
            <div class="widget-description"><span class="pl-1">&nbsp</span></div>
        </div>
    </div>
    
    <div class="grun-col-md-3 grun-col-sm-6 grun-col-xs-12">
        <div class="card mb-3 bg-total-time grun-widget-chart text-green card-border">
            <div class="icon-wrapper rounded">
                <!-- <div class="icon-wrapper-bg bg-dark opacity-9"></div><i
                    class="lnr-graduation-hat text-white"></i>-->
            </div>
            <?php
                $total_time = 0;
                if($event_summary->total_times !=null && $event_summary->total_times > 0) {
                    $total_time = gmdate("H:i:s", $event_summary->total_times); 
                }
            ?>
            <div class="widget-numbers"><span class="rounded"><?php echo $total_time; ?></span></div>
            <div class="widget-subheading">ระยะเวลาสะสม</div>
            <div class="widget-description">
                <span class="pl-1">&nbsp;</span>
            </div>
        </div>
    </div>
    <div class="grun-col-md-3 grun-col-sm-6 grun-col-xs-12">
        <div class="card mb-3 bg-remain-distnace grun-widget-chart text-green card-border">
            <div class="icon-wrapper rounded-circle">
                
            </div>
            <div class="widget-numbers"><span class="rounded"><?php echo number_format($event_summary->distance_per_day, 2); ?> km</span></div>
            <div class="widget-subheading">ระยะทางเฉลี่ย</div>
            <div class="widget-description"><span class="pl-1">ที่วิ่งสะสมได้ต่อวัน</span></div>
        </div>
    </div>
    <div class="grun-col-md-3 grun-col-sm-6 grun-col-xs-12">
        <div class="card mb-3 bg-pace grun-widget-chart text-green card-border">
            <div class="icon-wrapper rounded">
                <!-- <div class="icon-wrapper-bg bg-dark opacity-9"></div><i
                    class="lnr-graduation-hat text-white"></i>-->
            </div>
            <?php
                $avg_pace = 0;
                if($event_summary->total_times != null && $event_summary->total_times > 0) {
                    $time_in_minute = ($event_summary->total_times/60);
                    $avg_pace = ($time_in_minute/$event_summary->total_distance); 
                }
            ?>
            <div class="widget-numbers"><span class="rounded"><?php echo number_format($avg_pace , 2, ".", "" );?></span></div>
            <div class="widget-subheading">เพชเฉลี่ยที่วิ่งได้</div>
            <div class="widget-description"><span class="pl-1">&nbsp;</span></div>
        </div>
    </div>

</div>