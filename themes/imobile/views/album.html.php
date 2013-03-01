<?php defined("SYSPATH") or die("No direct script access.") ?>
  <? $children_all = $item->viewable()->children(); ?>
  <!-- THUMBNAILS -->
  <div class="toolbar" id="thumbs-toolbar-top">
          <? if ($theme->item()->parent_id > 0): ?>
            <a class="backbutton" href="<?= ORM::factory("item", $theme->item()->parent_id)->url() ?>"><?= ORM::factory("item", $theme->item()->parent_id)->title ?></a>
          <? endif ?>
        <div class="center">
          <?= html::purify($item->title) ?>
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
   <? if (count($children)): ?>
      <? for ($i = 0; $i < $children_count; $i++): ?>
        <? $child = $children_all[$i] ?>
        <?= imobile::itemlink($child, $i) ?>
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
         //imageScaleMethod: "fitNoUpscale",
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

