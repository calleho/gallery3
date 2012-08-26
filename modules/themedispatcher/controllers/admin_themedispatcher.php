<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2011 Bharat Mediratta
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
class Admin_Themedispatcher_Controller extends Admin_Controller {

  public function index() {
    $view = new Admin_View("admin.html");
    $view->page_title = t("Select theme based on user-agent");
    $view->content = new View("admin_themedispatcher.html");
    $view->content->form = $this->_get_admin_form();
    $uas = unserialize(module::get_var("themedispatcher", "useragents", "a:0:{}"));
    $view->content->uas = array_keys($uas);

    print $view;
  }

  public function add_ua() {
    access::verify_csrf();

    $form = $this->_get_admin_form();
    $form->validate();

    $uas = unserialize(module::get_var("themedispatcher", "useragents", "a:0:{}"));
    $ua = $form->add_ua->ua->value;
    if ($ua) {
      $uas[$ua] = 1;
      module::set_var("themedispatcher", "useragents", serialize($uas));
    }
    module::set_var("themedispatcher", "theme", $form->target_theme->theme->value);
    module::set_var("themedispatcher", "overridedispatch", $form->target_theme->overridedispatch->value);
    message::success(t("Settings saved"));
    themedispatcher::check_config($uas);
    url::redirect("admin/themedispatcher");

    $view = new Admin_View("admin.html");
    $view->content = new View("admin_themedispatcher.html");
    $view->content->form = $form;
    $view->content->uas = array_keys($uas);
    print $view;
  }

  public function remove_ua() {
    access::verify_csrf();

    $ua = Input::instance()->get("ua");
    $uas = unserialize(module::get_var("themedispatcher", "useragents"));
    if (isset($uas[$ua])) {
      unset($uas[$ua]);
      message::success(t("Removed user-agent %ua", array("ua" => $ua)));
      module::set_var("themedispatcher", "useragents", serialize($uas));
      themedispatcher::check_config($uas);
    }
    url::redirect("admin/themedispatcher");
  }

  private function _get_admin_form() {
    $form = new Forge("admin/themedispatcher/add_ua", "", "post", array("id" => "g-themedispatcher-admin-form"));
    $target_theme = $form->group("target_theme")->label(t("Theme"));
    $target_theme->dropdown("theme")
      ->label(t("Target Theme"))
      ->options($this->_get_themes())
      ->selected(module::get_var("themedispatcher", "theme"));
    $target_theme->checkbox("overridedispatch")->label(t("Places a link in the theme footer to allow users to manually switch between target and default theme."))
      ->checked(module::get_var("themedispatcher", "overridedispatch", 0) == 1);
    $target_theme->submit("save")->value(t("Save"));

    $add_ua = $form->group("add_ua")->label(t("User-Agents"));
    $add_ua->input("ua")->label(t("User-Agent"))->rules("required")->id("g-ua");
    $add_ua->submit("add")->value(t("Add User-Agent"));
    return $form;
  }

  private function _get_themes() {
    $themes = array();
    foreach (scandir(THEMEPATH) as $theme_name) {
      if ($theme_name[0] == ".") {
        continue;
      }
      $theme_name = preg_replace("/[^a-zA-Z0-9\._-]/", "", $theme_name);
      if (file_exists(THEMEPATH . "$theme_name/theme.info")) {
        $themeinfo = theme::get_info($theme_name);
        if ($themeinfo->site) {
          $themes[$theme_name] = theme::get_info($theme_name)->name;
        }
      }
    }
    return $themes;
  }
}
