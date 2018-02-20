<form role="search" method="get" class="search-form form-inline" action="<?= esc_url(home_url('/')); ?>">
  <div class="-inner">  
    <h3>Search</h3>
    <div class="input-wrap">    
      <label for="s">Keyword(s)</label>
      <input id="s" type="search" value="" autocomplete="off" name="s" class="search-field form-control" placeholder="" required>
    </div>
    <button type="submit" class="search-submit button">Go</button>
    <div class="search-close"><span class="text">Close Search</span> <svg class="icon icon-plus" aria-hidden="true" role="presentation"><use xlink:href="#icon-plus"/></svg></div>
  </div>
</form>
