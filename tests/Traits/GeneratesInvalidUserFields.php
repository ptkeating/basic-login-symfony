<?php

namespace App\Tests\Traits;

trait GeneratesInvalidUserFields
{

    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * Generates an array of invalid user fields
     * @param array $fields - an array of strings representing the fields to be generated as invalid, defaults to all fields
     * 
     * @return array
     */
    protected function generateInvalidUserFields(?array $fields = []): array
    {
        if (empty($fields)) {
            $fields = ['full_name', 'email', 'password'];
        }
        $userFields = [];
        foreach ($fields as $fieldName) {
            $userFields[$fieldName] = $this->{'generateInvalid' . snake_to_studly_case($fieldName) . 'Field'}();
        }
        return $userFields;
    }

    /**
     * Generate a full name field that is invalid in some specific way
     * @param string $typeOfInvalidity - the rule the invalid text should break
     * 
     * @return string|null
     */
    protected function generateInvalidFullNameField(?string $typeOfInvalidity = null): string|null
    {
        return match ($typeOfInvalidity) {
            'empty' => '',
            'null' => null,
            'max_length' => str_pad($this->faker->text(128), 128, '0'),
            'min_length' => $this->faker->randomLetter(),
            default => null
        };
    }

    /**
     * Generate an email field that is invalid in some specific way
     * @param string $typeOfInvalidity - the rule the invalid text should break
     * 
     * @return string|null
     */
    protected function generateInvalidEmailField(?string $typeOfInvalidity = null): string|null
    {
        return match ($typeOfInvalidity) {
            'max_length' => str_pad($this->faker->text(128), 128, '0'),
            'min_length' => $this->faker->randomLetter() . '@' . $this->faker->randomLetter(),
            'special_chars' => $this->faker->email() . ['!', '#', '$', '%', '&', '~'][mt_rand(0, 5)],
            'format' => str_replace('@', '.', $this->faker->email()),
            'empty' => '',
            'null' => null,
            default => null
        };
    }

    /**
     * Generate an invalid password field that is invalid in some specific way
     * @param string $typeOfInvalidity - the rule the invalid text should break
     * 
     * @return string|null
     */
    protected function generateInvalidPasswordField(?string $typeOfInvalidity = null): string|null
    {
        return match ($typeOfInvalidity) {
            'max_length' => str_pad($this->faker->text(80), 65, '0'), // max of 64 chars recommended for bcrypt
            'min_length' => $this->faker->text(5),
            'entropy' => $this->faker->words(1), // susceptible to dictionary attack
            'empty' => '',
            'null' => null,
            default => null
        };
    }

    /**
     * Generate an invalid house number field that is invalid in some specific way
     * @param string $typeOfInvalidity - the rule the invalid text should break
     * 
     * @return string|null
     */
    protected function generateInvalidHouseNumberField(?string $typeOfInvalidity = null): string|null
    {
        $maxLength = 16;
        return match ($typeOfInvalidity) {
            'max_length' => str_pad($this->faker->text($maxLength), $maxLength, '0'),
            default => str_pad($this->faker->text($maxLength), $maxLength, '0'),
        };
    }

    /**
     * Generate an invalid street address field that is invalid in some specific way
     * @param string $typeOfInvalidity - the rule the invalid text should break
     * 
     * @return string|null
     */
    protected function generateInvalidStreetAddressField(?string $typeOfInvalidity = null): string|null
    {
        $maxLength = 256;
        return match ($typeOfInvalidity) {
            'max_length' => str_pad($this->faker->text($maxLength), $maxLength, '0'),
            default => str_pad($this->faker->text($maxLength), $maxLength, '0'),
        };
    }

    /**
     * Generate an invalid city field that is invalid in some specific way
     * @param string $typeOfInvalidity - the rule the invalid text should break
     * 
     * @return string|null
     */
    protected function generateInvalidCityField(?string $typeOfInvalidity = null): string|null
    {
        $maxLength = 128;
        return match ($typeOfInvalidity) {
            'max_length' => str_pad($this->faker->text($maxLength), $maxLength, '0'),
            default => str_pad($this->faker->text($maxLength), $maxLength, '0'),
        };
    }

    /**
     * Generate an invalid postcode field that is invalid in some specific way
     * @param string $typeOfInvalidity - the rule the invalid text should break
     * 
     * @return string|null
     */
    protected function generateInvalidPostcodeField(?string $typeOfInvalidity = null): string|null
    {
        $maxLength = 16;
        return match ($typeOfInvalidity) {
            'max_length' => str_pad($this->faker->text($maxLength), $maxLength, '0'),
            default => str_pad($this->faker->text($maxLength), $maxLength, '0'),
        };
    }
}
