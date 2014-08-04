create database diet_tracker;

use diet_tracker;

create table user
(
    email varchar(255) not null,
    name varchar(255) not null,
    password varchar(255) not null,
    calorie_target int not null,
    primary key(email)
);

create table food
(
    ndb_no int not null,
    name text not null,
    usda_report_url text not null,
    primary key(ndb_no)
);

create table frequently_eats
(
    user varchar(255) not null,
    food int not null,
    count int,
    constraint pk_frequently_eats primary key (user, food),
    foreign key (user) references user(email),
    foreign key (food) references food(ndb_no)
);

create table weight_measurement
(
    date datetime not null,
    amount real not null,
    user varchar(255) not null,
    foreign key (user) references user(email)
);

create table meal
(
    meal_id int not null auto_increment,
    date datetime not null,
    amount real not null,
    user varchar(255) not null,
    primary key(meal_id),
    foreign key (user) references user(email)
);

create table food_report
(
    metric varchar(255) not null,
    value real not null,
    calories real not null,
    meal int not null,
    food int not null,
    foreign key (meal) references meal(meal_id),
    foreign key (food) references food(ndb_no)
);
