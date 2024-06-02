# Appointment Booking System

This project is an appointment booking system where users can book appointments with service providers and administrators can manage these appointments.

## Features

- Users can create either a service provider or customer account.
- Service providers can set their working hours and specify the types of vehicles they service (SUV, truck, car, etc.).
- Service providers can view incoming appointment requests and accept or reject them.
- Customers can enter information such as city, district, and vehicle type in a form to see available services.
- Customers can select a service, choose a date and time, and send an appointment request if everything is available.
- Customers can view their appointment requests along with their statuses in a table, and they can also cancel appointments.
- Customers can edit their profiles.
- The service search form uses jQuery/AJAX to dynamically update the form fields based on the selected data, providing a seamless user experience.


## Technologies Used

- **Frontend:** HTML, CSS (Bootstrap), JavaScript (jQuery)
- **Backend:** PHP
- **Database:** MySQL

## Installation

To set up the project locally, follow these steps:

### Requirements

- PHP 7.x or later
- MySQL 5.x or later
- Web server (Apache, Nginx, etc.)

### Steps

1. **Clone the Repository**

   ```sh
   git clone https://github.com/FurkanBaran/Car-Service-Booking.git
   cd Car-Service-Booking
    ```

2. **Database Configuration**

    Create a database in MySQL and update the db_connection.php file with your database connection details.

    ```php
    <?php
    $host = "localhost";
    $dbname = "your_database_name";
    $user = "your_database_username";
    $pass = "your_database_password";
    ?>
    ```

 3. **Create Database Tables**

    Use the [car_service.sql](car_service.sql) file to create database and tables.

4. **Configure Web Server**
    ```sh
    cp -r Car-Service-Booking /path/to/your/webserver/htdocs/
    ```
5. **Run the Project**
    Open your web browser and navigate to http://localhost/Car-Service-Booking to run the project. You can now test the functionality and make sure everything is working as expected.

## Screenshots
