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

- The app will be live at http://bus-booking.local or http://homestead.test *(based on your [Hostname Resolution](https://laravel.com/docs/8.x/homestead#hostname-resolution) configuration)*

#### Option #2: Using PHP server

- Run PHP server
```
$ php artisan serve
```

- The app will be live at http://localhost:8000
