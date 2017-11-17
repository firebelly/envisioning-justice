<?php

use Roots\Sage\Config;
use Roots\Sage\Wrapper;

?>

<?php get_template_part('templates/head'); ?>
  <body <?php body_class(); ?>>
    <!--[if lt IE 9]>
      <div class="old-browser">
        <?php _e('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'sage'); ?>
      </div>
    <![endif]-->
    <?php
      do_action('get_header');
      get_template_part('templates/header');
    ?>
    <main class="site-main" role="document">
      <?php include Wrapper\template_path(); ?>
    </main><!-- /.wrap -->
    <?php
      get_template_part('templates/footer');
      wp_footer();
    ?>
    <?php if (WP_ENV === 'development'): ?>
    <script type='text/javascript' id="__bs_script__">//<![CDATA[
        document.write("<script async src='http://HOST:3000/browser-sync/browser-sync-client.2.5.0.js'><\/script>".replace("HOST", location.hostname));
    //]]></script>
    <?php endif; ?>
  </body>
</html>
