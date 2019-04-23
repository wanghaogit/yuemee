DELIMITER |||

DROP FUNCTION IF EXISTS `NEW_ORDER_ID`|||
CREATE FUNCTION `NEW_ORDER_ID`(
	Pfx VARCHAR(1),
	Cat VARCHAR(1)
) RETURNS varchar(18) CHARSET utf8
BEGIN
    DECLARE chars_str varchar(36) DEFAULT 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    DECLARE return_str varchar(18) DEFAULT '';

	IF LENGTH(Pfx) > 0 THEN
		SET return_str = SUBSTRING(Pfx,1,1);
	ELSE
		SET return_str = '';
	END IF;
	IF LENGTH(Cat) > 0 THEN
		SET return_str = CONCAT(return_str,SUBSTRING(Cat,1,1));
	END IF;
	SET return_str = CONCAT( return_str , YEAR(NOW()) - 2000);
	IF MONTH(NOW()) < 10 THEN
		SET return_str = CONCAT( return_str , '0');
	END IF;
	SET return_str = CONCAT ( return_str , MONTH(NOW()));
	IF DAY(NOW()) < 10 THEN
		SET return_str = CONCAT( return_str , '0');
	END IF;
	SET return_str = CONCAT ( return_str , DAY(NOW()));
    WHILE LENGTH(return_str) < 16 DO
        SET return_str = CONCAT(return_str,substring(chars_str , FLOOR(1 + RAND()*36 ),1));
    END WHILE;
    RETURN return_str;
END |||
