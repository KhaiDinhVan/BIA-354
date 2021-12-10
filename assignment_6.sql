# 1.Show item information for all items that have been included on an invoice. Show common columns only once.

SELECT *
FROM invoice
NATURAL JOIN item;


# 2.Show customers and their invoices. Include all customers whether or not they have an invoice.

SELECT *
FROM customer c
LEFT OUTER JOIN invoice i ON c.customer_id = i.customer_id;

# 3.Show customers (first and last name) that picked up (date out) their dry cleaning between September 1, 2019 and September 30, 2019.

SELECT first_name, last_name, date_out
FROM customer
INNER JOIN invoice USING (customer_id)
WHERE date_out BETWEEN '2019-09-1' AND '2019-09-30';
 

# 4.Using subqueries only, show the first name and last name of all customers who have had an invoice with an item named Dress Shirt. Present the results sorted by last name in ascending order and then first name in descending order.

SELECT first_name, last_name
FROM customer
WHERE customer_id IN (SELECT customer_id
					  FROM invoice
					  WHERE invoice_id IN ( 
					  SELECT invoice_id
					  FROM invoice_item 
					  WHERE item_id IN(
					  SELECT item_id 
					  FROM item
					  WHERE description = 'Dress Shirt')))
ORDER BY last_name ASC
				

# 5.Without entering table IDs except to connect the tables, use subqueries to change Jedidiah Bugbee's quantity of Dress Shirts included on his March 21, 2020 invoice from 6 to 3.

UPDATE invoice_item
SET quantity = '3'
WHERE quantity = '6'
AND invoice_id IN (SELECT invoice_id
				   FROM invoice
				   WHERE date_in ='2020-03-21')
AND invoice_id IN (SELECT invoice_id
				   FROM invoice
				   WHERE customer_id IN (
				   SELECT customer_id
				   FROM customer
				   WHERE first_name = 'Jedidiah' AND last_name = 'Bugbee'))
AND item_id IN (
 SELECT item_id
 FROM item
 WHERE description = 'Dress Shirt');		 
	
# 6.Show customers (first and last name) and their total number of invoices. Give the total column an alias of total_invoices.

SELECT first_name, last_name, COUNT(invoice_id) AS total_invoices
FROM customer
INNER JOIN invoice USING (customer_id)
GROUP BY first_name, last_name;



# 7.Show customers (first and last name) that have had more than $500 worth of dry cleaning done. Give the total cost an alias of total_dry_cleaning.

SELECT first_name, last_name, SUM(price * quantity) AS total_dry_cleaning
FROM customer
INNER JOIN invoice USING (customer_id)
INNER JOIN invoice_item USING (invoice_id)
INNER JOIN item USING (item_id)
GROUP BY first_name, last_name
HAVING SUM(price * quantity) > 500;

# 8.Show the invoice id, subtotal (price times quantity), tax (subtotal times 7.5% tax rate), and total (subtotal plus tax) for all invoices where the subtotal is greater than $150. Set column aliases for subtotal, tax, and total. Sort by subtotal in descending order.
SELECT invoice_id, SUM(price * quantity) AS subtotal, SUM((SUM(price * quantity) * .075)) AS tax, SUM(SUM(price * quantity) + SUM((SUM(price * quantity) * .075))) AS total

SELECT invoice_id, SUM(price * quantity) AS subtotal, SUM(subtotal * .075) AS tax,  subtotal + tax AS total
FROM invoice
INNER JOIN invoice_item USING (invoice_id)
INNER JOIN item USING (item_id)
GROUP BY invoice_id 
HAVING SUM(price * quantity) > 150
ORDER BY SUM(price * quantity) DESC;

