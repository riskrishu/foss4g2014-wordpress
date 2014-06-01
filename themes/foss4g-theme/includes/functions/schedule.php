<?php

function slot_time_str($start_time,$slot){
  $start_time=$start_time+(($slot-1)*60*30);
  $finish_time=$start_time + (60*25); #25 minutes long
  return(date('H:i', $start_time) . ' - ' . date('H:i', $finish_time));
}

function time_for_presentation($day, $session, $slot){
  $first_session=strtotime('10:00');
  if ($day == 3) {
    $first_session=strtotime('10:30');
    
  }
   switch ($session){
    case 1:
      $ret = slot_time_str($first_session, $slot);
      break;
    case 2:
      # second sessions starts 3 hours after first
      $ret = slot_time_str($first_session+(3*60*60), $slot);
      break;
    case 3:
    # second sessions starts 5 hours after first
      $ret = slot_time_str($first_session+(5*60*60), $slot);
      break;
    default:
      $ret = 'Unknown Time Slot';
  }
  return($ret);
}

function args_for_post($day, $session, $track, $slot){
  $args = array (
    'post_type' => 'session',
    'post_status' => 'any',
    'meta_query' => array(
      array(
        'key'  => 'decision',
        'value'=>  'accepted',
      ),
      array(
        'key'  => 'schedule_day',
        'value'=>  $day,
        'type' => 'NUMERIC',
      ),
      array(
        'key'  => 'schedule_session',
        'value'=>  $session,
        'type' => 'NUMERIC',
      ),
      array(
        'key'  => 'schedule_track',
        'value'=>  $track,
        'type' => 'NUMERIC',
      ),
      array(
        'key'  => 'schedule_slot',
        'value'=>  $slot,
        'type' => 'NUMERIC',
      ),
    ),
  );
  return($args);
}

function get_schedule() {
  for($day = 1; $day<=3; $day++) {
    for($track = 1; $track<=9; $track++) {
      echo '<div id="d'.$day.'t'.$track.'" class="sched-block row">';
      for($session = 1; $session<=3; $session++) {
        echo '<div class="col-sm-4 session"><h2>Session '.$session.'</h2>';
        for($slot = 1; $slot<=3; $slot++) {
            $the_query = new WP_Query( args_for_post($day, $session, $track, $slot) );
            while ( $the_query->have_posts() ) {
              $post=$the_query->the_post();
              echo '<div class="single-session">';
              $user = get_userdata($post->post_author);
              echo '<span class="session-time">';
              echo time_for_presentation($day,$session,$slot);
              echo '</span><br>';
              echo '<span class="session-title">'.get_the_title().'</span><br>';
              echo '<span class="session-presenter">';
              echo the_author_meta('first_name').' ';
              echo the_author_meta('last_name').'</span>';
              echo '</div>'; //single-session
            } //while
        } //for each slot
        echo '</div>'; // end session
      } // for each session
      echo '</div>'; //end sched-block
    }
  }
}