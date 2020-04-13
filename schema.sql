CREATE DATABASE readme
DEFAULT CHARACTER SET utf8
DEFAULT COLLATE utf8_general_ci;
USE readme;

CREATE TABLE users (
id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(32) NOT NULL UNIQUE,
email VARCHAR(128) NOT NULL UNIQUE,
password VARCHAR(64),
avatar VARCHAR(128));

CREATE TABLE posts (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT,
type_id INT,
dt_add DATETIME,
content TEXT,
quote_author TEXT,
img_url VARCHAR(128),
youtube_url VARCHAR(128),
url VARCHAR(128),
view_count INT);

CREATE TABLE comments (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT,
post_id INT,
dt_add DATETIME,
content TEXT);

CREATE TABLE likes (
user_id INT,
post_id INT);

CREATE TABLE subscribe (
follower_id INT,
author_id INT
);

CREATE TABLE MESSAGE (
dt_add DATETIME,
content TEXT,
messenger_id INT,
receiver_id INT
);

CREATE TABLE hashtag (
id INT AUTO_INCREMENT PRIMARY KEY,
tag_name VARCHAR(128)
);

CREATE TABLE content_types  (
id INT AUTO_INCREMENT PRIMARY KEY,
type_name VARCHAR(128),
type_class VARCHAR(128)
);
