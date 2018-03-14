<footer class="site-footer" role="contentinfo">

  <div class="slashfield-container container">
    <div class="-inner">
      <div class="slashfield" data-rows="3"></div>
    </div>
  </div>

  <div class="content-container container">
    <div class="grid">

      <div class="social-media-section section grid-item md-one-half -first">

        <ul class="social-media-links">
          <?php if (!empty(\Firebelly\SiteOptions\get_option('hashtag'))) { ?>
            <li class="hashtag">
              <h3 class="type-h3"><?= \Firebelly\SiteOptions\get_option('hashtag') ?></h3>
            </li>
          <?php } ?>
          <?php if (!empty(\Firebelly\SiteOptions\get_option('facebook_handle'))) { ?>
            <li>
              <a href="https://facebook.com/<?= \Firebelly\SiteOptions\get_option('facebook_handle'); ?>" class="button button-circular theme-exception -gray-light"><span class="sr-only">Facebook</span><svg class="icon icon-facebook" aria-hidden="true" role="presentation"><use xlink:href="#icon-facebook"/></svg></a>
              <a href="https://facebook.com/<?= \Firebelly\SiteOptions\get_option('facebook_handle'); ?>">@<?= \Firebelly\SiteOptions\get_option('facebook_handle') ?></a>
            </li>
          <?php } ?>
          <?php if (!empty(\Firebelly\SiteOptions\get_option('twitter_handle'))) { ?>
            <li>
              <a href="https://twitter.com/<?= \Firebelly\SiteOptions\get_option('twitter_handle'); ?>" class="button button-circular theme-exception -gray-light"><span class="sr-only">Twitter</span><svg class="icon icon-twitter" aria-hidden="true" role="presentation"><use xlink:href="#icon-twitter"/></svg></a>
              <a href="https://twitter.com/<?= \Firebelly\SiteOptions\get_option('twitter_handle'); ?>">@<?= \Firebelly\SiteOptions\get_option('twitter_handle') ?></a>
            </li>
          <?php } ?>
        </ul>
        
      </div>

      <div class="section site-contact grid-item md-one-half -second">

        <h3 class="type-h3">Join our Email List</h3>
        <div id="bbox-root-38ced2e2-cad0-4cec-baa5-4e97cce64789" class="bb-form"></div>
        <script type="text/javascript">// <![CDATA[
        var bboxInit2 = bboxInit2 || [];     bboxInit2.push(function () {         bboxApi.showForm('38ced2e2-cad0-4cec-baa5-4e97cce64789');     });     (function () {         var e = document.createElement('script'); e.async = true;         e.src = 'https://bbox.blackbaudhosting.com/webforms/bbox-2.0-min.js';         document.getElementsByTagName('head')[0].appendChild(e);     } ());
        // ]]></script>

        <h3 class="type-h3">Envisioning Justice</h3>

        <div class="-item">
          <div class="site-address">        
            <address class="vcard">
              <span class="street-address"><?= \Firebelly\SiteOptions\get_option('contact_address'); ?></span>
              <span class="locality"><?= \Firebelly\SiteOptions\get_option('contact_locality'); ?></span>
            </address>
          </div>
          <div class="site-communication">
            <p><a href="mailto:<?= \Firebelly\SiteOptions\get_option('contact_email'); ?>"><?= \Firebelly\SiteOptions\get_option('contact_email'); ?></a></p>
            <p><?= \Firebelly\SiteOptions\get_option('contact_phone'); ?></p>
          </div>       
        </div>

        <div class="logos">
          <a href="http://ilhumanities.org"><span class="sr-only">Illinois Humanities</span><svg class="il-humanities-logo" aria-hidden="true" role="presentation"><use xlink:href="#ih-logo-color"/></svg></a>
          <a href="/"><span class="sr-only"><?php bloginfo('name'); ?></span><svg class="icon envisioning-justice-logo" aria-hidden="true" role="presentation"><use xlink:href="#envisioning-justice-logo"/></svg></a>
        </div>
        <p class="site-disclaimer"><?= \Firebelly\SiteOptions\get_option('disclaimer'); ?></p>

      </div>

    </div>
  </div>
</footer>