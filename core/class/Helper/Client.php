<?php
/* This file is part of NextDom.
 *
 * NextDom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * NextDom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NextDom. If not, see <http://www.gnu.org/licenses/>.
 */
namespace NextDom\Helper;

class Client
{
    /**
     * Test si l'utilisateur utilise un navigateur sur mobile
     *
     * @return bool True si un navigateur mobile est détecté
     */
    public static function isMobile()
    {
        $useragent = (isset($_SERVER["HTTP_USER_AGENT"])) ? $_SERVER["HTTP_USER_AGENT"] : 'none';
        $result = false;
        if (stristr($useragent, "Android") ||
            strpos($useragent, "iPod") ||
            strpos($useragent, "iPhone") ||
            strpos($useragent, "Mobile") ||
            strpos($useragent, "WebOS") ||
            strpos($useragent, "mobile") ||
            strpos($useragent, "hp-tablet")) {
            $result = true;
        }
        return $result;
    }
}