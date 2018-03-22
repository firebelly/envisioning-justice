<?php
/**
 * Filtering for Resources
 */

$filter_neighborhood = get_query_var('filter_neighborhood', '');
$filter_resource_type = get_query_var('filter_resource_type', '');
$resource_types = get_terms(array(
  'taxonomy' => 'resource-type',
  'hide_empty' => true,
));
?>
  <form class="filters" action="/resources" method="get">

    <h3 class="type-h3">View By</h3>

    <div class="field-group">

      <div class="select-wrapper">
        <select name="filter_resource_type">
          <option value="">Resource Type</option>
          <?php
            foreach ($resource_types as $resource_type): ?>
            <option <?= $filter_resource_type==$resource_type->slug ? 'selected' : '' ?> value="<?= $resource_type->slug ?>"><?= $resource_type->name ?></option>
          <?php endforeach; ?>
        </select>
      </div>

    </div>

    <div class="actions grid sm-spaced">
      <div class="grid-item sm-one-half">
        <button class="button -full" type="submit">Filter</button>
      </div>
      <div class="grid-item sm-one-half"><button id="filter-clear" class="button -full<?= ($filter_neighborhood !== '' || $filter_resource_type !== '') ? '' : ' hide' ?>">Clear All</button></div>
    </div>
  </form>
