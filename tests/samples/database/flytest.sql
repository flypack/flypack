DROP TABLE IF EXISTS `city`;

CREATE TABLE `city` (
  `ID`          INT(11)  NOT NULL AUTO_INCREMENT,
  `Name`        CHAR(35) NOT NULL DEFAULT '',
  `CountryCode` CHAR(3)  NOT NULL DEFAULT '',
  `Population`  INT(11)  NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `CountryCode` (`CountryCode`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT INTO `city` VALUES ('1', 'Minsk', 'BLR', '2645500');
INSERT INTO `city` VALUES ('2', 'Gomel', 'BLR', '535229');
INSERT INTO `city` VALUES ('3', 'Mogilev', 'BLR', '380440');
INSERT INTO `city` VALUES ('4', 'Vitebsk', 'BLR', '377722');
INSERT INTO `city` VALUES ('5', 'Grodno', 'BLR', '368662');
INSERT INTO `city` VALUES ('6', 'Brest', 'BLR', '343985');
INSERT INTO `city` VALUES ('7', 'Bobruisk', 'BLR', '217940');
INSERT INTO `city` VALUES ('8', 'Moscow', 'RUS', '12500123');
INSERT INTO `city` VALUES ('9', 'Saint Petersburg', 'RUS', '5356755');
INSERT INTO `city` VALUES ('10', 'Novosibirsk', 'RUS', '1602915');
INSERT INTO `city` VALUES ('11', 'Nizhny Novgorod', 'RUS', '1264075');
INSERT INTO `city` VALUES ('12', 'Yekaterinburg', 'RUS', '1455904');
INSERT INTO `city` VALUES ('13', 'Samara', 'RUS', '1169719');
INSERT INTO `city` VALUES ('14', 'Omsk', 'RUS', '1178391');
INSERT INTO `city` VALUES ('15', 'Kazan', 'RUS', '1231878');
INSERT INTO `city` VALUES ('16', 'Ufa', 'RUS', '1454053');
INSERT INTO `city` VALUES ('17', 'Chelyabinsk', 'RUS', '1198858');
INSERT INTO `city` VALUES ('18', 'Rostov-on-Don', 'RUS', '1125299');
INSERT INTO `city` VALUES ('19', 'Perm', 'RUS', '1048005');

DROP TABLE IF EXISTS `country`;

CREATE TABLE `country` (
  `Code`           CHAR(3)      NOT NULL DEFAULT '',
  `Name`           CHAR(52)     NOT NULL DEFAULT '',
  `Continent`      CHAR(15)     NOT NULL DEFAULT '',
  `SurfaceArea`    FLOAT(10, 2) NOT NULL DEFAULT '0.00',
  `IndepYear`      SMALLINT(6)           DEFAULT NULL,
  `Population`     INT(11)      NOT NULL DEFAULT '0',
  `GovernmentForm` CHAR(45)     NOT NULL DEFAULT '',
  `Code2`          CHAR(2)      NOT NULL DEFAULT '',
  PRIMARY KEY (`Code`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;


INSERT INTO `country` VALUES
  ('BLR', 'Belarus', 'Europe', '207600.00', '1991', '9504704', 'Republic', 'BY');
INSERT INTO `country` VALUES
  ('RUS', 'Russian Federation', 'Europe', '17125191.00', '1991', '146877088', 'Federal Republic', 'RU');

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