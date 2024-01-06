<?php
namespace Tivnet\WPDB\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class HiCommand
 * @package Tivnet\WPDB\Command
 */
class HiCommand extends Command {

	/**
	 * Configuration
	 *
	 * @return void
	 */
	protected function configure() {
		$this->setName( 'hi' )
		     ->setDescription( "This command prints 'Hi World!'" )
		     ->setDefinition( array(
			     new InputOption( 'flag', 'f', InputOption::VALUE_NONE, 'Raise a flag' ),
			     new InputArgument( 'activities', InputArgument::IS_ARRAY, 'Space-separated activities to perform', null ),
		     ) )
		     ->setHelp( /** @lang text */
			     "The <info>hi</info> command just prints 'Hi World!'" );
	}

	/**
	 * Executes the command
	 *
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 *
	 * @return int
	 */
	protected function execute( InputInterface $input, OutputInterface $output ) : int {
		$output->writeln( 'Hi World!' );

		return 0;
	}
}
