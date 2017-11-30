<form role="search" method="get" class="search-form form-inline" action="<?= esc_url(home_url('/')); ?>">
  <div class="-inner">  
    <h3>Search</h3>
    <div class="input-wrap">    
      <label for="search">Keyword(s)</label>
      <input id="search" type="search" value="" autocomplete="off" name="search" class="search-field form-control" placeholder="" required>
    </div>
    <button type="submit" class="search-submit button">Go</button>
    <div class="search-close"><span class="text">Close Search</span> <svg class="icon icon-plus" aria-hidden="true" role="image"><use xlink:href="#icon-plus"/></svg></div>
  </div>
</form>
