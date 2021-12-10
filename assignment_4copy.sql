/*Drop table from in class work */
/*DROP TABLE course, certificate;*/
/* Create table customer
a. The email is customer can be null*/

# DROP TABLE IF EXISTS
# use this to drop table and added the table again
CREATE TABLE customer (
  customer_id	INTEGER		NOT NULL	PRIMARY KEY  AUTO_INCREMENT,
  first_name	VARCHAR(50) NOT NULL,	
  last_name	VARCHAR(50) NOT NULL,
  street		VARCHAR(100) NOT NULL,
  city		VARCHAR(20) NOT NULL,
  state		CHAR(2)		NOT NULL,
  zipcode		CHAR(9)	NOT NULL,
  phone		BIGINT NOT NULL,
  email		VARCHAR(150) 
);
/* Create table item*/
CREATE TABLE item (
	item_id		   INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
  	item_description VARCHAR(250) NOT NULL
);

/* Create table complaint */
CREATE TABLE complaint (
	complaint_id INTEGER	NOT NULL	PRIMARY KEY AUTO_INCREMENT, 
  	date		   DATETIME		NOT NULL DEFAULT now(),
  	details	   TEXT		NOT NULL,
  	customer_id  INTEGER	NOT NULL,
  	item_id	   INTEGER	NOT NULL,
  	CONSTRAINT complaint_fk1 FOREIGN KEY (customer_id) REFERENCES customer(customer_id) 	ON DELETE CASCADE ON UPDATE CASCADE,
  	CONSTRAINT complaint_fk2 FOREIGN KEY (item_id) REFERENCES item(item_id)
  	ON DELETE CASCADE ON UPDATE CASCADE
);
/* Create table employee 
b. The field manager_id in employee can be null */

CREATE TABLE employee (
	employee_id INTEGER     NOT NULL	PRIMARY KEY AUTO_INCREMENT,
  	first_name  VARCHAR(50) NOT NULL,
  	last_name	  VARCHAR(50) NOT NULL,
  	street	  VARCHAR(100) NOT NULL,
  	city		  VARCHAR(20) NOT NULL,
  	state		  CHAR(2)	  NOT NULL,
  	zipcode	  CHAR(10)	  NOT NULL,
  	hire_date	  DATE		  NOT NULL,
  	manager_id  INTEGER	  ,
  	CONSTRAINT employee_fk1 FOREIGN KEY (manager_id) REFERENCES employee(employee_id)
  	ON UPDATE CASCADE
# For Unary relationship, do not do ON DELETE CASCADE
);
/* Create table invoice 
c. The field date_out in invoive be be null */

CREATE TABLE invoice (
	invoice_id   INTEGER	NOT NULL	PRIMARY KEY AUTO_INCREMENT,
  	date_in	   DATE		NOT NULL,
  	date_out	   DATE		,
  	customer_id  INTEGER	NOT NULL,
  	employee_id  INTEGER	NOT NULL,
  	CONSTRAINT invoice_fk1 FOREIGN KEY (employee_id) REFERENCES employee(employee_id)
  	ON DELETE CASCADE ON UPDATE CASCADE
);

/* Create table invoice_item */

CREATE TABLE invoice_item (
	item_id	   INTEGER  NOT NULL,
  	invoice_id   INTEGER	NOT	NULL,
  	quantity	   INTEGER	NOT	NULL,
  	PRIMARY KEY (item_id, invoice_id),
  	CONSTRAINT invoice_item_fk1 FOREIGN KEY (item_id) REFERENCES item(item_id),
  	CONSTRAINT invoice_item_fk2 FOREIGN KEY (invoice_id) REFERENCES invoice(invoice_id)
  	ON DELETE CASCADE ON UPDATE CASCADE
);

/* 2. Add a price column to the item table */
ALTER TABLE item
ADD price DOUBLE (10,2) NOT NULL;

/* 3. Add the missing constraint that should link invoice to customer. */

ALTER TABLE invoice
ADD CONSTRAINT invoice_fk2 FOREIGN KEY (customer_id) REFERENCES customer(customer_id);

/* 4. Change the item_description column in the item table to be named description with a data type of text. */

ALTER TABLE item
CHANGE item_descriptiondescription TEXT NOT NULL;

/* 5. Remove the foreign key constraint that links invoice to employee and remove the employee_id column from the invoice table. */

ALTER TABLE invoice
DROP FOREIGN KEY invoice_fk1,
DROP employee_id;

/* 6. Remove the COMPLAINT and EMPLOYEE tables from your database. */

DROP TABLE complaint, employee;