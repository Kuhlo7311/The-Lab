-- Drop database if it exists
DROP DATABASE IF EXISTS RecyclingDB;

-- Create database
CREATE DATABASE RecyclingDB;
USE RecyclingDB;

-- Table: RecyclingCenter
CREATE TABLE RecyclingCenter (
    CenterID INT AUTO_INCREMENT PRIMARY KEY,
    CenterName VARCHAR(255),
    Address VARCHAR(255),
    PC INT,
    Town VARCHAR(255),
    OpenStart TIME,
    OpenStop TIME
);

-- Table: TypeWaste
CREATE TABLE TypeWaste (
    TypeID INT AUTO_INCREMENT PRIMARY KEY,
    Description VARCHAR(255),
    Price DECIMAL(10, 2)
);

-- Table: Invoice
CREATE TABLE Invoice (
    InvoiceID INT AUTO_INCREMENT PRIMARY KEY,
    DateInvoice DATETIME,
    IsPaid VARCHAR(255),
    ToPay DECIMAL(10, 2)

);

-- Table: MeasurementStation
CREATE TABLE MeasurementStation (
    StationID INT AUTO_INCREMENT PRIMARY KEY,
    Address VARCHAR(255),
    Description VARCHAR(255),
    CenterID INT,
    TypeID INT,
    FOREIGN KEY (CenterID) REFERENCES RecyclingCenter(CenterID),
    FOREIGN KEY (TypeID) REFERENCES TypeWaste(TypeID)
);

-- Table: RecyclingUser
CREATE TABLE RecyclingUser (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    UserName VARCHAR(25),
    Name VARCHAR(60),
    Email VARCHAR(255),
    UserType ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    RandomCode VARCHAR(10),
    Password VARCHAR(255),
    FavoriteCenter INT,
    FOREIGN KEY (FavoriteCenter) REFERENCES RecyclingCenter(CenterID)
);

-- Table: WasteMeasurement
CREATE TABLE WasteMeasurement (
    MeasurementID INT AUTO_INCREMENT PRIMARY KEY,
    Weight DECIMAL(10, 2),
    DateMeasure DATETIME,
    InvoiceID INT,
    StationID INT,
    TypeID INT,
    UserID INT,
    FOREIGN KEY (InvoiceID) REFERENCES Invoice(InvoiceID),
    FOREIGN KEY (StationID) REFERENCES MeasurementStation(StationID),
    FOREIGN KEY (TypeID) REFERENCES TypeWaste(TypeID),
    FOREIGN KEY (UserID) REFERENCES RecyclingUser(UserID)
);

-- Relationships
ALTER TABLE WasteMeasurement
ADD CONSTRAINT FK_WasteMeasurement_Invoice
FOREIGN KEY (InvoiceID) REFERENCES Invoice(InvoiceID);

ALTER TABLE WasteMeasurement
ADD CONSTRAINT FK_WasteMeasurement_TypeWaste
FOREIGN KEY (TypeID) REFERENCES TypeWaste(TypeID);


-- Insert data into TypeWaste table
INSERT INTO TypeWaste (Description, Price)
VALUES ('Plastic', 0.10);

-- Insert data into Invoice table
INSERT INTO Invoice (DateInvoice, IsPaid)
VALUES ('2023-01-15 12:00:00', 'Yes');

-- Insert data into RecyclingCenter table
INSERT INTO RecyclingCenter (CenterName, Address, PC, Town, OpenStart, OpenStop)
VALUES ('Recycling Center Ville de Luxembourg', '48 Rue de Stade', 1140, 'Luxembourg', '08:00:00', '19:00:00');

-- Insert data into RecyclingCenter table
INSERT INTO RecyclingCenter (CenterName, Address, PC, Town, OpenStart, OpenStop)
VALUES ('Recycling Center Dudelange', 'N31 Rte de Luxembourg', 3285, 'Dudelange', '08:00:00', '17:50:00');

INSERT INTO RecyclingCenter (CenterName, Address, PC, Town, OpenStart, OpenStop)
VALUES ('Recycling Center Mondorf', '2.Pass Bernad Simminger', 5655, 'Mondorf-les-bains', '08:00:00', '18:00:00');

INSERT INTO RecyclingCenter (CenterName, Address, PC, Town, OpenStart, OpenStop)
VALUES ('Hein Sàrl', '1 Quai de la Moselle', 5405, 'Bech-Kleinmacher Schengen', '8:00:00', '17:00:00');

-- Insert data into TypeWaste table
INSERT INTO TypeWaste (Description, Price)
VALUES ('Plastic', 0.10);

INSERT INTO TypeWaste (Description, Price)
VALUES ('Glass', 0.20);

INSERT INTO TypeWaste (Description, Price)
VALUES ('Tech', 1.10);

INSERT INTO TypeWaste (Description, Price)
VALUES ('Scrap', 0.50);

INSERT INTO TypeWaste (Description, Price)
VALUES ('Organic', 0.15);

-- Insert data into MeasurementStation table
INSERT INTO MeasurementStation (Address, Description, CenterID, TypeID)
VALUES ('N31 Rte de Luxembourg', 'Scrap Station Dudelange', 2, 5);


