<?php defined("SYSPATH") or die("No direct script access.") ?>
  <!-- THUMBNAILS -->
  <div class="toolbar" id="thumbs-toolbar-top">
        <a class="backbutton" href="<?= ORM::factory("item", 0)->url() ?>"><?= t("Gallery") ?></a>
        <div class="center">
           <?= t("Search results") ?>
        </div>
          <? if ($user->guest): ?>
            <a class="button" href="<?= url::site("login/html")?>"><?= t("Login") ?></a>
          <? else: ?>
            <a class="button" href="<?= url::site("logout?csrf=".access::csrf_token()."&amp;continue_url=" . urlencode(url::abs_site(""))) ?>"><?= t("Logout") ?></a>
          <? endif ?>
  </div>
	
  <div id="thumbs-container">
    <div id="thumbs-images-container"></div>
    <div id="thumbs-load-more" onclick="thumbmgr.LoadMoreThumbs();"><?= t("Load more...") ?></div>	
  </div>
  <div id="thumbs-footer">
    <div id="thumbs-count-text"></div>
    <? if (module::is_active("search")): ?>
      <form action="<?= url::site("search") ?>" id="g-quick-search-form">
        <input type=search name="q" id="g-search" placeholder="<?= t("Search the gallery") ?>">
      </form>
    <? endif ?>
  </div>

  <script type="text/javascript">
   var images = [
     <? if (count($items)): ?>
      <? for ($i = 0; $i < count($items); $i++): ?>
        <?= imobile::itemlink($items[$i], $i) ?>
      <? endfor ?>
    <? endif ?>
   ];
   <? if (stristr( request::user_agent("agent"),'ipad')): ?>
	var MAX_LOADING_THUMBNAILS = 40;
   <? else: ?>
	var MAX_LOADING_THUMBNAILS = 16;
   <? endif ?>
   var BASE_URL = '<?= $theme->url("../imobile/images/") ?>';
   var MAX_CONCURRENT_LOADING_THUMBNAILS = 4;

   var thumbmgr, slidemgr;

   function init()
   {
      //prepare and load thumbnails
      var dao = new Jph_Dao();
      for (var i in images) {
         dao.ReadImage(
            i, 
            images[i].url,
            images[i].thumburl,
            images[i].caption,
            images[i].type,
            images[i].style
         );
      }

      thumbmgr = new Jph_Application(dao);
      thumbmgr.Init();

      // prepare slideshow
      var options = {
         getImageSource: function(obj){
            return obj.url;
         },
         getImageCaption: function(obj){
            return obj.caption;
         },
         getImageMetaData: function(obj){
            return {
               type: obj.type,
               thumburl: obj.thumburl,
            }
        },
      };
      slidemgr = window.Code.PhotoSwipe.attach( 
         images, 
         options 
      );
   }	  
  </script>

