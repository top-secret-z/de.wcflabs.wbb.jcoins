-- only single alter tables supported
ALTER TABLE wbb1_board ADD customJCoins TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE wbb1_board ADD customJCoinsAmountCreateThread INT(10) NOT NULL DEFAULT 0;
ALTER TABLE wbb1_board ADD customJCoinsAmountCreatePost INT(10) NOT NULL DEFAULT 0;

-- since 2.1 
ALTER TABLE wbb1_board ADD customJCoinsRetractableAmountCreateThread INT(10) NOT NULL DEFAULT 0;
ALTER TABLE wbb1_board ADD customJCoinsRetractableAmountCreatePost INT(10) NOT NULL DEFAULT 0;
