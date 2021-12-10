# 1. Create table 

# Create student table 
CREATE TABLE student (
 net_id VARCHAR(8) NOT NULL PRIMARY KEY,
 first_name VARCHAR(50) NOT NULL,
 last_name VARCHAR(50) NOT NULL,
 email VARCHAR(150) NOT NULL,
 phone BIGINT NOT NULL,
 gender CHAR(1)
);

# Create receptionist table
CREATE TABLE receptionist (
 receptionist_id VARCHAR(8) NOT NULL PRIMARY KEY,
 first_name VARCHAR(50) NOT NULL,
 last_name VARCHAR(50) NOT NULL,
 phone BIGINT NOT NULL,
 email VARCHAR(150) NOT NULL
);

# Create guest table
CREATE TABLE guest (
 guest_id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
 first_name VARCHAR(50) NOT NULL,
 last_name VARCHAR(50) NOT NULL,
 gender CHAR(1) NOT NULL
);



# Create equipment table
CREATE TABLE equipment (
  equipment_id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  replacement_cost DOUBLE(10,0) NOT NULL,
  receptionist_id VARCHAR(8) NOT NULL,
  CONSTRAINT equipment_fk1 FOREIGN KEY (receptionist_id) REFERENCES receptionist(receptionist_id)
  ON DELETE CASCADE ON UPDATE CASCADE
);
  


# Create guest_log table 
CREATE TABLE guest_log (
 receptionist_id VARCHAR(8) NOT NULL,
 guest_id INTEGER NOT NULL,
 net_id VARCHAR(8) NOT NULL,
 check_in DATETIME NOT NULL,
 check_out DATETIME,
 notes VARCHAR(255),
 PRIMARY KEY (receptionist_id, guest_id, net_id),
 CONSTRAINT guest_log_fk1 FOREIGN KEY (receptionist_id) REFERENCES receptionist(receptionist_id),
 CONSTRAINT guest_log_fk2 FOREIGN KEY (guest_id) REFERENCES guest(guest_id),
 CONSTRAINT guest_log_fk3 FOREIGN KEY (net_id) REFERENCES student(net_id)
 ON DELETE CASCADE ON UPDATE CASCADE
);
  

  
# Create rental table 
CREATE TABLE rental (
  rental_id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
  check_out DATETIME NOT NULL,
  check_in DATETIME,
  notes TEXT,
  fees DOUBLE(10,0), 
  net_id VARCHAR(8) NOT NULL,
  receptionist_id VARCHAR(8) NOT NULL,
  CONSTRAINT rental_fk1 FOREIGN KEY (net_id) REFERENCES student(net_id),
  CONSTRAINT rental_fk2 FOREIGN KEY (receptionist_id) REFERENCES receptionist(receptionist_id)
  ON DELETE CASCADE ON UPDATE CASCADE
);


# 2a. Remove the foreign key link from receptionist to equipment and remove receptionist ID from equipment. 
ALTER TABLE equipment
DROP FOREIGN KEY equipment_fk1;

ALTER TABLE equipment
DROP receptionist_id;

# 2b. Add equipment ID to the rental table and link this equipment ID to the equipment table. 
ALTER TABLE rental
ADD equipment_id INTEGER NOT NULL,
ADD CONSTRAINT rental_fk3 FOREIGN KEY (equipment_id) REFERENCES equipment(equipment_id) ON DELETE CASCADE ON UPDATE CASCADE;

# 3. Insert the following records (let auto-increment handle the IDs where applicable):
# Add yourself as a student.

INSERT INTO student
VALUES ('kvd58543', 'Khai', 'Dinh', 'kvd58543@gmail.edu', 4443335858, 'M');

# Add an equipment of your choice.

INSERT INTO equipment (name, replacement_cost)
VALUES ('Badminton Racket', 30);

# Add a rental by you for the equipment you created.

INSERT INTO rental (check_out, check_in, net_id, receptionist_id, equipment_id)
VALUES ('2021-10-28 17:39:33','2021-10-29 19:39:33', 'kvd58543', 'jdr87699', 13);


# 4. Using a subquery, for equipment items rented to Norman Dignam that are Missing, change the check in to now and notes to Late.

UPDATE rental
SET check_in = NOW(), notes = 'Late'
WHERE notes = 'Missing' AND net_id IN (
  SELECT net_id
  FROM student
  WHERE first_name = 'Norman'
  AND last_name = 'Dignam'
);


# 5. Show equipment with the word base in the name.

SELECT name
FROM equipment
WHERE name LIKE '%base%';

# 6. Show female guests that were checked in. Show common columns only once.

SELECT * 
FROM guest
NATURAL JOIN guest_log
WHERE gender = 'F';



