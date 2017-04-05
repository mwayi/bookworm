<?php

namespace Smrtr\Bookworm;

use RuntimeException;
use Smrtr\Bookworm\DocumentStore\FilesDocumentStore;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PublishCommand extends Command
{
	/**
	 * Configure the command options.
	 *
	 * @return void
	 */
	protected function configure()
	{
		$this
			->setName('publish')
			->setDescription('Publish markdown documentation')
			->addArgument('src',  InputArgument::REQUIRED, 'The project root')
        ;
	}

	/**
	 * Execute the command.
	 *
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return void
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = new Config($input->getArgument('src'));
        new Publish($config);

		$output->writeln('<comment>Documentation published</comment>');
	}
}
