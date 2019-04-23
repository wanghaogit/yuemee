
UPDATE yuemi_sale.spu SET att_refund = 0;
UPDATE yuemi_sale.spu SET att_refund = 1 WHERE catagory_id IN (1, 101, 102, 103, 104, 105, 106, 107);
