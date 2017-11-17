<article class="article page">
  <div class="article-content">
    <div class="article-content-wrap">
      <header class="article-header">
        <h1 class="article-title"><a class="no-ajaxy" href="<?= get_the_permalink($post) ?>"><?= $post->post_title; ?></a></h1>
      </header>
      <div class="article-excerpt">
        <p><?= \Firebelly\Utils\get_excerpt($post); ?></p>
      </div>
    </div>
  </div>
</article>