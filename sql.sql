CREATE DATABASE vis_project;
USE mysql;
CREATE USER 'vis_project'@'localhost' IDENTIFIED BY 'vis_project';
GRANT ALL PRIVILEGES ON vis_project.* TO 'vis_project'@'localhost';
GRANT ALL PRIVILEGES ON vis_project.* TO 'vis_project'@'%';
FLUSH PRIVILEGES;
USE vis_project;
DROP TABLE data_table_1;
CREATE TABLE data_table_1(
id bigint not null unique auto_increment,
key_column varchar(70) primary key,
vin varchar(50),
vehicle_mode varchar(50),
odometer varchar(20),
speed varchar(20),
soc varchar(20),
dte varchar(20),
ac_status varchar(10),
data_timestamp datetime,
response_timestamp datetime,
time_gap time,
zero_gap_frequency int,
filename varchar(200),
updated_on timestamp)
ENGINE=InnoDB;

CREATE TABLE data_table_2(
id bigint not null unique auto_increment,
key_column varchar(70) primary key,
vin varchar(50),
latitude varchar(50),
longitude varchar(50),
latitude_direction varchar(10),
longitude_direction varchar(10),
gps_validity_flag varchar(10),
gps_speed int,
data_timestamp datetime,
response_timestamp datetime,
time_gap time,
zero_gap_frequency int,
filename varchar(200),
updated_on timestamp)
ENGINE=InnoDB;

CREATE TABLE data_table_3(
id bigint not null unique auto_increment,
key_column varchar(70) primary key,
vin varchar(50),
ttc varchar(20),
door_open_indicator varchar(20),
parking_brake varchar(20),
gear_position varchar(20),
data_timestamp datetime,
response_timestamp datetime,
time_gap time,
zero_gap_frequency int,
filename varchar(200),
updated_on timestamp)
ENGINE=InnoDB;


select * from data_table_1;
select * from data_table_2;
select * from data_table_3;
truncate table data_table_1;

SELECT data_timestamp, zero_gap_frequency FROM data_table_1 WHERE vin='MA1LSEW79J2A80058' && response_timestamp<='2018-04-26 08:49:00' order by response_timestamp desc limit 0,1;

SELECT vin, COUNT(id) as NoOfAPI from data_table_1 WHERE response_timestamp>='2018-04-01 00:00:00' && response_timestamp<='2018-04-30 23:59:59' GROUP BY vin;