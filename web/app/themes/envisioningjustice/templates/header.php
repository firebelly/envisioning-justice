<?php use Roots\Sage\Nav\NavWalker; ?>

<div class="il-humanities-bar">
  <div class="container">
    <a href="http://ilhumanities.org"><span class="sr-only">IllinoisHumanities</span><svg class="il-humanities-logo" aria-hidden="true" role="image"><use xlink:href="#ih-logo-grayscale"/></svg></a>
  </div>
</div>

<header class="site-header" role="banner">
  <div class="container hover-item" data-hover="header-slash">
    <div class="-inner">
      <h1 class="site-logo"><a href="/" class="hover-trigger" data-hover="header-slash"><span class="sr-only"><?php bloginfo('name'); ?></span><svg class="icon envisioning-justice-logo" aria-hidden="true" role="image"><use xlink:href="#envisioning-justice-logo"/></svg></a></h1>
      <nav class="site-nav" role="navigation">
        <div class="-inner">
          <?php
          if (has_nav_menu('primary_navigation')) :
            wp_nav_menu(['theme_location' => 'primary_navigation', 'walker' => new NavWalker()]);
          endif;
          ?>
          <button class="search-toggle"><span class="text">Search</span><svg class="icon icon-search" aria-hidden="true" role="image"><use xlink:href="#icon-search"/></svg></button>
        </div>
      </nav>
      <?php get_search_form(); ?>
    </div>
  </div>
</header>
