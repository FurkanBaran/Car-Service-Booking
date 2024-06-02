# Car Service Booking System

This project is an appointment booking system where users can book appointments with service providers and administrators can manage these appointments. It was created based on specific requests and requirements. It was developed in Turkish to meet the client's needs. The technologies used, such as PHP for the backend, MySQL for the database, and Bootstrap and jQuery for the frontend, were selected based on the client's requests and constraints.


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
## Live Demo
https://car-service.furkanbaran.com

## Screenshots
![Ekran görüntüsü 2024-06-02 181455](https://github.com/FurkanBaran/Car-Service-Booking/assets/21145014/eb00e066-4dbf-4533-a77b-a3ebe8c6b9d4)
![Ekran görüntüsü 2024-06-02 181523](https://github.com/FurkanBaran/Car-Service-Booking/assets/21145014/e5b6e9e5-988f-4314-9fb4-15389e887505)
![Ekran görüntüsü 2024-06-02 181546](https://github.com/FurkanBaran/Car-Service-Booking/assets/21145014/e3779ddd-5c4a-41a8-9dc8-2f7311488be9)
![Ekran görüntüsü 2024-06-02 181652](https://github.com/FurkanBaran/Car-Service-Booking/assets/21145014/c1491f77-aca6-46d8-b477-ea45b61fec23)
![Ekran görüntüsü 2024-06-02 181709](https://github.com/FurkanBaran/Car-Service-Booking/assets/21145014/9c25c079-e972-4fb9-9a4e-2535ef2ea8bd)
![Ekran görüntüsü 2024-06-02 181725](https://github.com/FurkanBaran/Car-Service-Booking/assets/21145014/40810201-673e-4fab-b15f-232996b56c71)
![Ekran görüntüsü 2024-06-02 181800](https://github.com/FurkanBaran/Car-Service-Booking/assets/21145014/a4c35f21-30e1-408a-a99e-537292dfc5ac)
![Ekran görüntüsü 2024-06-02 181805](https://github.com/FurkanBaran/Car-Service-Booking/assets/21145014/3c3f3ae8-230f-4a62-8a07-61cb0d23ef78)







