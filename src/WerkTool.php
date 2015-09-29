<?php
namespace wegewerk\devops;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;
use wegewerk\devops\WerkTool\Cli\CliDeploy;
include dirname(__FILE__) ."/../../../../autoload.php";

/**
* WerkTool
*/
class WerkTool
{
    public static function werkAction(Event $event)
    {
      $deploy = new CliDeploy($event);
    }

    public static function postUpdate(Event $event)
    {
        // $composer = $event->getComposer();
        // do stuff
    }

    public static function postAutoloadDump(Event $event)
    {
        // $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        // require $vendorDir . '/autoload.php';
        // some_function_from_an_autoloaded_file();
    }

    public static function postPackageInstall(PackageEvent $event)
    {
        // $installedPackage = $event->getOperation()->getPackage();
        // do stuff
    }
}

