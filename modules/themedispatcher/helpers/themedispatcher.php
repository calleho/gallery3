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
class themedispatcher_Core {
  static function check_config($uas=null) {
    if ($uas === null) {
      $uas = unserialize(module::get_var("themedispatcher", "useragents"));
    }
    if (empty($uas)) {
      site_status::warning(
        t("Theme dispatcher needs configuration. <a href=\"%url\">Configure it now!</a>",
          array("url" => html::mark_clean(url::site("admin/themedispatcher")))),
        "themedispatcher_configuration");
    } else {
      site_status::clear("themedispatcher_configuration");
    }
  }
}
