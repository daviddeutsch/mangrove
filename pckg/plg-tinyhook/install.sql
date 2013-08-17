create table if not exists '#__plg_tinyhook' (
	id           serial,
	hash         varchar(255) null,
	secret       varchar(255) null,
	created_date datetime     null default '0000-00-00 00:00:00',
	path         varchar(255) null,
	callable     varchar(255) null,
	primary key (id)
);
