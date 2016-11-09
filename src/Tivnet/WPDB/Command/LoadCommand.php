<?php
namespace Tivnet\WPDB\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Tivnet\Console\Style;
use Tivnet\Console\Utils;
use Tivnet\WPDB\I;

/**
 * Class LoadCommand
 * @package Tivnet\WPDB\Command
 */
class LoadCommand extends Command {

	/**
	 * Configuration
	 *
	 * @return void
	 */
	protected function configure() {
		$this->setName( 'load' )
		     ->setDescription( 'Load database from a dump file' )
		     ->setDefinition( array(
			     new InputOption( 'env', 'e', InputOption::VALUE_OPTIONAL, 'Specify the environment' ),
		     ) )
		     ->setHelp( /** @lang text */
			     'The <info>load</info> runs `mysql` to load the SQL file from the dumps folder' );
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
		$io = new Style( $input, $output );
		$io->title( $this->getDescription() );

		$env = $input->getOption( 'env' ) ?: I::$cfg->get( 'env' );

		$dump_folder = implode( '/', array(
			I::$cfg->get( 'dir_dump' ),
			$env,
		) );

		$io->section( 'Locating the dump file' );

		$file_dump            = $dump_folder . '/' . I::$cfg->get( 'dump_prefix' ) . '.' . I::$cfg->get( 'dump_ext' );
		$file_dump_escaped    = escapeshellarg( $file_dump );
		$file_dump_xz         = $file_dump . '.' . I::$cfg->get( 'ext_xz' );
		$file_dump_xz_escaped = escapeshellarg( $file_dump_xz );

		if ( ! is_file( $file_dump_xz ) ) {
			$io->error( 'The dump file cannot be found: ' . $file_dump_xz );

			return 1;
		}

		$io->writeln( Utils::ls_l( $file_dump_xz ) );

		if ( ! $io->confirm( 'Continue?', true ) ) {
			return null;
		}

		$xz_option_quiet = ( $io->getVerbosity() < Style::MIN_VERBOSITY ? 'q' : '' );

		$xz_options_decompress = '-dfk' . $xz_option_quiet;

		$io->section( 'Decompressing' );
		system( implode( ' ', array(
			I::$cfg->get( 'cmd_xz' ),
			$xz_options_decompress,
			$file_dump_xz_escaped
		) ) );

		$io->section( 'Loading' );
		$cmd = implode( ' ', array(
			'mysql',
			I::$cfg->get( 'mysql_authorization' ),
			'-h',
			I::$cfg->get( 'DB_HOST' ),
			$output->isDebug() ? '--verbose' : '',
			I::$cfg->get( 'DB_NAME' ),
			'<',
			$file_dump_escaped,
		) );

		if ( $output->isDebug() ) {
			$io->note( $cmd );
		}

		$cmd_return = 0;
		system( $cmd, $cmd_return );
		if ( 0 !== (int) $cmd_return ) {
			$io->error( "Load failed. The return code is $cmd_return" );

			return 1;
		}

		$io->section( 'Removing decompressed dump file' );
		if ( ! unlink( $file_dump ) ) {
			$io->error( 'Removing decompressed dump file failed: ' . $file_dump );
		}

		$io->success( 'Done.' );

		return null;

	}
}
