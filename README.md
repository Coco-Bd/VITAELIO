# PHP Web Application

This is a simple PHP web application that includes user authentication, session management, and routing. The application allows users to register, log in, and access a dashboard. It also includes a logout functionality.

## Features

- User Registration
- User Login
- User Dashboard
- Session Management
- Logout Functionality
- Routing

## Prerequisites

- PHP 7.4 or higher
- SQLite3

### Setup

# Required

Docker desktop
PS: take care about hyper V to make sure docker work correctly.
If you have Windows pro, it should be fine, else, be sure to select : "Use WSL2 instead of hyper V during docker install

If you have an error about WSL2 when launching docker and if you're using windows pro :

on the start menu, search for "Turn windows features on off", click on it and search for hyper V, enable it and restart your computer
It may resolve the prblm.

Now, make shure you have php installed into you machine (PHP 8.2.24 or latest)

to check that, use the following command

`php --vesrion`

if you got an error, you don't have php on your machine

[PHP](https://windows.php.net/download#php-8.3)

install the one matching you device and follow php install instructions

Now, create a php folder in you C disk root, and extract .zip file you just downloaded

find php.ini-developpement file and create a copy of it.
Rename it as php.ini and open it
now, ctrl + f and search for extention_dir = "ext"
delete ; to uncomment the line.
Now, search for sqlite and uncomment extention= pdo_sqlite, extention= pdo_mysqli, extention= mysqli and extention=sqlite3
save and exit. For more infos you can read the php readme inside this folder.

Now you're done !! You can start the project

1. **Clone the repository:**

   ```
   git init
   git clone https://github.com/yourusername/your-repo.git
   ```

Be sure you've launched docker in the backgound, open cmd in the docker directory and do this :

`docker-compose build app`
`docker-compose up -d`

you should have something like this

![Lauched](/readmeResources/Screenshot%202024-11-01%20175850.png)

## IMGs

![Index](/readmeResources/Screenshot%202024-11-01%20003859.png)

![register](/readmeResources/Screenshot%202024-11-01%20003909.png)

![cv](/readmeResources/Screenshot%202024-11-01%20004051.png)

![cv_update](/readmeResources/Screenshot%202024-11-01%20004119.png)

![profile](/readmeResources/Screenshot%202024-11-01%20004320.png)

![profile with admin + name](/readmeResources/Screenshot%202024-11-01%20004342.png)
