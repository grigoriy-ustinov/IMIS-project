<?php
include("config.php");
$query = 'CREATE TABLE Login(
	Id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	Login varchar(6) NOT NULL,
	Password varchar(32) NOT NULL,
	Person_id INT NOT NULL,
	Account_type TINYTEXT,
	Care_receiver TINYINT(1),
	Care_provider TINYINT(1),
	Active TINYINT(1) DEFAULT 0);
	
	INSERT INTO Login(Login,Password,Person_id,Account_type,Active) VALUES("Admin","21232f297a57a5a743894a0e4a801fc3","1","Admin","1");
	
	CREATE TABLE Group/Person(
	Id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	Group_Id INT NOT NULL,
	Person_Id INT NOT NULL);
	
	CREATE TABLE Engine(
	Id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	Group_Id INT,
	SubGroup_Id INT,
	Task_Id INT,
	Read TINYINT(1),
	Change TINYINT(1),);
	
	CREATE TABLE Message(
	Id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	Title TINYTEXT,
	Text TEXT,
	Sender_Id INT NOT NULL,
	Receiver_Id INT,
	Group_Id INT,);
	
	CREATE TABLE Task/Group(
	Id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	Task_Id INT NOT NULL,
	Group_Id INT,);
	
	';
	
$db->Execute($query);
