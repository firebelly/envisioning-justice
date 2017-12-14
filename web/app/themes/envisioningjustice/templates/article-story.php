<?php
use Firebelly\Utils;
$story_url = get_permalink($story_post);
?>
<article class="story">
  <h2 class="article-title type-h1"><a href="<?= $story_url ?>"><?= $story_post->post_title ?></a></h2>
  <p><?= Utils\get_excerpt($story_post); ?></p>
</article>