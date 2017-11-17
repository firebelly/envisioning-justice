<article class="program">
  <div class="article-content">
    <div class="article-content-wrap">
      <h1 class="article-title"><a href="<?= get_the_permalink($program_post); ?>"><?= $program_post->post_title; ?></a></h1>
      <p><?= \Firebelly\Utils\get_excerpt($program_post); ?></p>
      <a class="button" href="<?= get_the_permalink($program_post); ?>">Learn More</a>
    </div>
  </div>
</article>