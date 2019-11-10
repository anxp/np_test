CREATE TABLE fmscan_nptest.logs (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	ip_address VARCHAR(45) NOT NULL,
	start_date_timestamp INT UNSIGNED NOT NULL,
	end_date_timestamp INT UNSIGNED NOT NULL,
	date_diff INT NOT NULL,
	time_spent FLOAT NOT NULL,
	CONSTRAINT logs_PK PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_general_ci;
