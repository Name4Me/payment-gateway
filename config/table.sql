-- Don't change %tbName%
CREATE TABLE IF NOT EXISTS %tbName% (
    FullName varchar(25) COLLATE utf8_bin NOT NULL,
    PaymentID varchar(30) COLLATE utf8_bin  NOT NULL,
    Details varchar(255) COLLATE utf8_bin,
    DateTime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ID int(11) NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (ID)
  )
  ENGINE=MyISAM
  DEFAULT CHARSET=utf8
  COLLATE=utf8_bin
  AUTO_INCREMENT=1;