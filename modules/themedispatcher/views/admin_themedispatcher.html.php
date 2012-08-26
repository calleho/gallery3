<?php defined("SYSPATH") or die("No direct script access.") ?>
<div class="g-block">
  <h1> <?= t("Theme dispatcher settings") ?> </h1>
  <div class="g-block-content">
    <?= $form ?>
    <fieldset>
      <legend><?= t("Configured User-Agents") ?></legend>
      <ul id="g-themedispatcher-uas">
        <? if (empty($uas)): ?>
        <li class="g-module-status g-info"><?= t("No user-agents defined yet") ?></li>
        <? endif ?>
        <? foreach ($uas as $id => $ua): ?>
        <li>
          <?= html::clean($ua) ?>
          <a href="<?= url::site("admin/themedispatcher/remove_ua?ua=" . urlencode($ua) . "&amp;csrf=$csrf") ?>"
             id="icon_<?= $id ?>"
             class="g-remove-dir g-button"><span class="ui-icon ui-icon-trash"><?= t("delete") ?></span></a>
        </li>
        <? endforeach ?>
      </ul>
    </fieldset>
  </div>
</div>
