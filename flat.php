<?php
class Flat {
    private $flat_id;
    private $flat_ref;
    private $owner_id;
    private $location;
    private $address;
    private $price;
    private $start_date;
    private $end_date;
    private $bedrooms;
    private $bathrooms;
    private $size;
    private $furnished;
    private $heating;

    private $parking;
    private $is_approved;
    private $is_rented;
    private $image;

    // Constructor
    public function __construct(
        $flat_id, $flat_ref, $owner_id, $location, $address, $price,
        $start_date, $end_date, $bedrooms, $bathrooms, $size, $furnished,
        $heating, $parking, $is_approved, $is_rented, $image
    ) {
        $this->flat_id = $flat_id;
        $this->flat_ref = $flat_ref;
        $this->owner_id = $owner_id;
        $this->location = $location;
        $this->address = $address;
        $this->price = $price;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->bedrooms = $bedrooms;
        $this->bathrooms = $bathrooms;
        $this->size = $size;
        $this->furnished = $furnished;
        $this->heating = $heating;
       
        $this->parking = $parking;
       
        $this->is_approved = $is_approved;
        $this->is_rented = $is_rented;
        $this->image = $image;
    }

    // Display in Table
    public function displayInTable() {
        $imagePath = "image/{$this->image}";
        return "<tr>
            <td>{$this->flat_id}</td>
            <td>{$this->flat_ref}</td>
            <td>
                <a href='details.php?flat_id={$this->flat_id}'>
                    <img src='{$imagePath}' width='150' onerror=\"this.onerror=null;this.src='images/default.png';\">
                </a>
            </td>
            <td>{$this->owner_id}</td>
            <td>{$this->location}</td>
            <td>{$this->address}</td>
            <td>{$this->price}</td>
            <td>{$this->start_date}</td>
            <td>{$this->end_date}</td>
            <td>{$this->bedrooms}</td>
            <td>{$this->bathrooms}</td>
             <td>{$this->size}</td>
            <td>{$this->furnished}</td>
            <td>{$this->heating}</td>
            <td>{$this->parking}</td>
            <td>{$this->is_approved	}</td>
           
        </tr>";
    }

    // Getters and Setters
    public function getFlatId() { return $this->flat_id; }
    public function setFlatId($flat_id) { $this->flat_id = $flat_id; }

    public function getFlatRef() { return $this->flat_ref; }
    public function setFlatRef($flat_ref) { $this->flat_ref = $flat_ref; }

    public function getOwnerId() { return $this->owner_id; }
    public function setOwnerId($owner_id) { $this->owner_id = $owner_id; }

    public function getLocation() { return $this->location; }
    public function setLocation($location) { $this->location = $location; }

    public function getAddress() { return $this->address; }
    public function setAddress($address) { $this->address = $address; }

    public function getPrice() { return $this->price; }
    public function setPrice($price) { $this->price = $price; }

    public function getStartDate() { return $this->start_date; }
    public function setStartDate($start_date) { $this->start_date = $start_date; }

    public function getEndDate() { return $this->end_date; }
    public function setEndDate($end_date) { $this->end_date = $end_date; }

    public function getBedrooms() { return $this->bedrooms; }
    public function setBedrooms($bedrooms) { $this->bedrooms = $bedrooms; }

    public function getBathrooms() { return $this->bathrooms; }
    public function setBathrooms($bathrooms) { $this->bathrooms = $bathrooms; }

    public function getSize() { return $this->size; }
    public function setSize($size) { $this->size = $size; }

    public function getFurnished() { return $this->furnished; }
    public function setFurnished($furnished) { $this->furnished = $furnished; }

    public function getHeating() { return $this->heating; }
    public function setHeating($heating) { $this->heating = $heating; }

    public function getParking() { return $this->parking; }
    public function setParking($parking) { $this->parking = $parking; }

    public function getIsApproved() { return $this->is_approved; }
    public function setIsApproved($is_approved) { $this->is_approved = $is_approved; }

    public function getIsRented() { return $this->is_rented; }
    public function setIsRented($is_rented) { $this->is_rented = $is_rented; }

    public function getImage() { return $this->image; }
    public function setImage($image) { $this->image = $image; }
}
?>
