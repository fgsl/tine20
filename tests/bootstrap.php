<?php

$paths = array(
realpath(dirname(__FILE__)),
realpath(dirname(__FILE__) . '/../lib'),
get_include_path()
);
set_include_path(implode(PATH_SEPARATOR, $paths));

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);

function getTestDatabase()
{
    if (file_exists('/tmp/syncope_test.sq3')) {
        unlink('/tmp/syncope_test.sq3');
    }
    
    // create temp database by default 
    $params = array (
    	#'dbname' => '/tmp/syncope_test.sq3',
    	'dbname' => ':memory:'
    );
    
    $db = Zend_Db::factory('PDO_SQLITE', $params);
    
    // enable foreign keys
    #$db->query('PRAGMA foreign_keys = ON');
    
    $db->query("CREATE TABLE IF NOT EXISTS `syncope_devices` (
        `id` varchar(40) NOT NULL,
        `deviceid` varchar(64) NOT NULL,                                                                                                                                                                         
        `devicetype` varchar(64) NOT NULL,
        `policykey` varchar(64) DEFAULT NULL,
        `owner_id` varchar(40) NOT NULL,
        `acsversion` varchar(40) NOT NULL,
        `pinglifetime` int(11) DEFAULT NULL,
        `remotewipe` int(11) DEFAULT '0',
        PRIMARY KEY (`id`)
	)");

    $db->query("CREATE TABLE IF NOT EXISTS `syncope_folderstates` (
        `id` varchar(40) NOT NULL,
        `device_id` varchar(64) NOT NULL,
        `class` varchar(64) NOT NULL,
        `folderid` varchar(254) NOT NULL,
        `creation_time` datetime NOT NULL,
        `lastfiltertype` int(11) DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE (`device_id`,`class`,`folderid`)
	)");
    
    $db->query("CREATE TABLE `syncope_synckeys` (
    	`id` varchar(40) NOT NULL,
      	`device_id` varchar(64) NOT NULL,
      	`type` varchar(64) NOT NULL,
      	`counter` int(11) NOT NULL DEFAULT '0',
      	`lastsync` datetime NOT NULL,
      	`pendingdata` longblob,
      	PRIMARY KEY (`id`),
      	UNIQUE (`device_id`,`type`,`counter`)
    )");
    
    $db->query("CREATE TABLE `syncope_contentstates` (
        `id` varchar(40) NOT NULL,
        `device_id` varchar(64) NOT NULL,
        `class` varchar(64) NOT NULL,
        `contentid` varchar(64) NOT NULL,
        `collectionid` varchar(254) NOT NULL,
        `creation_time` datetime NOT NULL,
        `is_deleted` int(11) DEFAULT '0',
        PRIMARY KEY (`id`),
        UNIQUE (`device_id`,`class`,`collectionid`,`contentid`)
    )");
    
    return $db;
}