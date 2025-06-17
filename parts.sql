CREATE TABLE parts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_part VARCHAR(100) NOT NULL,
    location_part_id INT NOT NULL,
    equipment_id INT NOT NULL,
    keterangan TEXT,
    FOREIGN KEY (location_part_id) REFERENCES location_parts(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;