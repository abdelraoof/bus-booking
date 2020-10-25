## bus-booking

A bus-booking system with Laravel

## Requirements

- PHP >= 7.3

## How to use

- Install dependencies
```
$ composer install
```

- Set the application encryption key
```
$ cp .env.example .env
$ php artisan key:gen
```

#### Option #1: Using Vagrant

- Make Homestead settings file
```
$ vendor/bin/homestead make
```

- Edit Homestead.yaml to your liking *(edit memory & cpus values)*
```
$ nano Homestead.yaml
```

- Launch the Vagrant box
```
$ vagrant up
```

- Run the database migrations
```
$ vagrant ssh
$ cd code
$ php artisan migrate
$ exit
```

- Seed the database with records *(optional)*
```
$ vagrant ssh
$ cd code
$ php artisan db:seed
$ exit
```

- The app will be live at http://bus-booking.local or http://homestead.test *(based on your [Hostname Resolution](https://laravel.com/docs/8.x/homestead#hostname-resolution) configuration)*

#### Option #2: Using PHP server

- Edit DB_* env vars
```
$ nano .env
```

- Run the database migrations
```
$ php artisan migrate
```

- Seed the database with records *(optional)*
```
$ php artisan db:seed
```

- Run PHP server
```
$ php artisan serve
```

- The app will be live at http://localhost:8000
