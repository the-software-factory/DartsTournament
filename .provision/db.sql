CREATE DATABASE IF NOT EXISTS `darts_tournament` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE darts_tournament;
GRANT ALL ON darts_tournament.* TO 'root'@'%';

CREATE TABLE IF NOT EXISTS `players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(45) NOT NULL,
  `last_name` varchar(45) NOT NULL,
  `games_won` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

DELETE FROM players WHERE 1;
INSERT INTO players SET first_name = 'Marco', last_name = 'Matarazzi';
INSERT INTO players SET first_name = 'Andrea', last_name = 'Sprega';
INSERT INTO players SET first_name = 'Riccardo', last_name = 'Bastianini';
INSERT INTO players SET first_name = 'Luca', last_name = 'Abbati';

-- Testing DB
DROP DATABASE IF EXISTS `darts_tournament_test`;
CREATE DATABASE IF NOT EXISTS `darts_tournament_test` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE darts_tournament_test;
GRANT ALL ON darts_tournament_test.* TO 'root'@'%';

-- Copying table structures
CREATE TABLE `darts_tournament_test`.`players` LIKE `darts_tournament`.`players`;
