CREATE TABLE equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_equipment VARCHAR(100) NOT NULL,
    location_id INT NOT NULL,
    status VARCHAR(50),
    keterangan TEXT,
    FOREIGN KEY (location_id) REFERENCES locations(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;