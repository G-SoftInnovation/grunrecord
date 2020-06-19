<?php
    global $wpdb;
    $record_query = "SELECT r.distance, r.time_in_seconds, r.created_time, r.status, r.rejected_reason "
    . "FROM wp_grun_record r "
    . "INNER JOIN wp_grun_events_records er ON er.record_id = r.record_id "
    . "WHERE r.uid = " . get_current_user_id()  
    ." AND er.eid IN ( "
    ."SELECT p.ID  "
    ."FROM wp_posts p  "
    ."INNER JOIN wp_postmeta pm ON pm.post_id = p.ID "
    ."WHERE pm.meta_key = 'grun_ebib_pgid' "
    ."AND pm.meta_value = '$gid' ) "
    . " ORDER BY r.created_time DESC ";

    $records = $wpdb->get_results($record_query);
?>


<div class="row">
   
    <div class="grun-col-md-12">
            <table>
            <tr>
                <th class="text-center">เวลาส่งผล</th>
                <th class="text-center">รายการวิ่ง</th>
                <th class="text-center">ระยะทาง</th>
                <th class="text-center">เวลา</th>
                <th class="text-center">สถานะ</th>
                <th class="text-center">หมายเหตุ</th>
            </tr>
            <?php foreach($records as $record) { ?>
            <tr>
                <?php
                    $dtime = DateTime::createFromFormat("Y-m-d H:i:s", $record->created_time);
                    $timestamp = $dtime->getTimestamp();
                ?>
                <td class="text-center"><?php echo date ('d/m/Y H:i', $timestamp); ?></td>
                <td class="text-left"><?php echo date ('d/m/Y H:i', $timestamp) ;?></td>
                <td class="text-right"><?php echo $record->distance; ?> km</td>
                <td class="text-center><?php echo gmdate("H:i:s", $record->time_in_seconds); ?></td>
                <?php
                if($record->status == 0){
                ?>
                    <td class="text-center">รอการตรวจสอบ</td>
                <?php
                }
                elseif($record->status == 1){
                ?>
                    <td class="text-center text-green">อนุมัติ</td>
                <?php
                }
                elseif($record->status == 2){
                ?>
                    <td class="text-center text-red">ไม่อนุมัติ</td>
                <?php
                }
                ?>
                <td><?php echo $record->rejected_reason;?></td>
            </tr>
            <?php } ?>
            </table>
 
    </div>

</div>

