<?php
/**
 * Filter dropdowns for Events and Blog
 */

$filter_program = get_query_var('filter_program', '');
$filter_focus_area = get_query_var('filter_focus_area', '');
$past_events = get_query_var('past_events', 0);
$prox_miles = get_query_var('prox_miles', 0);
$prox_zip = (int)get_query_var('prox_zip', '');
if ($prox_zip==0) $prox_zip = '';
if (strlen($prox_zip) > 5) $prox_zip = substr($prox_zip, 0, 5);
$post_type = is_post_type_archive('event') ? 'event' : 'post';
?>
  <form class="filters" action="/<?= $post_type == 'event' ? 'events' : 'news' ?>/" method="get">
    <div class="focus-area-topic">Focus Area:
      <div class="select-wrapper">
        <select name="filter_focus_area">
          <option value="">ALL</option>
            <?php $focus_areas = get_terms('focus_area');
            foreach ($focus_areas as $focus_area): ?>
            <option <?= $filter_focus_area==$focus_area->slug ? 'selected' : '' ?> value="<?= $focus_area->slug ?>"><?= $focus_area->name ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="program-topic">Program: 
      <?php if ($post_type==='event'): ?>
        <input type="hidden" name="past_events" value="<?= $past_events ?>">
      <?php endif ?>
      <div class="select-wrapper">
        <select name="filter_program">
          <option value="">ALL</option>
            <?php 
            $extra_where = '';
            // If filtering events, only match past events, or future events
            if ($post_type=='event') {
              $evt_post_ids = $wpdb->get_results(
                "SELECT ID FROM {$wpdb->posts} p
                INNER JOIN {$wpdb->postmeta} pm ON (pm.post_id=p.iD AND pm.meta_key='_cmb2_event_end')
                WHERE p.post_type='event' AND pm.meta_value " . (!empty($_REQUEST['past_events']) ? '<=' : '>') . current_time('timestamp')
              );
              $event_ids = [];
              foreach ($evt_post_ids as $event)
                $event_ids[] = $event->ID;
              $extra_where = 'AND p2.ID IN (' . implode(',', $event_ids) . ') ';
            }
            if (!empty($_REQUEST['filter_focus_area'])) {
              // todo: also filter out posts that match focus_area first
            }
            $programs_related = $wpdb->get_results(
              "SELECT p.ID,p.post_title FROM {$wpdb->postmeta} pm 
              INNER JOIN {$wpdb->posts} p ON (p.ID=pm.meta_value) 
              INNER JOIN {$wpdb->posts} p2 ON (p2.ID=pm.post_id) 
              WHERE meta_key='_cmb2_related_program' AND p2.post_type='{$post_type}' 
              {$extra_where}
              GROUP BY pm.meta_value ORDER BY p.post_title"
            );
            foreach ($programs_related as $program):
            ?>
            <option <?= $filter_program==$program->ID ? 'selected' : '' ?> value="<?= $program->ID ?>"><?= $program->post_title ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

  <?php if ($post_type=='event'): ?>
    <div class="event-proximity">
      <div class="input-wrapper">
        <label for="prox_zip">ZIP CODE: <input type="text" name="prox_zip" value="<?= $prox_zip ?>" placeholder="Your Zip"></label>
      </div>
      Distance: 
      <div class="select-wrapper">
        <select name="prox_miles">
            <?php 
            $prox_arr = [
              '' => 'All',
              '1' => '1 Mile',
              '2' => '2 Miles',
              '5' => '5 Miles',
              '10' => '10 Miles',
              '20' => '20 Miles',
              '50' => '50 Miles',
            ];
            foreach ($prox_arr as $prox_val => $prox_title):
            ?>
            <option <?= $prox_val==$prox_miles ? 'selected' : '' ?> value="<?= $prox_val ?>"><?= $prox_title ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
  <?php endif; ?>

    <div class="actions">
      <button class="button" type="submit">Filter</button>
    </div>
  </form>
