# Laravel Application Setup

This guide outlines the steps to set up and run the Laravel application.

## Prerequisites
Ensure you have the following installed on your system:
- PHP (>=8.0)
- Composer
- Node.js and npm
- A database server , XAMPP WITH MYSQL

---

## Installation Steps

1. **Create a New `.env` File**
   Copy the `.env.example` file to create a new `.env` file:
   ```bash
   cp .env.example .env

Configure the Database
Open the .env file and set the database credentials:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
Set Timezone
In the .env file, set the application's timezone to Asia/Manila:

APP_TIMEZONE=Asia/Manila
Install Composer Dependencies
Run the following command to install all PHP dependencies:

run "composer install"
Install NPM Dependencies
Install the required Node.js dependencies:

run "npm install"
Generate the Application Key
Generate a new application key for encryption:

run "php artisan key:generate"
Run Database Migrations
Set up the database tables by running migrations:

run "php artisan migrate"
Link Storage
Create a symbolic link for file storage:

run "php artisan storage:link"
Running the Application
Start the Laravel development server:

run "php artisan serve"
To serve the project
