<?php

$basedir = __DIR__;
$basedir = '.';

// Step 1: Create a Phar archive named wpdb.phar
$phar = new Phar( 'C:/tools/wpdb.phar' );

// Step 2: Start buffering Phar changes
$phar->startBuffering();

foreach ( [ 'src', 'vendor' ] as $folder ) {
	$folder_path = $basedir . '/' . $folder;
	$Directory   = new RecursiveDirectoryIterator( $folder_path );
	$Iterator    = new RecursiveIteratorIterator( $Directory );

	foreach ( $Iterator as $filePath => $fileInfo ) {
		if ( is_file( $filePath ) ) {
			// Adding files only, not folders.
			$localName = substr( $filePath, strlen( $basedir ) + 1 );
			$phar->addFile( $filePath, $localName );
			echo $localName . PHP_EOL;
		}
	}
}
// Step 5: Add the index.php file from the root
$phar->addFile( './index.php', 'index.php' );

// Step 6: Set the main file (entry point) for the Phar archive
$phar->setStub( $phar->createDefaultStub( 'index.php' ) );

// Step 7: Stop buffering and save the Phar archive
$phar->stopBuffering();

echo 'Phar archive "wpdb.phar" created successfully.';
