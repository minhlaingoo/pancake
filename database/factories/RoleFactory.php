<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
        ];
    }

    /**
     * Create a role with a specific name.
     *
     * @param string $name
     * @return Role
     */
    public function createRole($name)
    {
        return Role::create(['name' => $name]);
    }

    /**
     * Create a role if it does not already exist.
     *
     * @param string $roleName
     * @return Role
     */
    public function createRoleIfNotExists($roleName)
    {
        $existingRole = Role::where('name', $roleName)->first();

        if (!$existingRole) {
            return Role::create([
                'name' => $roleName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $existingRole;
    }
}
