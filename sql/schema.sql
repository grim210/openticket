create table ot_groups (
	group_id serial primary key,
	group_name varchar(32) not null
);

insert into ot_groups (group_id, group_name) values (0, 'New Users');

create table ot_users (
	user_id serial primary key,
	user_name varchar(64) unique,
	user_hash varchar(256) not null,
	user_email varchar(256),
	user_created timestamptz not null default '2017-01-01 08:00:00.00',
	user_group integer references ot_groups(group_id) default 0
);

-- Ticket status.  Just putting in the basics
create table ot_ticket_status (
	status_id serial primary key,
	status_text varchar(32) not null
);

insert into ot_ticket_status (status_text) values ('New');
insert into ot_ticket_status (status_text) values ('Assigned');
insert into ot_ticket_status (status_text) values ('Troubleshooting');
insert into ot_ticket_status (status_text) values ('Testing');
insert into ot_ticket_status (status_text) values ('Resolved');

create table ot_tickets (
	ticket_id bigserial primary key,
	ticket_created_by integer references ot_users(user_id),
	ticket_assigned_group integer,
	ticket_created_time timestamptz not null,
	ticket_text text not null default 'Empty Text'
);
