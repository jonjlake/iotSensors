DROP DATABASE IF EXISTS sensors;
CREATE DATABASE IF NOT EXISTS sensors;
use sensors;

DROP TABLE IF EXISTS thermistors;
CREATE TABLE IF NOT EXISTS thermistors (
  record INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(64),
  location VARCHAR(64),
  a_working FLOAT,
  b_working FLOAT,
  c_working FLOAT,
  a_calc FLOAT,
  b_calc FLOAT,
  c_calc FLOAT) ENGINE MyISAM;
 
DROP TABLE IF EXISTS test_measurements; 
CREATE TABLE IF NOT EXISTS test_measurements (
  voltage FLOAT,
  temperature FLOAT,
  thermistor INT UNSIGNED,
  FOREIGN KEY(thermistor) REFERENCES thermistors(record)) ENGINE MyISAM;  
