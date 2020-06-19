<?php
    global $wpdb;

    

    $current_user = wp_get_current_user();
    $card_img =  plugins_url('/img/leader_'.$target.'km.png',  __FILE__ );
    $limit = empty($limit)?5:$limit;
    $wpdb->query('SET @row_number = 0;');
    $query = 'SELECT * FROM (SELECT (@row_number:=@row_number + 1) AS rnk, a.* '
    ."FROM ( "
        ."SELECT  r.uid,  ger.eid, em.target_distance, SUM(r.distance) AS total_distance  "
        ."FROM wp_grun_record r "
        ."INNER JOIN wp_grun_events_records ger ON ger.record_id = r.record_id "
        ."INNER JOIN grun_event_member em ON em.uid = r.uid AND em.eid = ger.eid "
        ."WHERE em.target_distance = ".$target 
        ." AND em.eid IN ( "
        ."SELECT p.ID  "
        ."FROM wp_posts p  "
        ."INNER JOIN wp_postmeta pm ON pm.post_id = p.ID "
        ."WHERE pm.meta_key = 'grun_ebib_pgid' "
        ."AND pm.meta_value = '$gid' ) "
        ." GROUP BY r.uid, ger.eid, em.target_distance "
        ." ORDER BY SUM(r.distance) DESC "
        .") a ) b "
        ."WHERE b.uid = " . $current_user->ID ." OR b.rnk <= " . $limit ;
    $leaders = $wpdb->get_results($query); 
?>

<div class="card grun-col-md-12 grun-col-sm-12 grun-col-xs-12">
    <div class="leaderboard">
    <div class="card-header-tab card-header">
        <div class="header-title"><?php echo $title;?></div>
        <div class="leaderboard-logo"><img src="<?php echo $card_img; ?>"/></div>
    </div>
    <ol>
        <?php foreach($leaders as $lead) { ?>
        <li class="<?php echo ($current_user->ID == $lead->uid)?'my-rank':'none'; ;?>">
        <rnk><?php echo $lead->rnk; ?></rnk>
        <?php 
             $usr = get_userdata($lead->uid); 
             $runner =  $usr->first_name . " ". $usr->last_name;
        ?>
        <mark><?php echo $runner; ?></mark>
        <distance><?php echo $lead->total_distance;?> km</distance>
        </li>
        <?php }?>
    </ol>
    </div>
</div>
