<?php
    global $wpdb;
    $query = "SELECT a.eid, "
        ."SUM(a.total_distance) as total_distance, "
        ."SUM(a.total_times) as total_times, "
        ."count(a.uid) as total_register, "
        ."SUM(CASE when a.total_distance >= a.target_distance then 1 else 0 end) AS total_finisher, "
        ."SUM(CASE when  (a.total_distance/a.target_distance) >= 0.8 AND (a.total_distance/a.target_distance) < 1  then 1 else 0 end) AS total_early_finisher, "
        ." SUM(CASE when  (a.total_distance/a.target_distance) < 0.8 AND (a.total_distance/a.target_distance) < 1  then 1 else 0 end) AS total_far_finisher "
        ."FROM (SELECT rc.uid, rc.eid, rc.total_distance, rc.total_times, em.target_distance "
        ."FROM ( SELECT r.uid, ger.eid, SUM(r.distance) as total_distance, SUM(r.time_in_seconds) as total_times "
        ."FROM wp_grun_record r "
        ."INNER JOIN wp_grun_events_records ger ON r.record_id = ger.record_id "
        ."GROUP BY r.uid, ger.eid "
        .") rc  "
        ."INNER JOIN grun_event_member em ON em.eid = rc.eid AND rc.uid = em.uid ) a "
        ."WHERE a.eid IN ( "
        ."SELECT p.ID  "
        ."FROM wp_posts p  "
        ."INNER JOIN wp_postmeta pm ON pm.post_id = p.ID "
        ."WHERE pm.meta_key = 'grun_ebib_pgid' "
        ."AND pm.meta_value = '$gid' ) "
        ."GROUP BY a.eid ";
    $event_stat = $wpdb->get_row($query);

 
?>
<div class="row">
    <div class="grun-col-md-12">
        <div class="row">
            <div class="grun-col-md-4">
                <div class="card mb-3 bg-pace grun-widget-chart text-green card-border">
                    <div class="icon-wrapper rounded-circle">
                        
                    </div>
                    <div class="widget-numbers"><span class="rounded"><?php echo $event_stat->total_distance; ?> km</span></div>
                    <div class="widget-subheading">ระยะทางสะสม</div>
                </div>
            </div>
            <div class="grun-col-md-4">
                <div class="card mb-3 bg-total-time grun-widget-chart text-green card-border">
                    <div class="icon-wrapper rounded-circle">
                        
                    </div>
                    <div class="widget-numbers"><span class="rounded"><?php echo gmdate("H:i:s", $event_stat->total_times); ?></span></div>
                    <div class="widget-subheading">ระยะเวลาสะสม</div>
                </div>
            </div>
            <div class="grun-col-md-4">
                <div class="card mb-3 bg-pace grun-widget-chart text-green card-border">
                    <div class="icon-wrapper rounded-circle">
                        
                    </div>
                    <div class="widget-numbers"><span class="rounded"><?php echo $event_stat->total_register; ?> คน</span></div>
                    <div class="widget-subheading">จำนวนผู้สมัคร</div>
                </div>
            </div>
            <div class="grun-col-md-4">
                <div class="card mb-3 bg-remain-distnace grun-widget-chart text-green card-border">
                    <div class="icon-wrapper rounded">
                        <!-- <div class="icon-wrapper-bg bg-white opacity-10"></div><i
                            class="lnr-screen icon-gradient bg-warm-flame"></i>--> 
                    </div>

                    <div class="widget-numbers"><span class="rounded"><?php echo $event_stat->total_finisher;  ?> คน</span></div>
                    <div class="widget-subheading">ที่บรรลุเป้าหมาย</div>
                </div>
            </div>
            <div class="grun-col-md-4">
                <div class="card mb-3 bg-pace grun-widget-chart text-green card-border">
                    <div class="icon-wrapper rounded">
                        <!-- <div class="icon-wrapper-bg bg-dark opacity-9"></div><i
                            class="lnr-graduation-hat text-white"></i>-->
                    </div>
                   
                    <div class="widget-numbers"><span class="rounded"><?php echo $event_stat->total_early_finisher;  ?> คน</span></div>
                    <div class="widget-subheading">ที่ใกล้บรรลุเป้าหมาย</div>
                </div>
            </div>
            <div class="grun-col-md-4">
                <div class="card mb-3 bg-distance-per-day grun-widget-chart text-red card-border">
                    <div class="icon-wrapper rounded">
                        <!-- <div class="icon-wrapper-bg bg-dark opacity-9"></div><i
                            class="lnr-graduation-hat text-white"></i>-->
                    </div>
                   
                    <div class="widget-numbers"><span class="rounded"><?php echo $event_stat->total_far_finisher;  ?> คน</</span></div>
                    <div class="widget-subheading">ที่วิ่งได้น้อยกว่า 80%</div>
                    <div class="widget-description">
                        
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
