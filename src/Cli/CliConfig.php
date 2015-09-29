<?php

namespace wegewerk\devops\WerkTool\Cli;

use Garden\Cli\Cli;
use wegewerk\devops\WerkTool\WerkConfig;

require_once dirname(__FILE__)  .'/vendor/autoload.php';

/**
* CliConfig
*/
class CliConfig
{

    function __construct($argv)
    {
            // Define the cli options.
        $cli = new Cli();

        $cli->description('A simple build system wrapper.')
            // Define the first command: dev.
            ->command('dev')
            ->description('Build type: development.')
            // Define the first command: int.
            ->command('int')
            ->description('Build type: integration.')
            // Some global options.
            ->command('*')
            ->opt('platform:p', 'Target platform for this build.', false, 'string')
            ->opt('automatic:a', 'No prompts.', false, 'boolean');

        try {
          $cli_args = $cli->parse($argv, false);
          $WerkConfig = new WerkConfig($cli_args);
          $WerkConfig->configure();
        }
        catch(Exception $e) {
            print($e->getMessage() . PHP_EOL . PHP_EOL);
            print("  OPTIONS
  --automatic, -a  No prompts.
  --help, -?       Display this help.
  --platform, -p   Target platform for this build.". PHP_EOL . PHP_EOL);
        }
    }
}