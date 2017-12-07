<?php
$sponsor_url = get_post_meta($sponsor_post->ID, '_cmb2_sponsor_url', true);
?>
<div class="sponsor grid-item sm-one-half md-one-fourth big-clicky">
  <svg class="icon icon-image" aria-hidden="true" role="presentation"><use xlink:href="#icon-image"/></svg>
  <h3 class="type-h3">
    <?php
      if (!empty($sponsor_url)) { echo '<a href="'.$sponsor_url.'">';} else { echo ''; }
      echo $sponsor_post->post_title;
      if (!empty($sponsor_url)) { echo '</a>';} else { echo ''; }
    ?>
  </h3>
</div>