<?php
namespace Tivnet\WPDB\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Tivnet\Console\Style;
use Tivnet\WPDB\I;

/**
 * Class DumpCommand
 * @package Tivnet\WPDB\Command
 */
class DumpCommand extends Command {

	/**
	 * Configuration
	 *
	 * @return void
	 */
	protected function configure() {
		$this->setName( 'dump' )
		     ->setDescription( 'Dump database to a file' )
		     ->setDefinition( array(
			     new InputOption( 'flag', 'f', InputOption::VALUE_NONE, 'Raise a flag' ),
			     new InputArgument( 'activities', InputArgument::IS_ARRAY, 'Space-separated activities to perform', null ),
		     ) )
		     ->setHelp( /** @lang text */
			     'The <info>dump</info> runs `mysqladmin` to dump the entire database to a file in the `data` folder' );
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

		$dump_folder = implode( '/', array(
			I::$cfg->get( 'dir_dump' ),
			I::$cfg->get( 'env' ),
		) );

		$io->section( 'Creating the dump folder' );

		if ( ! @mkdir( $dump_folder, null, true ) && ! is_dir( $dump_folder ) ) {
			$io->error( 'Cannot create folder: ' . $dump_folder );

			return 1;
		}

		$io->section( 'Dumping the database' );

		$file_dump = $dump_folder . '/' . I::$cfg->get( 'dump_prefix' ) . '.' . I::$cfg->get( 'dump_ext' );

		$cmd = implode( ' ', array(
			'mysqldump',
			I::$cfg->get( 'mysql_authorization' ),
			$output->isDebug() ? '--verbose' : '',
			'--hex-blob --no-create-db --extended-insert=FALSE --add-drop-table --quick',
			I::$cfg->get( 'dump_ignore' ),
			'--result-file',
			$file_dump,
			I::$cfg->get( 'DB_NAME' )
		) );

		if ( $output->isDebug() ) {
			$io->note( $cmd );
		}

		system( $cmd );

		if ( ! is_file( $file_dump ) ) {
			$io->error( 'The dump file cannot be found: ' . $file_dump );

			return 1;
		}

		if ( $io->getVerbosity() >= Style::MIN_VERBOSITY ) {
			$io->section( 'The dump file:' );
			system( I::$cfg->get( 'cmd_ls' ) . ' ' . $file_dump );
			system( I::$cfg->get( 'cmd_tail' ) . ' ' . $file_dump );
		}

		$file_dump_xz = "${file_dump}.xz";

		$xz_option_quiet = ( $io->getVerbosity() < Style::MIN_VERBOSITY ? 'q' : '' );

		$xz_options_compress = '-f' . $xz_option_quiet;
		$xz_options_verify   = '-t' . $xz_option_quiet;

		$io->section( 'Compressing' );
		system( implode( ' ', array(
			I::$cfg->get( 'cmd_xz' ),
			$xz_options_compress,
			$file_dump
		) ) );

		$io->section( 'Verifying' );
		$cmd_return = 0;
		system( implode( ' ', array(
			I::$cfg->get( 'cmd_xz' ),
			$xz_options_verify,
			$file_dump_xz
		) ), $cmd_return );
		if ( $cmd_return ) {
			$io->error( "Verify failed. The return code is $cmd_return" );

			return 1;
		}

		if ( ! $output->isQuiet() ) {
			$io->section( 'Done.' );
			system( implode( ' ', array(
				I::$cfg->get( 'cmd_ls' ),
				$file_dump_xz
			) ) );
		}

		/*
		 * Without `ls
				$output->writeln( implode( ' ', array(
					filesize( $file_dump ),
					date( 'Y-m-d H:i', filemtime( $file_dump ) ),
					$file_dump,
				) ) );
		*/

		return null;
	}
}
