<?php
/**
 * Filtering for Resources
 */

$filter_neighborhood = get_query_var('filter_neighborhood', '');
$filter_resource_type = get_query_var('filter_resource_type', '');
$filter_order = get_query_var('filter_order', '');
$resource_types = get_terms(array(
  'taxonomy' => 'resource-type',
  'hide_empty' => true,
));
?>
  <form class="filters" action="/resources" method="get">

    <h3 class="type-h3">View By</h3>

    <div class="field-group grid sm-spaced">

      <div class="grid-item sm-one-half">
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

      <div class="grid-item sm-one-half">
        <div class="select-wrapper">
          <select name="filter_order">
            <option value="">Name (A-Z)</option>
            <option value="DESC" <?= $filter_order=='DESC' ? 'selected' : '' ?>>Name (Z-A)</option>
          </select>
        </div>
      </div>

    </div>

    <div class="actions grid sm-spaced">
      <div class="grid-item sm-one-half">
        <button class="button -full" type="submit">Search</button>
      </div>
      <div class="grid-item sm-one-half"><button id="filter-clear" class="button -full<?= ($filter_order !== '' || $filter_resource_type !== '') ? '' : ' hide' ?>">Clear All</button></div>
    </div>
  </form>
