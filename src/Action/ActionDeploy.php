<?php
namespace wegewerk\devops\WerkTool\Action;

use Garden\Cli\Cli;
use AFM\Rsync\Rsync;
use Seld\CliPrompt\CliPrompt;
use PHPGit\Git;

//include dirname(__FILE__) ."/../../vendor/autoload.php";

/**
* ActionDeploy
*/
class ActionDeploy
{

    protected $event;
    protected $deploy_type;
    protected $platform;
    protected $automatic;
    protected $no_commit;
    protected $dry_run;
    protected $deploy_status = "-unknown-";

    protected $defaults = array(
      'platform' => 'wegewerk',
    );

    function __construct($cliArgs, $event)
    {
        $this->type = $cliArgs->getCommand();
        $this->platform    = $cliArgs->getOpt('platform', $this->defaults['platform']);
        $this->automatic   = $cliArgs->getOpt('automatic', FALSE);
        $this->no_commit   = $cliArgs->getOpt('no-commit', FALSE);
        $this->dry_run     = $cliArgs->getOpt('dry-run', FALSE);

        $this->event       = $event;
    }

    function deploy()
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
        $config = $this->event->getComposer()->getConfig();
        $baseDir = dirname($config->get('vendor-dir'));

        $origin = $baseDir  .'/src/drupal/';
        $target = $baseDir  .'/deploy/int/wegewerk/htdocs';

        $OriginGit = new Git();
        $OriginGit->setRepository($origin);

        $origin_status = $OriginGit->status();

        if(empty($origin_status['changes'])) {
            // Ensure up-to-date.
            $OriginGit->pull();
            $TargetGit = new Git();
            $TargetGit->setRepository($target);
            $TargetGit->pull();

            $exclude = array(
              'sites/default/settings.php', // Always status symlink
              'sites/default/files', // Always status symlink
              '.tmp.',
              '.git',
              '.DS_Store',
              'node_modules', // These files are totally different depending on which machine they're built on.
              '.gitignore', // Ensure vendor directories are included.
            );

            $config = array(
              // Always do a dry run to start with unless automatic is selected.
              'archive'            => TRUE,
              'delete_from_target' => TRUE,
              'exclude'            => $exclude,
              'verbose'            => TRUE,
              'option_parameters'  => array('-c'),
              'show_output'        => TRUE,
            );

            $rsync = new Rsync($config);
            $rsync->sync($origin, $target);

            if($this->dry_run) {
              echo "No updates made." . PHP_EOL;
              exit(0);
            }

            if(!$this->automatic) {
              $accept = "";
              while(preg_match("/[YynN]/", $accept) !== 1) {
                echo PHP_EOL;
                echo 'Accept these changes? [yN]: ';
                $accept = CliPrompt::prompt();
              }

              if($accept === "y" || $accept === "Y") {
                echo 'Syncing deploy.' . PHP_EOL;
              }
              else {
                exit(0);
              }
            }

            $config['dry_run'] = FALSE;
            $rsync = new Rsync($config);
            $rsync->sync($origin, $target);


            if($this->no_commit) {
              echo PHP_EOL. "Updates made to local clone but no commit + push." . PHP_EOL . PHP_EOL;
              exit(0);
            }

            $target_status = $TargetGit->status();
            if(empty($target_status['changes'])) {
              print("No updates required at target.". PHP_EOL);
              exit(0);
            }
            else {
              $config = $OriginGit->config();
              $origin_url = $config['remote.origin.url'];
              $origin_log = $OriginGit->log('',NULL, array('limit' => 1));
              $origin_hash = substr($origin_log[0]['hash'], 0, 8);
              $TargetGit->add('.');
              $TargetGit->commit("Automatic werk_deploy from $origin_hash $origin_url.");
              $TargetGit->push('origin', 'master');
              print("Automatic werk_deploy from $origin_hash $origin_url.". PHP_EOL);
              exit(0);
            }
        }
        else {
            print("Can't deploy to target: Uncommitted changes in project.". PHP_EOL);
            exit(500);
        }
    }

    function __destruct()
    {
      print "Target function: ". $this->deploy_type . PHP_EOL;
      print "Target platform: ". $this->platform . PHP_EOL;
      print "Deploy status: ". $this->deploy_status . PHP_EOL;
    }
}


function dev_wegewerk($args) {
}


