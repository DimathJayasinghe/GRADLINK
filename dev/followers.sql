create table followers (
    follower_id int not null,
    followed_id int not null,
    primary key (follower_id, followed_id),
    foreign key (follower_id) references users(id) on delete cascade,
    foreign key (followed_id) references users(id) on delete cascade
);

-- table to find weather user has a public profile or not
CREATE TABLE user_profiles_visibility (
    user_id INT NOT NULL,
    is_public BOOLEAN NOT NULL DEFAULT TRUE,
    PRIMARY KEY (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


-- Dummy value
insert into followers (follower_id, followed_id) values (1, 2);