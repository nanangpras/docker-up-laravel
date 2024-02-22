CREATE TABLE bumbu (
    id INT PRIMARY KEY auto_increment,
    nama varchar(255),
    stock FLOAT,
    berat FLOAT,
    created_at TIMESTAMP,
	updated_at TIMESTAMP,
	deleted_at TIMESTAMP NULL
);

CREATE TABLE bumbu_details (
    id INT PRIMARY KEY auto_increment,
		bumbu_id INT,
		bumbu_customer_id,
		regu VARCHAR(15),
		stock FLOAT,
        berat FLOAT,
		status VARCHAR(10),
		tanggal DATE,
        created_at TIMESTAMP,
		updated_at TIMESTAMP,
		deleted_at TIMESTAMP NULL
);

CREATE TABLE customer_bumbu (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT,
    bumbu_id INT,
    status_bumbu INT,
    INDEX(customer_id),
    INDEX (bumbu_id),
    created_at TIMESTAMP,
	updated_at TIMESTAMP,
	deleted_at TIMESTAMP NULL
);

ALTER TABLE free_stocktemp
ADD bumbu_id INT AFTER selonjor;

ALTER TABLE free_stocktemp
ADD bumbu_berat FLOAT AFTER bumbu_id;