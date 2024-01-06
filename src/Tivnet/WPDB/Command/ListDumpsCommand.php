<?php

namespace Tivnet\WPDB\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tivnet\Console\Utils;
use Tivnet\WPDB\I;

/**
 * Class ListDumpsCommand
 *
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
			 ->setHelp( /** @lang text */
				 'The <info>list-dumps</info> shows the dump files in the `data` folder' );
	}

	/**
	 * Executes the command
	 *
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 *
	 * @return int
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$iterator = new \RegexIterator(
			new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator( getcwd() . '/' . I::$cfg->get( 'dir_dump' ) )
			),
			'/.+\.' . I::$cfg->get( 'dump_ext' ) . '.*/',
			\RegexIterator::GET_MATCH
		);

		foreach ( $iterator as $path ) {
			$output->writeln( Utils::ls_l( $path[0] ) );
		}

		return 0;
	}
}