# 7. Show students (first and last name) who have not checked in any guests.

SELECT first_name, last_name, net_id
FROM student
LEFT OUTER JOIN guest_log USING (net_id)
WHERE check_in IS NULL; 



# 8. Without using the OR operator, show students that rented baseballs, basketballs, soccer balls, or volleyballs after October 10, 2021. Show the results by first name in ascending order.

SELECT first_name, last_name, name, check_out
FROM student
INNER JOIN rental USING (net_id)
INNER JOIN equipment USING (equipment_id)
WHERE name IN ('Baseball', 'Basketball', 'Soccer Ball', 'Volleyball') 
AND check_out > '2021-10-10'
ORDER BY first_name;



# 9. Show each student's net ID, first name, last name, and the number of times they have checked in guests. Only return the two students that have checked in the most guests. Give the guests checked in column an alias of total_guests.

SELECT net_id, first_name, last_name, COUNT(check_in) AS total_guests
FROM student
INNER JOIN guest_log USING(net_id)
GROUP BY net_id 
ORDER BY COUNT(check_in) DESC
LIMIT 2;


# 10. Show the student's first name, last name, and the total unique equipment items checked out for students whose names start with D. Only show those students who have checked out between 5 and 10 items. Give the total column an alias of total_items. Sort the results by the total column in ascending order. 

SELECT first_name, last_name, COUNT(DISTINCT equipment_id) AS total_items
FROM student
INNER JOIN rental USING (net_id)
INNER JOIN equipment USING (equipment_id)
GROUP BY first_name
HAVING first_name LIKE 'D%' AND total_items BETWEEN 5 AND 10
ORDER BY total_items;


# 11. Create a view called late_fees that shows the student's net ID, first name, last name, and total fees for items that are late. Give the total column an alias of total_fees. After creating the view, show those with at least $5 in late fees.

DROP VIEW IF EXISTS late_fees;

CREATE VIEW late_fees AS 
SELECT net_id, first_name, last_name, SUM(fees) AS total_fees, notes
FROM student
INNER JOIN rental USING (net_id)
GROUP BY net_id
HAVING notes = 'Late';

SELECT *
FROM late_fees
HAVING total_fees >= 5;



# 12. Create a procedure named add_fees that takes a rental ID and fee amount as input parameters. This procedure should (1) get the value for check_in of the given rental and store it in a variable, (2) check if this variable is null and, if so, increase the fees for the given rental by the given fees, and (3) display the given rental. Provide the code that would execute the procedure using 12 as the rental ID and 5 as the fees.



DROP PROCEDURE IF EXISTS add_fees;
DELIMITER $$
CREATE PROCEDURE add_fees(IN vrentalID INTEGER, vfee DOUBLE(10,0))
BEGIN
Set @check_in = NULL;
SELECT check_in 
FROM rental 
WHERE rental_id = vrentalID INTO @check_in;
	IF @check_in IS NULL THEN
		UPDATE rental
		SET fees = fees + vfee
		WHERE rental_id = vrentalID;
	END IF;
SELECT *
FROM rental
WHERE rental_id = vrentalID;

END$$
DELIMITER;

CALL add_fees(12, 5);


# 13. Delete all guest check ins for the receptionist Janetta Drain. Note: You will need to use a subquery or a join. 

DELETE gl.*
FROM guest_log gl
INNER JOIN receptionist r ON r.receptionist_id = gl.receptionist_id
WHERE first_name = 'Janetta' AND last_name = 'Drain';



# 14.Remove all newly created objects (e.g. tables, views, procedures, functions, triggers) from your schema.

DROP TABLE student, receptionist, guest, equipment, guest_log, rental;

DROP VIEW IF EXISTS late_fees;

DROP PROCEDURE add_fees;


## Use drop talbe 

# 15. Provide only the code (because you don't have enough privileges) for the following queries:
# 15a.Create the schema log.
CREATE SCHEMA `log`;

# 15b. Create the user facility_director. Make sure this user has a password.

CREATE USER `facility_director`
IDENTIFIED BY 'creighton';


# 15c. Give the following privileges on the tables indicated below to the user facility_director. Use all privileges when all four privileges apply. Also, assume that the user facility_director currently has all privileges on the student table and SELECT and DELETE privileges on the guest table:

GRANT ALL
ON log.guest_log
TO `facility_director`
WITH GRANT OPTION;

GRANT SELECT, INSERT, UPDATE
ON log.receptionist
TO `facility_director`;

REVOKE DELETE
ON `log`.guest
FROM `facility_director`;


REVOKE GRANT OPTION
ON `log`.student
FROM `facility_director`;

REVOKE ALL
ON `log`.student
FROM `facility_director`;



