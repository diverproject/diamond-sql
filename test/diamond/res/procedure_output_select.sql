DELIMITER /

DROP PROCEDURE IF EXISTS procedure_output_select /
CREATE PROCEDURE procedure_output_select(
	OUT out_tinyint TINYINT,
	OUT out_smallint SMALLINT,
	OUT out_int INT,
	OUT out_long BIGINT,
	OUT out_float FLOAT,
	OUT out_double DOUBLE,
	OUT out_boolean BOOLEAN,
	OUT out_char CHAR,
	OUT out_varchar VARCHAR(32),
	OUT out_time TIME,
	OUT out_date DATE,
	OUT out_datetime DATETIME,
	OUT out_timestamp TIMESTAMP,
	OUT out_blob BLOB
)
BEGIN
	SELECT
		var_tinyint,
		var_smallint,
		var_int,
		var_bigint,
		var_float,
		var_double,
		var_boolean,
		var_char,
		var_varchar,
		var_time,
		var_date,
		var_datetime,
		var_timestamp,
		var_blob
	INTO
		out_tinyint,
		out_smallint,
		out_int,
		out_long,
		out_float,
		out_double,
		out_boolean,
		out_char,
		out_varchar,
		out_time,
		out_date,
		out_datetime,
		out_timestamp,
		out_blob
	FROM data_type
    LIMIT 0, 1;
END /

DELIMITER ;