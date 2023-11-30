<?php

namespace RedeCauzzoMais\Support;

use hisorange\BrowserDetect\Parser;

/**
 * @method static string userAgent()
 * @method static boolean isMobile()
 * @method static boolean isTablet()
 * @method static boolean isDesktop()
 * @method static boolean isBot()
 * @method static string deviceType()
 * @method static string browserName()
 * @method static string browserFamily()
 * @method static string browserVersion()
 * @method static integer browserVersionMajor()
 * @method static integer browserVersionMinor()
 * @method static integer browserVersionPatch()
 * @method static string browserEngine()
 * @method static string platformName()
 * @method static string platformFamily()
 * @method static integer platformVersion()
 * @method static integer platformVersionMajor()
 * @method static integer platformVersionMinor()
 * @method static integer platformVersionPatch()
 * @method static boolean isWindows()
 * @method static boolean isLinux()
 * @method static boolean isMac()
 * @method static boolean isAndroid()
 * @method static string deviceFamily()
 * @method static string deviceModel()
 * @method static boolean isChrome()
 * @method static boolean isFirefox()
 * @method static boolean isOpera()
 * @method static boolean isSafari()
 * @method static boolean isIE()
 * @method static boolean isIEVersion()
 * @method static boolean isEdge()
 * @method static boolean isInApp()
 * @method static array toArray()
 */
class Browser
{
    public static function __callStatic( $name, $arguments )
    {
        return Parser::$name();
    }
}
