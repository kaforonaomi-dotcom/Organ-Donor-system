# Organ Donor Management System

## Overview
The Organ Donor Management System is designed to facilitate the management of organ donors. This project focuses on the Donor Management phase, which includes functionalities for registering donors, retrieving donor information, updating donor profiles, and deleting donor records.

## Project Structure
```
organ-donor-system
├── config
│   └── database.php
├── controllers
│   └── DonorController.php
├── models
│   └── Donor.php
├── routes
│   └── donor.php
├── views
│   └── donors
│       ├── register.php
│       └── index.php
├── public
│   └── index.php
├── database
│   └── schema.sql
├── api-test.http
└── README.md
```

## Features
1. **Donor Registration**: 
   - Allows users to register as donors by providing necessary details such as `user_id`, `age`, `blood_type`, `organ`, `availability`, and `phone`.
   - Validates input fields and checks user roles to ensure only eligible users can register.
   - Prevents duplicate donor profiles for the same user.

2. **Retrieve Donors**:
   - Fetches a list of all registered donors.
   - Retrieves details of a specific donor by their ID.

3. **Update Donor Information**:
   - Allows updates to donor profiles, including changes to age and availability.

4. **Delete Donor Profile**:
   - Provides functionality to delete a donor's profile from the system.

## API Endpoints
- **POST /donors**: Register a new donor.
- **GET /donors**: Retrieve all donors.
- **GET /donors/{id}**: Retrieve a specific donor by ID.
- **PUT /donors/{id}**: Update donor information.
- **DELETE /donors/{id}**: Delete a donor profile.

## Testing
Use the `api-test.http` file to test the functionalities of the donor management API. The file contains sample requests for each of the implemented features.

## Future Development
After successful testing of the Donor Management phase, the project will move to Phase 2, which will include additional functionalities and enhancements.