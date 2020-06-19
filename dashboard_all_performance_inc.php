<?php
    global $wpdb;
    $query = "SELECT count(evst.uid) AS total_register, " 
     ." SUM(evst.total_distance) AS total_distance, "
     ." SUM(evst.total_times) AS total_times, "
     ."  SUM(case when evst.has_finished = 1 then 1 else 0 end) as total_finisher, "
     ."  SUM(CASE when  (evst.total_distance/evst.target_distance) >= 0.8 AND (evst.total_distance/evst.target_distance) < 1  then 1 else 0 end) AS total_early_finisher, "
     ."  SUM(case when evst.has_finished = 0 then 1 else 0 end) as total_unfinisher "
     ."  FROM (  "
     ."SELECT u.ID as uid, rc.eid, em.target_distance, rc.total_distance, rc.total_times,"
        ."      (em.target_distance - rc.total_distance) < 0 AS has_finished, rrc.distance_per_day "
        ."      FROM (SELECT r.uid, ger.eid, SUM(r.distance) as total_distance, SUM(r.time_in_seconds) as total_times "
        ."      FROM  wp_grun_record r "
        ."      INNER JOIN wp_grun_events_records ger ON r.record_id = ger.record_id "
        ."      GROUP BY r.uid, ger.eid "
        ."      ) rc INNER JOIN wp_users u ON u.ID = rc.uid "
        ."      INNER JOIN grun_event_member em ON em.uid = u.ID "
        ."      INNER JOIN ( SELECT gr.uid, ger.eid, (SUM(gr.distance)/COUNT(distinct CAST(gr.created_time AS DATE))) AS distance_per_day "
        ."  FROM wp_grun_record gr "
        ." INNER JOIN wp_grun_events_records ger ON gr.record_id = ger.record_id "
        ." GROUP BY gr.uid, ger.eid "
        ."        ) rrc ON rrc.eid = rc.eid AND rc.uid = rrc.uid   "
        ." ) AS evst";
      
        $event_stat = $wpdb->get_row($query);
        

?>
<div class="row">
    <div class="grun-col-md-12">
        <div class="row">
            <div class="grun-col-md-4 grun-col-sm-6 grun-col-xs-12">
                <div class="card mb-3 bg-pace grun-widget-chart text-green card-border">
                    <div class="icon-wrapper rounded-circle">
                        
                    </div>
                    <div class="widget-numbers"><span class="rounded"><?php echo $event_stat->total_register; ?> คน</span></div>
                    <div class="widget-subheading">จำนวนผู้สมัคร</div>
                </div>
            </div>
            <div class="grun-col-md-4 grun-col-sm-6 grun-col-xs-12">
                <div class="card mb-3 bg-total-distance grun-widget-chart text-green card-border">
                    <div class="icon-wrapper rounded-circle">
                        
                    </div>
                    <div class="widget-numbers"><span class="rounded"><?php echo $event_stat->total_distance; ?> km</span></div>
                    <div class="widget-subheading">ระยะทางสะสม</div>
                </div>
            </div>
            <?php
                $total_time = 0;
                if($event_stat->total_times !=null && $event_stat->total_times > 0) {
                    $total_time = gmdate("H:i:s", $event_stat->total_times); 
                }
            ?>

            <div class="grun-col-md-4 grun-col-sm-6 grun-col-xs-12">
                <div class="card mb-3 bg-total-time grun-widget-chart text-green card-border">
                    <div class="icon-wrapper rounded">
                        <!-- <div class="icon-wrapper-bg bg-white opacity-10"></div><i
                            class="lnr-screen icon-gradient bg-warm-flame"></i>--> 
                    </div>

                    <div class="widget-numbers"><span class="rounded"><?php echo $total_time;  ?></span></div>
                    <div class="widget-subheading">ระยะเวลาสะสม</div>
                </div>
            </div>

            <div class="grun-col-md-4 grun-col-sm-6 grun-col-xs-12">
                <div class="card mb-3 bg-remain-distnace grun-widget-chart text-green card-border">
                    <div class="icon-wrapper rounded">
                        <!-- <div class="icon-wrapper-bg bg-white opacity-10"></div><i
                            class="lnr-screen icon-gradient bg-warm-flame"></i>--> 
                    </div>

                    <div class="widget-numbers"><span class="rounded"><?php echo $event_stat->total_finisher;  ?> คน</span></div>
                    <div class="widget-subheading">ที่บรรลุเป้าหมาย</div>
                </div>
            </div>
            <div class="grun-col-md-4 grun-col-sm-6 grun-col-xs-12">
                <div class="card mb-3 bg-early-finisher grun-widget-chart text-green card-border">
                    <div class="icon-wrapper rounded">
                        <!-- <div class="icon-wrapper-bg bg-dark opacity-9"></div><i
                            class="lnr-graduation-hat text-white"></i>-->
                    </div>
                   
                    <div class="widget-numbers"><span class="rounded"><?php echo $event_stat->total_early_finisher;  ?> คน</span></div>
                    <div class="widget-subheading">ที่ใกล้บรรลุเป้าหมาย</div>
                </div>
            </div>
            <div class="grun-col-md-4 grun-col-sm-6 grun-col-xs-12">
                <div class="card mb-3 bg-distance-per-day grun-widget-chart text-red card-border">
                    <div class="icon-wrapper rounded">
                        <!-- <div class="icon-wrapper-bg bg-dark opacity-9"></div><i
                            class="lnr-graduation-hat text-white"></i>-->
                    </div>
                   
                    <div class="widget-numbers"><span class="rounded"><?php echo $event_stat->total_unfinisher;  ?> คน</</span></div>
                    <div class="widget-subheading">ที่วิ่งไม่บรรลุเป้าหมาย 80%</div>
                    <div class="widget-description">
                        
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
