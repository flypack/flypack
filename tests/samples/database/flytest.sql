DROP TABLE IF EXISTS `city`;

CREATE TABLE `city` (
  `ID`          INT(11)  NOT NULL AUTO_INCREMENT,
  `Name`        CHAR(35) NOT NULL DEFAULT '',
  `CountryCode` CHAR(3)  NOT NULL DEFAULT '',
  `District`    CHAR(20) NOT NULL DEFAULT '',
  `Population`  INT(11)  NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `CountryCode` (`CountryCode`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT INTO `city` VALUES ('1', 'Minsk', 'BLR', 'Horad Minsk', '1674000');
INSERT INTO `city` VALUES ('2', 'Gomel', 'BLR', 'Gomel', '475000');
INSERT INTO `city` VALUES ('3', 'Mogiljov', 'BLR', 'Mogiljov', '356000');
INSERT INTO `city` VALUES ('4', 'Vitebsk', 'BLR', 'Vitebsk', '340000');
INSERT INTO `city` VALUES ('5', 'Grodno', 'BLR', 'Grodno', '302000');
INSERT INTO `city` VALUES ('6', 'Brest', 'BLR', 'Brest', '286000');
INSERT INTO `city` VALUES ('7', 'Bobruisk', 'BLR', 'Mogiljov', '221000');
INSERT INTO `city` VALUES ('8', 'Moscow', 'RUS', 'Moscow (City)', '8389200');
INSERT INTO `city` VALUES ('9', 'St Petersburg', 'RUS', 'Pietari', '4694000');
INSERT INTO `city` VALUES ('10', 'Novosibirsk', 'RUS', 'Novosibirsk', '1398800');
INSERT INTO `city` VALUES ('11', 'Nizni Novgorod', 'RUS', 'Nizni Novgorod', '1357000');
INSERT INTO `city` VALUES ('12', 'Jekaterinburg', 'RUS', 'Sverdlovsk', '1266300');
INSERT INTO `city` VALUES ('13', 'Samara', 'RUS', 'Samara', '1156100');
INSERT INTO `city` VALUES ('14', 'Omsk', 'RUS', 'Omsk', '1148900');
INSERT INTO `city` VALUES ('15', 'Kazan', 'RUS', 'Tatarstan', '1101000');
INSERT INTO `city` VALUES ('16', 'Ufa', 'RUS', 'Baškortostan', '1091200');
INSERT INTO `city` VALUES ('17', 'Tšeljabinsk', 'RUS', 'Tšeljabinsk', '1083200');
INSERT INTO `city` VALUES ('18', 'Rostov-na-Donu', 'RUS', 'Rostov-na-Donu', '1012700');
INSERT INTO `city` VALUES ('19', 'Perm', 'RUS', 'Perm', '1009700');

DROP TABLE IF EXISTS `country`;

CREATE TABLE `country` (
  `Code`           CHAR(3)      NOT NULL DEFAULT '',
  `Name`           CHAR(52)     NOT NULL DEFAULT '',
  `Continent`      CHAR(15)     NOT NULL DEFAULT '',
  `Region`         CHAR(26)     NOT NULL DEFAULT '',
  `SurfaceArea`    FLOAT(10, 2) NOT NULL DEFAULT '0.00',
  `IndepYear`      SMALLINT(6)           DEFAULT NULL,
  `Population`     INT(11)      NOT NULL DEFAULT '0',
  `LifeExpectancy` FLOAT(3, 1)           DEFAULT NULL,
  `GNP`            FLOAT(10, 2)          DEFAULT NULL,
  `GNPOld`         FLOAT(10, 2)          DEFAULT NULL,
  `LocalName`      CHAR(45)     NOT NULL DEFAULT '',
  `GovernmentForm` CHAR(45)     NOT NULL DEFAULT '',
  `HeadOfState`    CHAR(60)              DEFAULT NULL,
  `Capital`        INT(11)               DEFAULT NULL,
  `Code2`          CHAR(2)      NOT NULL DEFAULT '',
  PRIMARY KEY (`Code`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;


INSERT INTO `country` VALUES
  ('BLR', 'Belarus', 'Europe', 'Eastern Europe', '207600.00', '1991', '10236000', '68.0', '13714.00', NULL, 'Belarus',
   'Republic', 'Aljaksandr Lukašenka', '3520', 'BY');
INSERT INTO `country` VALUES
  ('RUS', 'Russian Federation', 'Europe', 'Eastern Europe', '17075400.00', '1991', '146934000', '67.2', '276608.00',
          '442989.00', 'Rossija', 'Federal Republic', 'Vladimir Putin', '3580', 'RU');

DROP TABLE IF EXISTS `countrylanguage`;

CREATE TABLE `countrylanguage` (
  `CountryCode` CHAR(3)         NOT NULL DEFAULT '',
  `Language`    CHAR(30)        NOT NULL DEFAULT '',
  `IsOfficial`  ENUM ('T', 'F') NOT NULL DEFAULT 'F',
  `Percentage`  FLOAT(4, 1)     NOT NULL DEFAULT '0.0',
  PRIMARY KEY (`CountryCode`, `Language`),
  KEY `CountryCode` (`CountryCode`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;


INSERT INTO `countrylanguage` VALUES ('BLR', 'Belorussian', 'T', '65.6');
INSERT INTO `countrylanguage` VALUES ('BLR', 'Polish', 'F', '0.6');
INSERT INTO `countrylanguage` VALUES ('BLR', 'Russian', 'T', '32.0');
INSERT INTO `countrylanguage` VALUES ('BLR', 'Ukrainian', 'F', '1.3');
INSERT INTO `countrylanguage` VALUES ('RUS', 'Avarian', 'F', '0.4');
INSERT INTO `countrylanguage` VALUES ('RUS', 'Bashkir', 'F', '0.7');
INSERT INTO `countrylanguage` VALUES ('RUS', 'Belorussian', 'F', '0.3');
INSERT INTO `countrylanguage` VALUES ('RUS', 'Chechen', 'F', '0.6');
INSERT INTO `countrylanguage` VALUES ('RUS', 'Chuvash', 'F', '0.9');
INSERT INTO `countrylanguage` VALUES ('RUS', 'Kazakh', 'F', '0.4');
INSERT INTO `countrylanguage` VALUES ('RUS', 'Mari', 'F', '0.4');
INSERT INTO `countrylanguage` VALUES ('RUS', 'Mordva', 'F', '0.5');
INSERT INTO `countrylanguage` VALUES ('RUS', 'Russian', 'T', '86.6');
INSERT INTO `countrylanguage` VALUES ('RUS', 'Tatar', 'F', '3.2');
INSERT INTO `countrylanguage` VALUES ('RUS', 'Udmur', 'F', '0.3');
INSERT INTO `countrylanguage` VALUES ('RUS', 'Ukrainian', 'F', '1.3');