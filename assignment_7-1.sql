#Part 1: Views
#1.Create a view called no_invoices. This view should display all information for customers who have no invoices. After creating this view, select from it and show only a list of customer emails.

DROP VIEW IF EXISTS no_invoices;
CREATE VIEW no_invoices AS
SELECT c.*, invoice_id
FROM customer c
LEFT OUTER JOIN invoice i ON c.customer_id = i.customer_id
WHERE invoice_id IS NULL;


SELECT email
FROM no_invoices;


#2.Create a view called invoice_summary. This view should display the invoice ID, date in, date out, description, quantity, and price. After creating this view, select from it while showing order summaries for those containing men's shirts where the date out was on or after October 1, 2019.

CREATE VIEW invoice_summary AS 
SELECT i.invoice_id, i.date_in, i.date_out, it.description, iv.quantity, it.price
FROM invoice i
INNER JOIN invoice_item iv ON iv.invoice_id = i.invoice_id
INNER JOIN item it ON it.item_id = iv.item_id

SELECT *  
FROM invoice_summary 
WHERE date_out >= '2019-10-1' AND description = "Men's Shirt";

#Part 2: Procedures, Functions, and Triggers
#1.Create a procedure named price_change that takes two input parameters (i.e. item ID and price). The procedure should use the item ID and price input parameters to change the price for the given item. Provide the code that would test to see if your procedure works.

DROP PROCEDURE IF EXISTS price_change;

DELIMITER $$
CREATE PROCEDURE price_change(IN vPrice DOUBLE(10,2), vItemID INTEGER)
BEGIN
  UPDATE item
  SET price = vPrice
  WHERE item_id = vItemID;
END$$
DELIMITER;

CALL price_change(2,3);

#2.Create a function named invoice_total that will return the total for a given invoice (i.e. invoice ID). Select from this function displaying the invoice ID and the total for the selected invoice. Sort the results by total in descending order.


DELIMITER $$
CREATE FUNCTION invoice_total(vInvoice INTEGER) RETURNS DOUBLE(10,2)
BEGIN
  SET @total = NULL;
  SELECT SUM(iv.quantity * it.price) INTO @total
  FROM invoice i
  INNER JOIN invoice_item iv ON iv.invoice_id = i.invoice_id
  INNER JOIN item it ON it.item_id = iv.item_id
  WHERE i.invoice_id = vInvoice;
  RETURN @total;
END$$
DELIMITER;

SELECT invoice_id, invoice_total(invoice_id) AS Total
FROM invoice
GROUP BY invoice_id
ORDER BY Total DESC;


#3.Create a trigger named invalid_date that checks if the invoice date in is not today. If this is the case supply the new record with the current date. Test to see if your trigger works.
DELIMITER $$
CREATE TRIGGER invalid_date
  BEFORE INSERT ON invoice
  FOR EACH ROW
BEGIN # <> is not
	IF new.date_in != CURDATE() THEN
	  SET new.date_in = CURDATE();
	END IF;
END$$
DELIMITER;

# TEST
INSERT INTO invoice 
VALUES (203,'2021-10-18', NULL, 3)

#Client introduction
#Overview of ERD We are interested in finding how many events the student attend


