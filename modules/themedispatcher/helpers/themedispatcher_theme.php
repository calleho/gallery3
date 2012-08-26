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
class themedispatcher_theme_Core {
  static function admin_head($theme) {
    $buf = "";
    if (strpos(Router::$current_uri, "admin/themedispatcher") !== false) {
      $buf .= $theme->css("admin_themedispatcher.css");
    }

    return $buf;
  }

  static function head($theme) {
    $buf = $theme->css("themedispatcher.css");
    return $buf;
  }

  static function page_bottom($theme) {
    $buf = "";
    if ((module::get_var("themedispatcher", "overridedispatch", 0) == 1) && (theme::$dispatch)) {
       $buf .= "<div class=\"g-themedispatcher\">";
       if (theme::$site_theme_name == module::get_var("gallery", "active_site_theme")) {
          $buf .= "<a href=\"";
          $buf .= url::site("themedispatcher/overrideoff");
          $buf .= "\" id=\"override\">".t("Mobile")."</a> | ".t("Full Version");
       } else {
          $buf .= t("Mobile")." | <a href=\"";
          $buf .= url::site("themedispatcher/overrideon");
          $buf .= "\" id=\"override\">".t("Full")."</a> ".t("Version");
       }
       $buf .= "</div>";
    }  
    return $buf;
  }
}
