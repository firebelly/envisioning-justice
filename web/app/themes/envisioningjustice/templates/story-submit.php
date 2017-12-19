<div class="story-submission">
  <?= \Firebelly\PostTypes\Story\submit_form(); ?>

  <div class="story-submitted-wrapper hide">
    <?php if (!empty(\Firebelly\SiteOptions\get_option('story_submission_success'))) { ?>
      <h3 class="type-h2"><?= \Firebelly\SiteOptions\get_option('story_submission_success'); ?></h2>
    <?php } else { ?>
      <h3 class="type-h2">Your story has been submitted. Thank You.</h2>
    <?php } ?>
    <h3 class="response type-h3"></h2>
  </div>
</div>