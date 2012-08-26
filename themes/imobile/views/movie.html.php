<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="item-overlay" style="display:block">
<div class="toolbar" id="item-toolbar-top">
<a class="backbutton" href="<?= ORM::factory("item", $theme->item()->parent_id)->url() ?>"><?= ORM::factory("item", $theme->item()->parent_id)->title ?></a>
</div>
</div>

<video controls="controls" poster="<?= $item->thumb_url() ?>" src="<?= $item->file_url() ?>"></video>

