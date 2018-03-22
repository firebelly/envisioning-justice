<?php
/**
 * Filtering for Events
 */

$filter_related_hub = get_query_var('filter_related_hub', '');
$filter_event_type = get_query_var('filter_event_type', '');
$filter_program_type = get_query_var('filter_program_type', '');
$program_types = get_terms(array(
  'taxonomy' => 'program-type',
  'hide_empty' => true,
));
?>
  <form class="filters" action="/programming" method="get">

    <h3 class="type-h3">View By</h3>

    <div class="field-group grid sm-spaced">
      <div class="radio-wrap grid-item sm-one-half">
        <input type="radio" name="filter_event_type" id="one-time" value="one-time" <?= $filter_event_type=='one-time' ? 'checked' : '' ?>>
        <label for="one-time">One-Time Events</label>
      </div>
      <div class="radio-wrap grid-item sm-one-half">
        <input type="radio" name="filter_event_type" id="ongoing" value="ongoing" <?= $filter_event_type=='ongoing' ? 'checked' : '' ?>>
        <label for="ongoing">Ongoing Events</label>
      </div>
    </div>

    <div class="field-group grid sm-spaced">
      
      <div class="hub-topic grid-item sm-one-half">
        <div class="select-wrapper">
          <select name="filter_related_hub">
            <option value="">Hub</option>
            <?php
              $hub_args = array(
                'post_type' => 'hub',
                'numberposts' => -1
              );

              $hubs = get_posts($hub_args);
              foreach ($hubs as $hub): ?>
              <option <?= $filter_related_hub==$hub->ID ? 'selected' : '' ?> value="<?= $hub->ID ?>"><?= $hub->post_title ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="program-topic grid-item sm-one-half">
        <div class="select-wrapper">
          <select name="filter_program_type">
            <option value="">Program Type</option>
            <?php
              foreach ($program_types as $program_type): ?>
              <option <?= $filter_program_type==$program_type->slug ? 'selected' : '' ?> value="<?= $program_type->slug ?>"><?= $program_type->name ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>


    </div>

    <div class="actions grid sm-spaced">
      <div class="grid-item sm-one-half">
        <button class="button -full" type="submit">Filter</button>
      </div>
      <div class="grid-item sm-one-half"><button id="filter-clear" class="button -full<?= ($filter_related_hub !== '' || $filter_program_type !== '' || $filter_event_type !== '') ? '' : ' hide' ?>">Clear All</button></div>
    </div>
  </form>
