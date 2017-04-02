<?php

namespace Smrtr\Bookworm;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewCommand extends Command
{
	/**
	 * Configure the command options.
	 *
	 * @return void
	 */
	protected function configure()
	{
		$this
			->setName('new')
			->setDescription('Create new document')
			->addArgument('project', InputArgument::REQUIRED, 'The project root')
			->addArgument('path', InputArgument::REQUIRED, 'The file path')
			->addArgument('template', InputArgument::OPTIONAL, 'Template');
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
		new Template(
			new Config($input->getArgument('project'))
		);

		$output->writeln('<comment>Documentation published</comment>');
	}
}
