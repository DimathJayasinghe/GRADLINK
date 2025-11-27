 create table notifications (
    id int auto_increment primary key,
    receiver_id int not null,
    type varchar(50) not null,
    reference_id int not null,
    content text not null,
    is_read boolean default false,
    created_at timestamp default current_timestamp,
    
    foreign key (receiver_id) references users(id) on delete cascade,
    index idx_user_id (receiver_id),
    index idx_is_read (is_read)
 );