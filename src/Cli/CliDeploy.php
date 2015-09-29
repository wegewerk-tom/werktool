<?php

namespace wegewerk\devops\WerkTool\Cli;

use Garden\Cli\Cli;
use wegewerk\devops\WerkTool\Action\ActionDeploy;

//include dirname(__FILE__) ."/../../vendor/autoload.php";

/**
* CliDeploy
*/
class CliDeploy
{
    function __construct($event)
    {
        // Define the cli options.
        $cli = new Cli();

        $cli->description('A simple wrapper for deploy systems.')
            // Define the first command: dev.
            ->command('dev')
            ->description('Type: development.')
            // Define the first command: int.
            ->command('int')
            ->description('Type: integration.')
            // Some global options.
            ->command('*')
            ->opt('platform:p', 'Target platform for this deploy.', false, 'string')
            ->opt('dry-run:d', 'Dry run.', false, 'boolean')
            ->opt('no-commit:n', 'Skip commit and push.', false, 'boolean')
            ->opt('automatic:a', 'No prompts.', false, 'boolean');

        try {
            $argv = $event->getArguments();
            array_unshift($argv, __FILE__);
            $cliArgs = $cli->parse($argv, false);
            $ActionDeploy = new ActionDeploy($cliArgs, $event);
            $ActionDeploy->deploy();
        }
        catch(Exception $e) {
            print($e->getMessage() . PHP_EOL . PHP_EOL);
            print("  OPTIONS
    --automatic, -a  No prompts.
    --dry-run, -d    Dry run.
    --help, -?       Display this help.
    --no-commit, -n  Skip commit and push.
    --platform, -p   Target platform for this deploy.". PHP_EOL . PHP_EOL);
        }
    }
}
