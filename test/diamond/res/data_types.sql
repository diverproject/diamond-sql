CREATE TABLE data_type (
  var_tinyint tinyint(4) DEFAULT NULL,
  var_smallint smallint(6) DEFAULT NULL,
  var_mediumint mediumint(9) DEFAULT NULL,
  var_int int(11) NOT NULL,
  var_bigint bigint(20) DEFAULT NULL,
  var_decimal decimal(10,0) DEFAULT NULL,
  var_float float DEFAULT NULL,
  var_double double DEFAULT NULL,
  var_bit bit(1) DEFAULT NULL,
  var_boolean tinyint(1) DEFAULT NULL,
  var_char char(1) DEFAULT NULL,
  var_varchar varchar(8) DEFAULT NULL,
  var_tinytext tinytext,
  var_text text,
  var_mediumtext mediumtext,
  var_longtext longtext,
  var_time time DEFAULT NULL,
  var_date date DEFAULT NULL,
  var_datetime datetime DEFAULT NULL,
  var_timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  var_year year(4) DEFAULT NULL,
  var_blob BLOB,

  PRIMARY KEY (var_int)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO data_type (var_tinyint, var_smallint, var_mediumint, var_int, var_bigint, var_decimal, var_float, var_double, var_bit, var_boolean, var_char, var_varchar, var_tinytext, var_text, var_mediumtext, var_longtext, var_time, var_date, var_datetime, var_timestamp, var_year) VALUES
(NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);