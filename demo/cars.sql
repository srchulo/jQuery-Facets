DROP TABLE IF EXISTS `cars`;

CREATE TABLE `cars` (
  `id` int(11) ,
  `make` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `condition` varchar(255) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `cars` (`id`, `make`, `model`, `year`, `condition`, `price`, `color`, `image`) VALUES (1,'BMW','M3',1998,'used',6000,'silver','bmw_m3_1998_silver.jpg');
INSERT INTO `cars` (`id`, `make`, `model`, `year`, `condition`, `price`, `color`, `image`) VALUES (2,'Mini','Cooper',2010,'new',20000,'red','mini_cooper_2010_red.jpg');
INSERT INTO `cars` (`id`, `make`, `model`, `year`, `condition`, `price`, `color`, `image`) VALUES (3,'Audi','A4',2003,'like_new',7000,'blue','audi_a4_2003_blue.jpg');
INSERT INTO `cars` (`id`, `make`, `model`, `year`, `condition`, `price`, `color`, `image`) VALUES (4,'Audi','Q7',2007,'new',10000,'silver','audi_q7_2007_silver.jpg');
INSERT INTO `cars` (`id`, `make`, `model`, `year`, `condition`, `price`, `color`, `image`) VALUES (5,'BMW','325i',2002,'like_new',5500,'white','bmw_325i_2002_white.jpg');
INSERT INTO `cars` (`id`, `make`, `model`, `year`, `condition`, `price`, `color`, `image`) VALUES (6,'Mini','Clubman',2010,'really_used',15000,'black','mini_clubman_2010_black.jpg');
INSERT INTO `cars` (`id`, `make`, `model`, `year`, `condition`, `price`, `color`, `image`) VALUES (7,'Cadillac','El Dorado',1997,'really_used',2000,'green',NULL);
INSERT INTO `cars` (`id`, `make`, `model`, `year`, `condition`, `price`, `color`, `image`) VALUES (8,'Maserati','Spyder',2012,'new',50000,'silver','maserati_spyder_2012_silver.jpg');
INSERT INTO `cars` (`id`, `make`, `model`, `year`, `condition`, `price`, `color`, `image`) VALUES (9,'Chevrolet','Avalanche',2004,'used',3000,'black',NULL);
INSERT INTO `cars` (`id`, `make`, `model`, `year`, `condition`, `price`, `color`, `image`) VALUES (10,'BMW','Z4',2003,'used',9000,'silver','bmw_z4_2003_silver.jpg');
INSERT INTO `cars` (`id`, `make`, `model`, `year`, `condition`, `price`, `color`, `image`) VALUES (11,'Audi','A8',2014,'new',45000,'white','audi_a8_2014_white.jpg');
