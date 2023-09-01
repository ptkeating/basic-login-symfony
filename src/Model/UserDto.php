<?php

namespace App\Model;

class UserDto
{


    public function __construct(
        protected ?string $full_name = null,
        protected ?string $email = null,
        protected ?string $password = null,
        protected ?string $house_number = null,
        protected ?string $street_address = null,
        protected ?string $city = null,
        protected ?string $postcode = null,
    ) {
    }

    public function getFullName(): ?string
    {
        return $this->full_name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getHouseNumber(): ?string
    {
        return $this->house_number;
    }

    public function getStreetAddress(): ?string
    {
        return $this->street_address;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }
}
