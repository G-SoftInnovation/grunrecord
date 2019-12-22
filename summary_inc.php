<?php
    $std = date_create("2019-12-25");
    $fnd = date_create("2020-01-25");
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
    <div class="grun-col-md-6 ">
        <div class="mb-3 card bg-main-stat">
            <div class="card-header-tab card-header">
                <div class="card-header-title"></div>
                <div class="target-distance-label"><span class="rounded text-green">เป้าหมาย <?php echo $event_summary->target_distance;?> km</span></div>
            </div>
            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="pt-2 card-body stat-body">

                        <div class="mt-5 row">
                            <div class="grun-col-md-12">
                                <div class="widget-content">
                                    <div class="widget-content-outer">
                                        <?php 
                                            $total_distance_percentage = ($event_summary->total_distance/$event_summary->target_distance)*100; 
                                            if( $total_distance_percentage>100){
                                                $total_distance_percentage = 100;
                                            }
                                            $remain_distance = ($event_summary->target_distance -  $event_summary->total_distance);
                                            if( $remain_distance<0){
                                                $remain_distance = 0;
                                            }
                                           
                                            $remain_distance_percentage = 100 - $total_distance_percentage; 
                                        ?>
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left mr-3">
                                                <div class="widget-numbers fsize-3 text-muted"><?php echo $total_distance_percentage;?>%</div>
                                            </div>
                                            <div class="widget-content-right">
                                                <div class="text-muted opacity-6">ระยะทางสะสม <?php echo $event_summary->total_distance;?> km</div>
                                            </div>
                                        </div>
                                        <div class="widget-progress-wrapper mt-1">
                                            <div class="progress-bar-sm progress-bar-animated-alt progress">
                                                <div class="progress-bar bg-success" role="progressbar"
                                                    aria-valuenow="<?php echo $total_distance_percentage; ?>" aria-valuemin="0" aria-valuemax="100"
                                                    style="width: <?php echo $total_distance_percentage; ?>%;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="divider mt-4"></div>
                        <div class="row">
                            <div class="grun-col-md-12">
                                <div class="widget-content">
                                    <div class="widget-content-outer">
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left mr-3">
                                                <div class="widget-numbers fsize-3 text-muted"><?php echo $remain_distance_percentage;?>%</div>
                                            </div>
                                            <div class="widget-content-right">
                                                <div class="text-muted opacity-6">ระยะทางที่เหลือ <?php echo $remain_distance;?> km</div>
                                            </div>
                                        </div>
                                        <div class="widget-progress-wrapper mt-1">
                                            <div class="progress-bar-sm progress-bar-animated-alt progress">
                                                <div class="progress-bar bg-danger" role="progressbar"
                                                    aria-valuenow="<?php echo $remain_distance_percentage;?>" aria-valuemin="0" aria-valuemax="100"
                                                    style="width: <?php echo $remain_distance_percentage;?>%;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="divider mt-4"></div>
                        <div class="row">
                            <div class="grun-col-md-12">
                                <div class="widget-content">
                                    <div class="widget-content-outer">
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-right">
                                                <div class="text-muted opacity-6">ระยะเวลาที่เหลืออีก <?php echo $remaining_days; ?> วัน</div>
                                            </div>
                                        </div>
                                        <div class="widget-progress-wrapper mt-1">
                                            <div class="progress-bar-sm progress-bar-animated-alt progress">
                                                <div class="progress-bar bg-primary" role="progressbar"
                                                    aria-valuenow="<?php echo $progress_days_percentage;?>" aria-valuemin="0" aria-valuemax="100"
                                                    style="width: <?php echo $progress_days_percentage;?>%;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="grun-widget-chart p-0">
                        <div class="grun-widget-chart-content">
                            <div class="widget-description mt-0 text-warning"></div>
                        </div>
                        <div class="recharts-responsive-container" style="width: 100%; height: 187px;">
                            
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
    <div class="grun-col-md-6">
        <div class="row">
            <div class="grun-col-md-6">
                <div class="card mb-3 bg-remain-distnace grun-widget-chart text-green card-border">
                    <div class="icon-wrapper rounded-circle">
                        
                    </div>
                    <div class="widget-numbers"><span class="rounded"><?php echo number_format($event_summary->distance_per_day, 2); ?> km</span></div>
                    <div class="widget-subheading">ระยะทางเฉลี่ย</div>
                    <div class="widget-description"><span class="pl-1">ที่วิ่งสะสมได้ต่อวัน</span></div>
                </div>
            </div>
            <div class="grun-col-md-6">
                <div class="card mb-3 bg-distance-per-day grun-widget-chart text-red card-border">
                    <div class="icon-wrapper rounded">
                        <!-- <div class="icon-wrapper-bg bg-white opacity-10"></div><i
                            class="lnr-screen icon-gradient bg-warm-flame"></i>--> 
                    </div>
                    <?php
                        $avg_remain_per_day = number_format($remain_distance/$remaining_days, 2);
                    ?>
                    <div class="widget-numbers"><span class="rounded"><?php echo $avg_remain_per_day; ?> km</span></div>
                    <div class="widget-subheading">ระยะทางเฉลี่ย</div>
                    <div class="widget-description">ที่ต้องวิ่งต่อวัน</div>
                </div>
            </div>
            <div class="grun-col-md-6">
                <div class="card mb-3 bg-pace grun-widget-chart text-green card-border">
                    <div class="icon-wrapper rounded">
                        <!-- <div class="icon-wrapper-bg bg-dark opacity-9"></div><i
                            class="lnr-graduation-hat text-white"></i>-->
                    </div>
                    <?php
                        $avg_pace = 0;
                        if($event_summary->total_times != null && $event_summary->total_times > 0) {
                            $time_in_minute = ($event_summary->total_times/60);
                            $avg_pace = ($event_summary->total_distance/$time_in_minute); 
                        }
                     ?>
                    <div class="widget-numbers"><span class="rounded"><?php echo number_format($avg_pace , 2, ":", "" );?></span></div>
                    <div class="widget-subheading">เพชเฉลี่ยที่วิ่งได้</div>
                    <div class="widget-description"></div>
                </div>
            </div>
            <div class="grun-col-md-6">
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
                        
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
