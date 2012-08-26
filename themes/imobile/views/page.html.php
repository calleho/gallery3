<?php defined("SYSPATH") or die("No direct script access.") ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=2.0; user-scalable=1;"/>
    <link rel="apple-touch-icon-precomposed"
          href="<?= url::file(module::get_var("gallery", "apple_touch_icon_url")) ?>" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <title>
      <? if ($page_title): ?>
        <?= $page_title ?>
      <? else: ?>
        <? if ($theme->item()): ?>
          <? if ($theme->item()->is_album()): ?>
          <?= t("Browse Album :: %album_title", array("album_title" => $theme->item()->title)) ?>
          <? elseif ($theme->item()->is_photo()): ?>
          <?= t("Photo :: %photo_title", array("photo_title" => $theme->item()->title)) ?>
          <? else: ?>
          <?= t("Movie :: %movie_title", array("movie_title" => $theme->item()->title)) ?>
          <? endif ?>
        <? elseif ($theme->tag()): ?>
          <?= t("Browse Tag :: %tag_title", array("tag_title" => $theme->tag()->name)) ?>
        <? else: /* Not an item, not a tag, no page_title specified.  Help! */ ?>
          <?= t("Gallery") ?>
        <? endif ?>
      <? endif ?>
    </title>
    <?= $theme->script("staystandalone.min.js") ?>
    <?= $theme->head() ?>
    <?= $theme->css("imobile.css") ?>
    <?= $theme->css("photoswipe-3.0.4.css") ?>
    <?= $theme->script("klass.min.js") ?>
    <?= $theme->script("code.photoswipe-3.0.4.min.js") ?>
    <?= $theme->script("imobile-1.0.1.min.js") ?>
  </head>
    <? if($page_subtype == "login"): ?>
      <body>
      <?= $theme->page_top() ?>
      <?= $theme->messages() ?>
      <div id="login" class="current">
      <form action="<?= url::abs_site("") ?>login/auth_html" method="post" id="g-login-form" class="form">
      <input type="hidden" name="csrf" value="<?= access::csrf_token() ?>" />
      <input type="hidden" name="continue_url" value="<?= Session::instance()->get("continue_url") ?>"  />
        <div class="toolbar center" id="login-toolbar-top">
          <?= t("Gallery") ?> - <?= $page_title ?>
        </div>
        <ul class="rounded">
          <li><input type="text" name="name" placeholder="Name" /></li>
          <li><input type="password" name="password" placeholder="Password" /></li>
        </ul>
        <input type="submit" class="btn login gray" value="<?= t("Login") ?>">
      </form>
      </div>        
    <? else: ?>
      <body onload="init()">
      <?= $theme->page_top() ?>
      <?= $theme->messages() ?>
      <?= $content ?>
    <? endif ?>
    <?= $theme->page_bottom() ?>
  </body>

