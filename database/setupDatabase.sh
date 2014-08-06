#!/bin/bash
# Initialize the database with the schema and create a test user for the inital web log in

mysql --batch --verbose < schema.sql

# password will be 'test'
echo "insert into diet_tracker.user values ('test@test.net','test','098f6bcd4621d373cade4e832627b4f6','2000')" | mysql --batch --verbose

read -r -p 'Press [Enter] to continue...'
