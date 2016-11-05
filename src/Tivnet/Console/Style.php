<?php
namespace Tivnet\Console;

use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Style\SymfonyStyle;


/**
 * Same as SymfonyStyle, but quiet by default. Requires `-v` to start talking.
 */
class Style extends SymfonyStyle {

	/**
	 * Minimum verbosity level at which any output is displayed.
	 */
	const MIN_VERBOSITY = Output::VERBOSITY_VERBOSE;

	/**
	 * The error output is forced.
	 * {@inheritdoc}
	 */
	public function error( $message ) {
		$old_verbosity = $this->getVerbosity();
		$this->setVerbosity( Output::VERBOSITY_NORMAL );
		parent::error( $message );
		$this->setVerbosity( $old_verbosity );
	}

	/**
	 * {@inheritdoc}
	 */
	public function section( $message ) {
		if ( $this->getVerbosity() >= self::MIN_VERBOSITY ) {
			parent::section( $message );
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function title( $message ) {
		if ( $this->getVerbosity() >= self::MIN_VERBOSITY ) {
			parent::title( $message );
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function success( $message ) {
		if ( $this->getVerbosity() >= self::MIN_VERBOSITY ) {
			parent::success( $message );
		}
	}
}
