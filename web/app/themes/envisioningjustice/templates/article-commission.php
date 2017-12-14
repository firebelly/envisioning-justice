<?php
  $commission_url = get_permalink($commission_post);
  $header_bg = \Firebelly\Media\get_header_bg($commission_post);
?>
<li class="commission grid-item sm-one-half big-clicky">
  <div class="commission-image-wrap">
    <div class="commission-image" <?= $header_bg ?>></div>
  </div>
  <h2 class="type-h2"><a href="<?= $commission_url ?>"><?= $commission_post->post_title ?></a></h2>
</li>