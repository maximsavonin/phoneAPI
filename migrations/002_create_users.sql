create table if not exists users (
    id int unsigned not null auto_increment,
    username varchar(50) not null unique,
    password varchar(255) not null,
    primary key (id)
);