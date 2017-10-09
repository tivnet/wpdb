<?php

namespace tests\Tivnet\WPDB\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tivnet\WPDB\Command\HiCommand;

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * Class TestCommandTest
 * @package tests\Tivnet\WPDB\Command
 */
class TestCommandTest extends TestCase {

	/**
	 * Test if command returns expected string
	 */
	public function testExecute() {
		$application = new Application();
		$application->add( new HiCommand() );

		$command       = $application->find( 'hi' );
		$commandTester = new CommandTester( $command );
		$commandTester->execute( array( 'command' => $command->getName() ) );

		$this->assertRegExp( '/Hi World!/', $commandTester->getDisplay() );
	}

}
