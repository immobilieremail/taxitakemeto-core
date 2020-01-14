# Setup

After first checkout, you need to install dependencies:

```
composer install

npm install
```

And then initialize config:

```
cp .env.example .env
php artisan key:generate
```

# Running

You start the application with:

```
php artisan serve    
npm run watch

```


# Laravel

<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

Setup development environment
--------

This project uses [Docker](https://www.docker.com/community-edition) to setup development environment.


First, [download and install Docker](https://www.docker.com/community-edition#/download) on your platform.

If you are on **Linux**, **you have to** install `docker-compose` too.

Then clone this project on your computer with git.

To build the application, move in the project directory and run
```
docker-compose build
```
It will fetch the base image from Docker Hub and install required packages and gems.

When the build is finished, you copy (and modify if needed) the `.env` file then you need to create the database. First, start the PostgreSQL container to initialize the database:
```
cp .env.example .env
docker-compose up
```
Then, you can create the database and install missing assets with:
```
docker-compose run app bin/setup
```

Update development environment
----------

To run the last version of the code from an existing development environment, you need to get the new commits, rebuild the image, update dependencies and migrate the database with the following commands:
```
git pull
docker-compose build
docker-compose run app bin/setup
```

Seed data to database
---------

To add some hotels to the database, you can use the following command:
```
docker-compose run app php artisan db:seed --class=HotelsTableSeeder
```

Use development environment
--------

To launch the application, move in the project directory and run
```
docker-compose up
```
It will launch one container for PostgreSQL, one for Redis and one for the application.
To close the containers, just press `Ctrl+C`.

If you need to run a command in a container, you can use
```
docker-compose run <container> <command>
```
`<container>` can be app, postgres or redis depending on the container you need.

The application can be accessed in your browser at [localhost:8000](http://localhost:8000/).

The emails sent by the application are accessible at [localhost:1081](http://localhost:1081/).

Kibana, used to managed Elasticsearch, can be accessed in your browser at [localhost:5601](http://localhost:5601/).
