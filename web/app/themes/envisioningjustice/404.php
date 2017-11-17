<?php get_template_part('templates/page', 'header'); ?>

<div class="content-wrap">
  <header class="page-header">
    <div class="container">
      <div class="image-wrap">
        <div class="header-text">
          <h1><?= \Roots\Sage\Titles\title(); ?></h1>
        </div>
      </div>
    </div>
  </header>

  <main>
    <div class="alert alert-warning">
      <?php _e('Sorry, but the page you were trying to view does not exist.', 'sage'); ?>
    </div>
  </main>
</div>