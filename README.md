# README

This README documents information for developers about the InstaPaint project.

## Requirements

This project is built in Docker containers, one for the web server and one for the database.
Check the docker-compose.yml file to see how the containers connect with each other and the volumes required.

Assuming you're on an EC2 instance running the Docker containers through ECS. You need to install Flyway on the EC2 instance to manage the database migrations, and Git to clone this repo.

Install PHPUnit to run the automated tests.

## How do I get set up?

0. SSH into the EC2 instance as ec2-user and run `umask 000` this will avoid prohibiting write permission to folders that require it
0. Clone the git repository
0. Copy `server.sett.new.php` to `server.sett.php` in `src/PF.Base/file/settings/`
	0. Change '$_CONF['core.host'] = 'localhost';' to domain name
0. `docker exec -it [IMAGE ID] /bin/bash` and install composer dependencies from the "PF.BASE" folder
0. Run `flyway migrate` from the `flyway/` folder
0. Run PHPUnit tests `phpunit tests`
0. Enable short URLs from Admin CP
0. Set the mail server from Admin CP (use TLS and port 25)
0. Create a site-wide block in Admin CP, add Google Analitycs code

## Who do I talk to?

If you have any questions contact ivan@ivandigital.com