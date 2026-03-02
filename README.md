# Lot Traceability System

A web-based system designed to replace manual paper-based processes for monitoring and tracking lots through production. Significantly reduces lookup times and improves production monitoring efficiency.

## Requirements

- PHP 8.x
- Microsoft SQL Server
- XAMPP or any local web server
- Web browser

## Installation

1. Clone the repository

   ```bash
   git clone https://github.com/KyleDemavivas/traceability.git
   ```

2. Move the project folder to your server's root directory (e.g. `htdocs` for XAMPP)
3. Import the database from the `/database` folder into your MySQL server
4. Configure your database connection in the config file
5. Start Apache and MySQL via XAMPP
6. Access the system at `http://localhost/traceability`

## Features

- Lot tracking and monitoring through production
- Batch and label registration
- Lookup and search functionality with fast retrieval
- Reporting and batch lot reports
- Account settings management
- DataTables integration for efficient data display and pagination
- Centralized repair for all processes

## Tech Stack

- PHP
- MySQL
- jQuery
- HTML / CSS

## Libraries

- DataTables
- Select2
- FontAwesome
- SweetAlert2

## Problem Solved

This system digitized a manual paper-based lot tracking process used in production. Before this system, looking up lot information required searching through physical records. This system centralizes all lot data and dramatically reduces lookup times across all production processes.

## Author

Jerico Villanueva, Kyle Demavivas
