<footer class="site-footer" role="contentinfo">

  <div class="slashfield-container container">
    <div class="-inner">
      <div class="slashfield" data-rows="3"></div>
    </div>
  </div>

  <div class="content-container container">
    <div class="grid md-col-spaced">

      <div class="grid-item md-one-half -first">

        <ul class="social-media-links">
          <?php if (!empty(\Firebelly\SiteOptions\get_option('facebook_url'))) { ?>
            <li><a href="<?= \Firebelly\SiteOptions\get_option('facebook_url'); ?>" class="button button-circular theme-exception -gray-light"><span class="sr-only">Facebook</span><svg class="icon icon-facebook" aria-hidden="true" role="presentation"><use xlink:href="#icon-facebook"/></svg></a></li>
          <?php } ?>
          <?php if (!empty(\Firebelly\SiteOptions\get_option('twitter_url'))) { ?>
            <li><a href="<?= \Firebelly\SiteOptions\get_option('twitter_url'); ?>" class="button button-circular theme-exception -gray-light"><span class="sr-only">Twitter</span><svg class="icon icon-twitter" aria-hidden="true" role="presentation"><use xlink:href="#icon-twitter"/></svg></a></li>
          <?php } ?>
          <?php if (!empty(\Firebelly\SiteOptions\get_option('youtube_url'))) { ?>
            <li><a href="<?= \Firebelly\SiteOptions\get_option('youtube_url'); ?>" class="button button-circular theme-exception -gray-light"><span class="sr-only">YouTube</span><svg class="icon icon-youtube" aria-hidden="true" role="presentation"><use xlink:href="#icon-youtube"/></svg></a></li>
          <?php } ?>
        </ul>
        
      </div>

      <div class="site-contact grid-item md-one-half -second">

        <form id="newsletter" class="newsletter-form">
          <h3 class="type-h3">Subscribe to our Newsletter</h3>
          <div class="-bottom">
            <div class="form-group">          
              <div class="input-wrap">
                <label for="newsletterName">Full Name</label>
                <input type="text" id="newsletterName" name="newsletterName">
              </div>
              <div class="input-wrap">
                <label for="newsletterEmail">Email Address</label>
                <input type="email" id="newsletterEmail" name="newsletterEmail">
              </div>
            </div>
            <button type="submit" class="button button-circular submit -white-dark theme-exception"><span class="sr-only">Submit</span><svg class="icon icon-arrow" aria-hidden="true" role="presentation"><use xlink:href="#icon-arrow"/></svg></button>
          </div>
        </form>

        <div class="logos">
          <a href="http://ilhumanities.org"><span class="sr-only">Illinois Humanities</span><svg class="il-humanities-logo" aria-hidden="true" role="presentation"><use xlink:href="#ih-logo-color"/></svg></a>
          <a href="/"><span class="sr-only"><?php bloginfo('name'); ?></span><svg class="icon envisioning-justice-logo" aria-hidden="true" role="presentation"><use xlink:href="#envisioning-justice-logo"/></svg></a>
        </div>
        <p class="site-disclaimer"><?= \Firebelly\SiteOptions\get_option('disclaimer'); ?></p>


        <h3 class="type-h3">Contact</h3>

        <div class="site-communication -item">
          <p><a href="mailto:<?= \Firebelly\SiteOptions\get_option('contact_email'); ?>"><?= \Firebelly\SiteOptions\get_option('contact_email'); ?></a></p>
          <p><?= \Firebelly\SiteOptions\get_option('contact_phone'); ?></p>
        </div>
        <div class="site-address -item">        
          <address class="vcard">
            <span class="street-address"><?= \Firebelly\SiteOptions\get_option('contact_address'); ?></span>
            <span class="locality"><?= \Firebelly\SiteOptions\get_option('contact_locality'); ?></span>
          </address>
        </div>

      </div>

    </div>
  </div>
</footer>