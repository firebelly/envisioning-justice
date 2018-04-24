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
  <form class="filters" action="/programming#filter" method="get" id="filter">

    <h3 class="type-h3">View By</h3>

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
        <button class="button -full" type="submit">Search</button>
      </div>
      <div class="grid-item sm-one-half"><button id="filter-clear" class="button -full<?= ($filter_related_hub !== '' || $filter_program_type !== '' || $filter_order !== '') ? '' : ' hide' ?>">Clear All</button></div>
    </div>
  </form>
