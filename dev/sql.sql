CREATE TABLE Users (
    id INT AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    PRIMARY KEY (id)
);
INSERT INTO Users (name, age) VALUES ('John Doe', 30);
INSERT INTO Users (name, age) VALUES ('Asela', 21);
INSERT INTO Users (name, age) VALUES ('Dimath', 22);