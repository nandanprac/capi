<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 16/07/15
 * Time: 17:56
 */
require_once __DIR__.'/bootstrap.php.cache';
require_once __DIR__.'/AppKernel.php';

use Doctrine\Bundle\MigrationsBundle\Command\MigrationsMigrateDoctrineCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Input\ArrayInput;
use Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaDoctrineCommand;
use Doctrine\DBAL\Tools\Console\Command\RunSqlCommand;

$kernel = new AppKernel('test', true);
$kernel->boot();
$application = new Application($kernel);
$command = new DropDatabaseDoctrineCommand();
$application->add($command);
$input = new ArrayInput(array(
    'command' => 'doctrine:database:drop',
    '--force' => true,
));
$command->run($input, new ConsoleOutput());
$command = new CreateDatabaseDoctrineCommand();
$application->add($command);
$input = new ArrayInput(array(
    'command' => 'doctrine:database:create',
));
$command->run($input, new ConsoleOutput());

$command = new MigrationsMigrateDoctrineCommand();
$application->add($command);
$input = new ArrayInput(array(
    'command' => 'doctrine:migrations:migrate',
));
$command->run($input, new ConsoleOutput());

$command = new RunSqlCommand();
$application->add($command);
$input = new ArrayInput(array(
    'command' => 'doctrine:dbal:run-sql',
    'sql' => "INSERT INTO doctor_consult_settings VALUES (1,'Dr. Rachit Mishra','https://practo-fabric-resources.s3.amazonaws.com/5592848f2fec9a24c58b52c74fe4117d20f6e2f30f218.jpg','Bangalore',4,2,'Asia/Calcutta',NULL,NULL,127,'Dentist',1,1,NULL,'2015-06-25 15:35:08','2015-07-14 07:13:12',0),(2,'Anshuman Sinha',NULL,'Bangalore',2,5,'Asia/Mumbai',NULL,0,5,'Orthopedist',1,1,NULL,'2015-06-25 15:35:28','2015-06-25 15:35:28',0),(3,'Manoj Hans',NULL,'Bangalore',3,15,'Asia/Mumbai',NULL,0,1111100,'Orthopedic Surgeon',1,1,NULL,'2015-06-25 15:35:38','2015-06-25 15:35:38',0),(5,'Dr. Manoj Hans','null','Bangalore',5,3,'Asia/Calcutta',NULL,1,96,'Cardiologist',1,1,NULL,'2015-06-25 15:35:50','2015-07-08 11:11:10',0),(6,'Manoj Hans',NULL,'Bangalore',6,6,'Asia/Mumbai',NULL,0,1111100,'Dentist',1,1,NULL,'2015-06-25 15:35:56','2015-06-25 15:35:56',0),(7,'Manoj Hans',NULL,'Bangalore',7,7,'Asia/Mumbai',NULL,0,1111100,'Dentist',1,1,NULL,'2015-06-25 15:36:02','2015-06-25 15:36:02',0),(8,'Manoj Hans',NULL,'Bangalore',8,8,'Asia/Mumbai',NULL,0,1111100,'Dentist',0,0,NULL,'2015-06-25 15:36:07','2015-06-25 15:36:07',0),(9,'Manoj Hans',NULL,'Bangalore',9,16,'Asia/Mumbai',NULL,0,1111100,'Dentist',0,0,NULL,'2015-06-25 15:36:12','2015-06-25 15:36:12',0),(10,'Manoj Hans',NULL,'Bangalore',10,10,'Asia/Mumbai',NULL,0,1111100,'Dentist',0,0,NULL,'2015-06-25 15:36:18','2015-06-25 15:36:18',0),(11,'Manoj Hans',NULL,'Bangalore',11,11,'Asia/Mumbai',NULL,0,1111100,'Dentist',0,0,NULL,'2015-06-25 15:36:24','2015-06-25 15:36:24',0);"
));
$command->run($input, new ConsoleOutput());

$command = new RunSqlCommand();
$application->add($command);
$input = new ArrayInput(array(
    'command' => 'doctrine:dbal:run-sql',
    'sql' => "INSERT INTO user_info(practo_account_id, is_enabled) VALUES(1, 1),(2, 1);"
));
$command->run($input, new ConsoleOutput());
 
