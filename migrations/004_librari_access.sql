create table if not exists books (
    id int unsigned not null auto_increment,
    title varchar(255) not null,
    text text not null,
    owner_id int unsigned not null,
    created_at timestamp default current_timestamp,
    deleted_at timestamp null,
    foreign key (owner_id) references users(id) on delete cascade,
    primary key (id)
);

create table if not exists access (
    id int unsigned not null auto_increment,
    owner_id int unsigned not null,
    user_id int unsigned not null,
    foreign key (owner_id) references users(id) on delete cascade,
    foreign key (user_id) references users(id) on delete cascade,
    primary key (id)
    );