<?php
$sponsor_logo = get_the_post_thumbnail($sponsor_post, 'medium', array('class' => 'sponsor-logo'));
$sponsor_url = get_post_meta($sponsor_post->ID, '_cmb2_sponsor_url', true);
?>
<div class="sponsor <?= !empty($sponsor_url) ? 'big-clicky' : ''; ?>">
  <?= $sponsor_logo ?>
  <h3 class="type-h3">
    <?php
      if (!empty($sponsor_url)) { echo '<a href="'.$sponsor_url.'">';} else { echo ''; }
      echo $sponsor_post->post_title;
      if (!empty($sponsor_url)) { echo '</a>';} else { echo ''; }
    ?>
  </h3>
</div>