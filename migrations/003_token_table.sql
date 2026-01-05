create table if not exists tokens (
    id int unsigned not null auto_increment,
    user_id int unsigned not null,
    token varchar(64) not null unique,
    created_at timestamp default current_timestamp,
    expires_at timestamp not null,
    foreign key (user_id) references users(id) on delete cascade,
    primary key (id)
);