CREATE DATABASE chat default charset utf8;

use chat;

create table user
(
    id mediumint unsigned not null auto_increment,
    username varchar(20) not null,
    password char(60) not null,
    primary key(id)
)engine=Innodb;

insert into user values(1,'tom','123123'),(2,'jack','123123');