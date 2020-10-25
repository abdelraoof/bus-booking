## bus-booking

A bus-booking system with Laravel

## Requirements
- PHP >= 7.3

## How to use
- Install dependencies
```
$ composer install
```

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
