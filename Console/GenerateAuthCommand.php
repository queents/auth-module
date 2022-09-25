<?php

namespace Modules\Auth\Console;

use Illuminate\Console\Command;
use Modules\Auth\Helpers\Auth\AuthGenerator;
use Nwidart\Modules\Facades\Module;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateAuthCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'vilt:auth {module} {table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Full Auth To This Module ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $module = $this->argument('module');
        $table = $this->argument('table');
        $check = Module::find($module);

        if (!$check) {
            $this->error('Module not exists we will create it for you');
            exit;
        }
        try {
            $types=['web','api','both'];
            $loginTypes=['email','phone'];

            $authType = $this->ask('you want create web or api or both (api & web) ?');
            if (!in_array($authType, $types)) {
                $this->error('Please Insert Correct Type : web or api or both (api & web) ?');
                exit;
            }
            $loginType = $this->ask('you want login with phone or email ?');

            if (!in_array($loginType, $loginTypes)) {
                $this->error('Please Insert Correct Type Of Login : phone or email ?');
                exit;
            }

            $newGenerator = new AuthGenerator($table, $module);

            if($authType == 'api'){
                $newGenerator->generateApi();
            }

            if($authType == 'web'){
                $newGenerator->generateWeb();
            }

            $this->info('Generated Full Auth & Login With'.' '.$loginType .' '.' on the '.' '.$authType .' '.'Stage' );

        } catch (Exception $e) {
            $this->error($e);
        }

        return Command::SUCCESS;

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['module', InputArgument::REQUIRED, 'An example argument.'],
            ['table', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['module', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
            ['table', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
