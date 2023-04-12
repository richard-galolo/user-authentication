<?php

namespace WebFuelAgency\UserAuthentication\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WebFuelAgency\UserAuthentication\Console\InstallsApiStack;
use WebFuelAgency\UserAuthentication\Console\InstallsWebStack;

class InstallCommand extends Command
{
    use InstallsApiStack, InstallsWebStack;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'webfuelagency:install {stack : The development stack that should be installed (api)}';
    protected $signature = 'webfuelagency:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Starter Kit controllers and resources';

    // /**
    //  * The available stacks.
    //  *
    //  * @var array<int, string>
    //  */
    // protected $stacks = ['api'];

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        $this->installApiStack();

        return $this->InstallsWebStack();

        // if ($this->argument('stack') === 'api') {
        //     return $this->installApiStack();
        // }

        // $this->components->error('Invalid stack. Supported stacks are [api].');

        // return 1;
    }

    // /**
    //  * Interact with the user to prompt them when the stack argument is missing.
    //  *
    //  * @param  \Symfony\Component\Console\Input\InputInterface  $input
    //  * @param  \Symfony\Component\Console\Output\OutputInterface  $output
    //  * @return void
    //  */
    // protected function interact(InputInterface $input, OutputInterface $output)
    // {
    //     if ($this->argument('stack')) {
    //         return;
    //     }

    //     $input->setArgument('stack', $this->components->choice('Which stack would you like to install?', $this->stacks));
    // }

    /**
     * Install tests.
     *
     * @return bool
     */
    protected function installTests()
    {
        // code test here

        return true;
    }

    /**
     * Replace a given string within a given file.
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $path
     * @return void
     */
    protected function replaceInFile($search, $replace, $path)
    {
        $content = file_get_contents($path);

        if (strpos($content, $replace) !== false) {
            return;
        }

        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }
}