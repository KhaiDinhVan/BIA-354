#2. Insert a record in each table by doing the following:

#Insert yourself as a new customer.
INSERT INTO customer 
VALUES (51,'Khai', 'Dinh', '2500 California Plaza', 'Omaha','NE','68178','402-601-5958','samsuper21122001@gmail.com');

#Add a new item of your choice.
INSERT INTO item (description, price)
VALUES ('Gucci vest',300 );

#Create a new invoice where you are the customer.
INSERT INTO invoice (date_in, customer_id)
VALUES ('2021-09-24',51);

#Add your new item to the invoice you just created.
INSERT INTO invoice_item 
VALUES (204, 14, 1 );

#3.Using a query, show the structure (i.e. field, data type, null, key, default, auto_increment) of the customer table.
DESCRIBE customer

#4.Change Jedidiah Bugbee's phone to 712-883-6006.
UPDATE customer
SET phone = '712-883-6006'
WHERE customer_id = 13;
# OR
UPDATE customer
SET phone = '712-883-6006'
WHERE first_name = 'Jedidiah'
AND last_name = 'Bugbee'


#5.Increase the price for dry cleaning a Blouse by 14%.
UPDATE item 
SET price = price * 1.14
WHERE item_id = 8;
#OR
UPDATE item 
SET price = price * 1.14
WHERE description = 'blouse';



#6.Show all items that cost between $2.50 and $5 to dry clean.
SELECT *
FROM item
WHERE price BETWEEN 2.50 AND 5;

#7.List the first name, last name, and phone for all customers whose second and third numbers of their phone number are 13 and their last name doesn't start with a G.
SELECT first_name, last_name, phone
FROM customer
WHERE MID(phone,2) = 13 AND last_name NOT LIKE 'G%';

#OR
SELECT first_name, last_name, phone
FROM customer
WHERE phone LIKE '_13%'
AND last_name NOT LIKE 'G%';

#8.Show all information for customers who have an email address. Sort customers by last name in ascending order and then by first name in descending order.
SELECT first_name, last_name, email
FROM customer
WHERE email IS NOT NULL
ORDER BY last_name, first_name DESC;

#9.In one query, show the total number of items and the maximum, minimum, and average unit price (round the average to two decimal places) for all items.
SELECT COUNT(item_id), MAX(price), MIN(price), ROUND(AVG(price),2)
FROM item;

#10.Show the customer with the longest email address.
SELECT first_name, last_name, email
FROM customer
ORDER BY LENGTH(email) DESC
LIMIT 1;

#11.Using the DateDiff function, show the difference between today and the date each invoice went out.
SELECT invoice_id, DATEDIFF(CURDATE(), date_out)
FROM invoice;

#12.Show the total number of invoices received on each date where the date in is after June 1, 2019.
SELECT date_in, COUNT(invoice_id)
FROM invoice
WHERE date_in > '2019-06-01'
GROUP BY date_in;
#Only use Having when there is an aggergate function in it 

#13.For each item, show the total quantity included on each invoices where the total quantity is greater than or equal to 200. Give the total quantity column an alias of total_quantity.
SELECT item_id, SUM(quantity) AS total_quantity
FROM invoice_item
GROUP BY item_id
HAVING SUM(quantity) >= 200;

#14.Remove Formal Gown from the item table.
DELETE FROM item
WHERE description = 'Formal Gown';

#Part 2: Data Control Language and Transaction Control Language
#1.Provide only the code (you won't be able to run them successfully) to the queries that would give the following privileges on the customer table to the users indicated. 
GRANT ALL
ON kvd58543.customer
TO `Owner`
WITH GRANT OPTION;
#All provileges means with Grant Option
REVOKE SELECT
ON kvd58543.customer
FROM `Human Resources`;

GRANT ALL
ON kvd58543.customer
TO `Manager`;

GRANT SELECT, UPDATE
ON kvd58543.customer
TO `Employee`;

#2.Grant all privileges on your invoice_item table to one of your classmates (net ID must be lowercase) with the ability to pass these privileges to others. Have your classmate test this access.

GRANT ALL
ON kvd58543.invoice_item
TO jcf67224
WITH GRANT OPTION;

#3.Grant the select privilege on all tables in your database to another classmate. Have your classmate test this access.
GRANT SELECT
ON kvd58543.*
TO crm72594;

#4.Remove all privileges you granted to your classmates. Have your classmates test to see if they no longer have access.
REVOKE SELECT
ON kvd58543.*
FROM crm72594;
#* table, function, PHP 

REVOKE GRANT OPTION
ON kvd58543.invoice_item
FROM jcf67224;

REVOKE ALL
ON kvd58543.invoice_item
FROM jcf67224;

SHOW GRANTS;

