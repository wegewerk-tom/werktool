<?php

namespace wegewerk\devops\WerkTool\Action;
use Garden\Cli\Cli;
use AFM\Rsync\Rsync;
use Seld\CliPrompt\CliPrompt;
use PHPGit\Git;

/**
* ActionConfig
*/
class ActionConfig
{

    protected $type;
    protected $platform;
    protected $automatic;

    protected $defaults = array(
      'platform' => 'wegewerk',
    );

    function __construct($cli_args)
    {
        $this->type = $cli_args->getCommand();
        $this->platform   = $cli_args->getOpt('platform', $this->defaults['platform']);
        $this->automatic  = $cli_args->getOpt('automatic', FALSE);
        $this->no_commit  = $cli_args->getOpt('no-commit', FALSE);
        $this->dry_run    = $cli_args->getOpt('dry-run', FALSE);
    }

    function configure()
    {
        $method_name = $this->type .'_'. $this->platform;
        if(method_exists($this, $method_name)) {
            call_user_func_array(array($this, $method_name), array());
            exit(0);
        }
        else {
            print("Unimplemented platform / type combination.". PHP_EOL);
            exit(500);
        }
    }

    function int_wegewerk() {
        // Instance generation for hand-off to Ansible. Not-yet-implemented.
        $defaults = array(
          'database_name' => 'test',
          'database_user' => 'test',
          'htauth_user'   => 'test',
        );

        echo 'Database name ['. $defaults['database_name'] .']: ';
        $this->config['db_name'] = CliPrompt::prompt();

        echo 'Database user ['. $defaults['database_name'] .']: ';
        $this->config['db_user'] = CliPrompt::prompt();

        echo 'Database password: ';
        $this->config['db_pass'] = CliPrompt::hiddenPrompt();

        echo 'HTTP auth user ['. $defaults['htauth_user'] .']: ';;
        $this->config['htauth_user'] = CliPrompt::prompt();

        echo 'HTTP auth password: ';
        $this->config['htauth_pass'] = CliPrompt::hiddenPrompt();
    }

    function __destruct()
    {
      print(json_encode($this->config) .PHP_EOL);
    }
}


function dev_wegewerk($args) {
}


