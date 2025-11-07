create table followers (
    follower_id int not null,
    followed_id int not null,
    primary key (follower_id, followed_id),
    foreign key (follower_id) references users(id) on delete cascade,
    foreign key (followed_id) references users(id) on delete cascade
);

-- Dummy value
insert into followers (follower_id, followed_id) values (1, 2);