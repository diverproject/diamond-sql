DELIMITER /

DROP PROCEDURE IF EXISTS procedure_input_update /
CREATE PROCEDURE procedure_input_update(
	IN in_tinyint TINYINT,
	IN in_smallint SMALLINT,
	IN in_int INT,
	IN in_long BIGINT,
	IN in_float FLOAT,
	IN in_double DOUBLE,
	IN in_boolean BOOLEAN,
	IN in_char CHAR(1),
	IN in_varchar VARCHAR(32),
	IN in_time TIME,
	IN in_date DATE,
	IN in_datetime DATETIME,
	IN in_timestamp TIMESTAMP,
	IN in_blob BLOB
)
BEGIN
	UPDATE data_type
	SET
		var_tinyint = in_tinyint,
		var_smallint = in_smallint,
		var_int = in_int,
		var_bigint = in_long,
		var_float = in_float,
		var_double = in_double,
		var_boolean = in_boolean,
		var_char = in_char,
		var_varchar = in_varchar,
		var_time = in_time,
		var_date = in_date,
		var_datetime = in_datetime,
		var_timestamp = in_timestamp,
		var_blob = in_blob;
	COMMIT;
END /

DELIMITER ;