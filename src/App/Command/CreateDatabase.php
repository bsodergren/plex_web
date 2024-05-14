<?php
namespace Plex\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

#[AsCommand(name: 'Create')]
class CreateDatabase extends Command
{
    protected function configure(): void
    {
        $this
            // the command description shown when running "php bin/console list"
            ->setDescription('Creates a new user.')
            // the command help shown when running the command with the "--help" option
            ->setHelp('This command allows you to create a user...')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // ... put here the code to create the user

        // this method must return an integer number with the "exit status code"
        // of the command. You can also use these constants to make code more readable

        // return this if there was no problem running the command
        // (it's equivalent to returning int(0))
        $files = $this->findSql();
        dd($files);
        return Command::SUCCESS;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;

        // or return this to indicate incorrect command usage; e.g. invalid options
        // or missing arguments (it's equivalent to returning int(2))
        // return Command::INVALID
    }

    public function findSql()
    {
        $replacements[] = Db_MEDIATAG_PREFIX;
        $replacements[] = Db_PLEXWEB_PREFIX;
        $search[] = '%%MEDIATAG_PREFIX%%';
        $search[] = '%%PLEXWEB_PREFIX%%';

        $finder = new Finder();
        $finder->files()->name('*.sql')->in(__PHP_SCHEMA_DIR__.DIRECTORY_SEPARATOR."Tables")->sortByName();
        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                $contents = $file->getContents();
                $contents = str_replace($search, $replacements, $contents);
                dd($contents);

            }
        }


        return $file_array;
    }

}
