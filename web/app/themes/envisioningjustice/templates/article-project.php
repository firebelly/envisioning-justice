<?php
  $project_url = get_permalink($project_post);
  $header_bg = \Firebelly\Media\get_header_bg($project_post);
?>
<li class="project grid-item sm-one-half big-clicky">
  <div class="image-wrap">
    <div class="image" <?= $header_bg ?>></div>
  </div>
  <h2 class="type-h2"><a href="<?= $project_url ?>"><?= $project_post->post_title ?></a></h2>
</li>
