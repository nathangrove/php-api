
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `active` tinyint NOT NULL,
  `username` varchar(45) NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of user
-- ----------------------------
BEGIN; -- admin:admin
INSERT INTO `user` VALUES (1, 1, 'admin', '$2y$10$9t1LxFyRQH4RSC0vWyeM9.6gJyUMjkjA4Xr13XclnBzlD7wp9Z0m2', '2022-01-31 02:10:50');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
