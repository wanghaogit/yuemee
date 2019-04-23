
DELIMITER |||
DROP FUNCTION IF EXISTS `RAND_STRING`|||
CREATE FUNCTION `RAND_STRING`(n INT) RETURNS varchar(64) CHARSET utf8
BEGIN
    DECLARE chars_str varchar(62) DEFAULT 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    DECLARE return_str varchar(64) DEFAULT '';
    DECLARE i INT DEFAULT 0;
    WHILE i < n AND i < 60 DO
        SET return_str = concat(return_str,substring(chars_str , FLOOR(1 + RAND()*62 ),1));
        SET i = i +1;
    END WHILE;
    RETURN return_str;
END |||

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

DROP FUNCTION IF EXISTS `BASE62_ENCODE` |||
CREATE FUNCTION `BASE62_ENCODE`(val BIGINT UNSIGNED) RETURNS varchar(16) CHARSET utf8
BEGIN
	DECLARE MASK VARCHAR(62) DEFAULT '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	DECLARE B62 VARCHAR(16) DEFAULT '';
	DECLARE I BIGINT DEFAULT 0;
	IF val = 0 THEN
		RETURN '0';
	END IF;
	WHILE val > 0 DO
		IF val < 62 THEN
			SET B62 = CONCAT(SUBSTR(MASK FROM val + 1 FOR 1),B62);
			SET val = 0;
		ELSE
			SET I = FLOOR(val / 62);
			SET val = val - I * 62;
			SET B62 = CONCAT(SUBSTR(MASK FROM val + 1 FOR 1),B62);
			SET val = I;
		END IF;
	END WHILE;
	RETURN B62;
END|||

DROP FUNCTION IF EXISTS `BASE62_DECODE` |||
CREATE FUNCTION `BASE62_DECODE`(str VARCHAR(16)) RETURNS BIGINT UNSIGNED
BEGIN
	DECLARE MASK VARCHAR(62) DEFAULT '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	DECLARE v BIGINT UNSIGNED DEFAULT 0;
	DECLARE i TINYINT UNSIGNED;
	DECLARE l TINYINT UNSIGNED;
	DECLARE t BIGINT UNSIGNED;
	DECLARE c CHAR(1);
	SET l = LENGTH(str);
	SET i = 1;
	SET v = 0;
	WHILE i <= l DO
		SET c = SUBSTR(str FROM i FOR 1);
		SET t = LOCATE(BINARY c,MASK) - 1;
		SET v = v * 62 + t;
		SET i = i + 1;
	END WHILE;

	RETURN v;
END|||
