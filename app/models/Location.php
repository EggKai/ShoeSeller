<?php
require_once __DIR__ . '/../../core/Model.php';

class Location extends Model {
    public function getAllLocations() {
        return $this->findAll('store_locations');
    }

    /**
     * Insert a new location into the store_locations table.
     *
     * @param string $name        Location name.
     * @param string $address     Location address.
     * @param string $country     Location country.
     * @param string $zip_code    Location zipcode.
     * @param string $phone       Location business number.
     * @param float $latitude     Location lat.
     * @param float $longitude    Location long.
     * @return int|false          The new location's ID on success or false on failure.
     */
    public function createLocation($name, $address, $country, $zip_code, $phone, $latitude, $longitude) {
        $stmt = $this->pdo->prepare("
            INSERT INTO store_locations (name, address, country, zip_code, phone, latitude, longitude)
            VALUES (:name, :address, :country, :zip_code, :phone, :latitude, :longitude)
        ");

        $params = [
            'name' => $name,
            'address' => $address,
            'country' => $country,
            'zip_code' => $zip_code,
            'phone' => $phone,
            'latitude' => $latitude,
            'longitude' => $longitude
        ];

        if ($stmt->execute($params)) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    public function updateLocation($id, $name, $address, $country, $zip_code, $phone, $latitude, $longitude)
    {
        $sql = "UPDATE store_locations SET 
            name = :name, 
            address = :address, 
            country = :country, 
            zip_code = :zip_code, 
            phone = :phone, 
            latitude = :latitude, 
            longitude = :longitude 
        WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $params = [
            'name' => $name,
            'address' => $address,
            "country" => $country,
            'zip_code' => $zip_code,
            'phone' => $phone,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'id' => $id
        ];

        return $stmt->execute($params);
    }

    public function deleteLocation($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM store_locations WHERE id = :id");
        return $stmt->execute([
            'id' => $id
        ]);
    } 
}