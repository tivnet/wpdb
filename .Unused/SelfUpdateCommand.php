<?php

namespace Unused;

use Herrera\Json\Exception\FileException;
use Herrera\Phar\Update\Manager;
use Herrera\Phar\Update\Manifest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SelfUpdateCommand
 * @package Tivnet\WPDB\Command
 */
class SelfUpdateCommand extends Command {

	/**
	 * Update path
	 */
	const MANIFEST_FILE = 'http://tivnet.github.io/wpdb/manifest.json';

	/**
	 * Configuration
	 * @return void
	 */
	protected function configure() {
		$this
			->setName( 'selfupdate' )
			->setDescription( 'Updates wpdb.phar to the latest version' )
			->addOption( 'major', null, InputOption::VALUE_NONE, 'Allow major version update' );
	}

	/**
	 * Executes the update command
	 *
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 *
	 * @return null|int
	 */
	protected function execute( InputInterface $input, OutputInterface $output ) {
		$output->writeln( 'Looking for updates...' );

		try {
			$manager = new Manager( Manifest::loadFile( self::MANIFEST_FILE ) );
		} catch ( FileException $e ) {
			$output->writeln( /** @lang text */
				'<error>Updates could not be fetched</error>' );

			return 1;
		}

		$currentVersion = $this->getApplication()->getVersion();
		$allowMajor     = $input->getOption( 'major' );

		if ( $manager->update( $currentVersion, $allowMajor ) ) {
			$output->writeln( /** @lang text */
				'<info>Updated to latest version</info>' );
		} else {
			$output->writeln( /** @lang text */
				'<comment>Already up-to-date</comment>' );
		}

		return null;
	}
}
