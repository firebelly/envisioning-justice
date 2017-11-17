<?php 
if ($thought = \Firebelly\PostTypes\Thought\get_thought_of_the_day(!empty($focus_area) ? $focus_area : '')):
?>
<section class="thought-of-the-day">
  <div class="thought-wrapper">
    <h2 class="flag">Thought of the Day</h2>
    <?= $thought ?>
    <div class="submit-thought"><a class="button" href="#">Submit A Thought</a></div>
  </div>

  <div class="submit-thought-wrapper hide">
    <h2 class="flag">Submit Your Thought</h2>
    <?= \Firebelly\PostTypes\Thought\submit_form(); ?>
  </div>

  <div class="thought-submitted-wrapper hide">
    <h2 class="flag">Thanks For Submitting</h2>
    <h2 class="response"></h2>
  </div>

  <div class="close-button"></div>
</section>
<?php endif; ?>