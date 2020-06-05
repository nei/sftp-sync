<?php

use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$log = new Monolog\Logger('name');
$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::DEBUG));
$log->debug('Starting sync of the sftp servers');

/**
 * I only care about recent files, so ignore all the others
 */
$dateFilter = new DateTime();
$dateFilter->setTimestamp(strtotime('-7 days'));

$syncedFiles = [];
if ($file = @file_get_contents('syncedFiles.json')) {
    $syncedFiles = json_decode($file);
}

$sftpFrom = new Filesystem(new SftpAdapter([
    'host' => $_ENV['FROM_HOST'],
    'port' => $_ENV['FROM_PORT'],
    'username' => $_ENV['FROM_USERNAME'],
    'password' => $_ENV['FROM_PASSWORD'],
    'timeout' => $_ENV['FROM_TIMEOUT'],
]));

$sftpTo = new Filesystem(new SftpAdapter([
    'host' => $_ENV['TO_HOST'],
    'port' => $_ENV['TO_PORT'],
    'username' => $_ENV['TO_USERNAME'],
    'password' => $_ENV['TO_PASSWORD'],
    'timeout' => $_ENV['TO_TIMEOUT'],
]));

$directories = $sftpTo->listContents($_ENV['INIT_PATH']);

$copiedFiles = [];
foreach ($directories as $object) {
    $log->debug('Looking for files at '.$object['path']);
    $files = array_filter($sftpTo->listContents($object['path']), function($file) use ($syncedFiles, $dateFilter) {
        $fileDate = new DateTime("@{$file['timestamp']}");
        $isFileRecent = $fileDate >= $dateFilter;
        $hasBeenSynced = in_array($file['path'], $syncedFiles);
        // skip rules
        return $file['filename'] !== 'Processed' && $isFileRecent && !$hasBeenSynced;
    });

    if (empty($files)) {
        $log->debug('No files to copy.');
    }

    foreach ($files as $file) {
        if (!$sftpFrom->has($file['path']) ) {
            $log->debug('Copying file '.$file['path'].' to staging.');

            // get file from one server
            $stream = $sftpTo->readStream($file['path']);
            $contents = stream_get_contents($stream);
            fclose($stream);

            // flag file as synced
            $syncedFiles[] = $file['path'];
            $copiedFiles[] = $file['path'];

            // write file to another server
            $putStream = tmpfile();
            fwrite($putStream, $contents);
            rewind($putStream);
            $sftpFrom->putStream($file['path'], $putStream);
            if (is_resource($putStream)) {
                fclose($putStream);
            }
        }
    }
}

if (empty($copiedFiles)) {
    $log->debug('No files were copied. all in sync.');
}else{
    $log->debug(count($copiedFiles.' files has been copied.');
}

// save the files that has been synced
$syncedFiles = file_put_contents('syncedFiles.json', json_encode($syncedFiles, JSON_PRETTY_PRINT));

$log->debug('end');