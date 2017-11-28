<form role="search" method="get" class="search-form form-inline" action="<?= esc_url(home_url('/')); ?>">
  <div class="-inner">  
    <h3>Search</h3>
    <button class="close-search"><span class="text">Close Search</span> <svg class="icon icon-plus" aria-hidden="true" role="image"><use xlink:href="#icon-plus"/></svg></button>
    <div class="input-wrap">    
      <input id="search" type="search" value="" autocomplete="off" name="search" class="search-field form-control" placeholder="" required>
      <label for="search">Keyword(s)</label>
    </div>
    <button type="submit" class="search-submit button">Go</button>
  </div>
</form>
