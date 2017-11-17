<form role="search" method="get" class="search-form form-inline" action="<?= esc_url(home_url('/')); ?>">
  <div class="close-button"></div>
  <label class="sr-only"><?php _e('Search for:', 'sage'); ?></label>
  <input type="search" value="" autocomplete="off" name="s" class="search-field form-control" placeholder="Search" required>
  <button type="submit" class="search-submit"><span class="sr-only"><?php _e('Search', 'sage'); ?></span>    <svg class="icon icon-search" aria-hidden="true" role="image"><use xlink:href="#icon-search"/></svg></button>
</form>
