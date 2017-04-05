## Laravel PayPal Demo

This is a sample laravel application containing demo express checkout workflow integration.


## Installation
* First simply clone this repo by using following command:
```
git clone https://github.com/srmklive/laravel-paypal-demo.git [your-directory]
```

* Now navigate to the directory you cloned the repo into and run the following command
```
composer install
```

* Publish configuration & views:
```
php artisan vendor:publish
```

* Create .env file
```
mv .env.example .env
```

* Set application key
```
php artisan key:generate
```

* Set your database credentials.

* Migrate the databases:
```
php artisan migrate
```

## Documentation

This application uses the [Laravel PayPal](https://github.com/srmklive/laravel-paypal) package. You can find the documentation for the package [here](https://github.com/srmklive/laravel-paypal/blob/master/README.md).