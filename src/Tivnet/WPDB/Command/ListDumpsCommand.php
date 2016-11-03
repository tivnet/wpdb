<?php
namespace Tivnet\WPDB\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Tivnet\WPDB\Config;

/**
 * Class ListDumpsCommand
 * @package Tivnet\WPDB\Command
 */
class ListDumpsCommand extends Command {

	/**
	 * Configuration
	 *
	 * @return void
	 */
	protected function configure() {
		$this->setName( 'list-dumps' )
		     ->setDescription( 'List dump files' )
		     ->setDefinition( array(
			     new InputOption( 'flag', 'f', InputOption::VALUE_NONE, 'Raise a flag' ),
			     new InputArgument( 'activities', InputArgument::IS_ARRAY, 'Space-separated activities to perform', null ),
		     ) )
		     ->setHelp( /** @lang text */
			     'The <info>list-dumps</info> shows the dump files in the `data` folder' );
	}

	/**
	 * Executes the command
	 *
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 *
	 * @return null|int
	 */
	protected function execute( InputInterface $input, OutputInterface $output ) {

		system( 'find ' . Config::$dir_dump . ' -name "*.' . Config::$dump_ext . '*" -exec ' . Config::$cmd_ls . ' {} ;' );

		return null;
	}
}
