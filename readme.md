<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

## About Silicon Laravel API Boilerplate
This is a boilerplate for writing RESTful API projects using Laravel. The aim of this boilerplate is to provide developers with scaffolding and common functionality which will make writing APIs exceedingly quick, efficient and convenient.

It is intended for this repository to be used when starting a new API project. Therefore, instead of cloning the laravel repository, you should clone this one.

The principles of this boilerplate are to;

 - Save developers considerable effort by using reasonable conventions
 - Allow for everything the boilerplate provides to be easily extended and entirely customised to suit developer needs, through normal PHP inheritance
   - As well as allow developers to easily use the boilerplate functionality and mix it in with their own implementation
 - Follow REST standards very closely
 - Use existing Laravel features and existing Laravel add-on packages where possible
 - Add many convenient features useful for writing APIs
 - Maintain a high level of performance

## Documentation
For setup, usage guidance, and all other docs - please consult the [Project Wiki](https://laravel.com/docs/).

## Requirements
  - docker
    
## Steps to configure project
1) Clone project from [github](https://github.com/ishansilicon/silicon-laravel-boiler-plate.git)
2) Run command make local and change DATA_PATH_HOST folder name. also change projectapp in composer, php-artisan, phpunit with docker-compose.yml's projectapp's replaced name.
3) Change port numbers as par requirement in docker-compose.yml settings in your.env file.
4) Run "docker-composer build" for install docker dependency .
5) Run "docker-composer up -d" for run project in docker.
6) Run "docker-composer ps" for check project working properly on docker or not.
7) Run "./php-artisan migrate" for migrate database
8) Run "./php-artisan db:seed" for seed database
9) Now your project is ready to work with.
10) Make sure that you have run project in same port of .env file APP_URL default it has port number 8007
11) It has in built swagger and audit tool check swagger on api/documentation and for audit check audit table.


## Check out the documentation of supporting projects

Every great project stands on the shoulders of giants. Check out the documentation of these key supporting packages to learn more;

 - [Laravel](https://laravel.com/docs/)
 - [Tymon JWT Auth](https://github.com/tymondesigns/jwt-auth)
 - [Laravel UUID](https://github.com/webpatser/laravel-uuid/tree/2.1.1)

