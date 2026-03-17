<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Operator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OperatorResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations to ensure database is set up
        $this->artisan('migrate');
    }

    /** @test */
    public function operator_can_be_created_with_all_required_fields()
    {
        $operatorData = [
            'name' => 'Juan Pérez',
            'email' => 'juan.perez@example.com',
            'phone_number' => '5217531672288',
            'role' => 'operador',
            'is_active' => true,
            'max_concurrent_chats' => 5,
            'status' => 'available',
        ];

        $operator = Operator::create($operatorData);

        $this->assertDatabaseHas('operators', [
            'name' => 'Juan Pérez',
            'email' => 'juan.perez@example.com',
            'phone_number' => '5217531672288',
            'role' => 'operador',
            'is_active' => true,
            'max_concurrent_chats' => 5,
            'status' => 'available',
            'current_chats_count' => 0, // Default value
        ]);

        $this->assertEquals('Juan Pérez', $operator->name);
        $this->assertEquals('operador', $operator->role);
        $this->assertTrue($operator->is_active);
        $this->assertEquals(5, $operator->max_concurrent_chats);
        $this->assertEquals('available', $operator->status);
        $this->assertEquals(0, $operator->current_chats_count);
    }

    /** @test */
    public function operator_can_be_created_with_different_roles()
    {
        $roles = ['admin', 'supervisor', 'operador'];

        foreach ($roles as $role) {
            $operator = Operator::create([
                'name' => "Test {$role}",
                'email' => "test.{$role}@example.com",
                'phone_number' => '521753167' . rand(1000, 9999),
                'role' => $role,
                'is_active' => true,
                'max_concurrent_chats' => 5,
                'status' => 'available',
            ]);

            $this->assertEquals($role, $operator->role);
            $this->assertDatabaseHas('operators', [
                'role' => $role,
                'email' => "test.{$role}@example.com",
            ]);
        }
    }

    /** @test */
    public function operator_can_be_created_with_different_statuses()
    {
        $statuses = ['available', 'busy', 'away', 'offline'];

        foreach ($statuses as $status) {
            $operator = Operator::create([
                'name' => "Test {$status}",
                'email' => "test.{$status}@example.com",
                'phone_number' => '521753167' . rand(1000, 9999),
                'role' => 'operador',
                'is_active' => true,
                'max_concurrent_chats' => 5,
                'status' => $status,
            ]);

            $this->assertEquals($status, $operator->status);
            $this->assertDatabaseHas('operators', [
                'status' => $status,
                'email' => "test.{$status}@example.com",
            ]);
        }
    }

    /** @test */
    public function operator_can_be_updated_with_new_field_values()
    {
        $operator = Operator::create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'phone_number' => '5217531672288',
            'role' => 'operador',
            'is_active' => true,
            'max_concurrent_chats' => 5,
            'status' => 'offline',
        ]);

        // Update the operator
        $operator->update([
            'name' => 'Updated Name',
            'role' => 'supervisor',
            'max_concurrent_chats' => 10,
            'status' => 'available',
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('operators', [
            'id' => $operator->id,
            'name' => 'Updated Name',
            'role' => 'supervisor',
            'max_concurrent_chats' => 10,
            'status' => 'available',
            'is_active' => false,
        ]);

        $operator->refresh();
        $this->assertEquals('Updated Name', $operator->name);
        $this->assertEquals('supervisor', $operator->role);
        $this->assertEquals(10, $operator->max_concurrent_chats);
        $this->assertEquals('available', $operator->status);
        $this->assertFalse($operator->is_active);
    }

    /** @test */
    public function operator_email_must_be_unique()
    {
        // Create first operator
        Operator::create([
            'name' => 'First Operator',
            'email' => 'unique@example.com',
            'phone_number' => '5217531672288',
            'role' => 'operador',
            'is_active' => true,
            'max_concurrent_chats' => 5,
            'status' => 'available',
        ]);

        // Try to create second operator with same email
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Operator::create([
            'name' => 'Second Operator',
            'email' => 'unique@example.com', // Same email
            'phone_number' => '5217531672289',
            'role' => 'operador',
            'is_active' => true,
            'max_concurrent_chats' => 5,
            'status' => 'available',
        ]);
    }

    /** @test */
    public function operator_phone_number_must_be_unique()
    {
        // Create first operator
        Operator::create([
            'name' => 'First Operator',
            'email' => 'first@example.com',
            'phone_number' => '5217531672288',
            'role' => 'operador',
            'is_active' => true,
            'max_concurrent_chats' => 5,
            'status' => 'available',
        ]);

        // Try to create second operator with same phone number
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Operator::create([
            'name' => 'Second Operator',
            'email' => 'second@example.com',
            'phone_number' => '5217531672288', // Same phone number
            'role' => 'operador',
            'is_active' => true,
            'max_concurrent_chats' => 5,
            'status' => 'available',
        ]);
    }

    /** @test */
    public function operator_defaults_are_applied_correctly()
    {
        $operator = Operator::create([
            'name' => 'Test Operator',
            'email' => 'test@example.com',
            'phone_number' => '5217531672288',
            // Not specifying role, is_active, max_concurrent_chats, status, current_chats_count
        ]);

        // Check that defaults are applied (these should match the migration defaults)
        $this->assertEquals(0, $operator->current_chats_count);
        // Note: role, is_active, max_concurrent_chats, status defaults are handled by the database migration
        // The exact defaults depend on the migration file
    }

    /** @test */
    public function operator_last_activity_at_can_be_updated()
    {
        $operator = Operator::create([
            'name' => 'Test Operator',
            'email' => 'test@example.com',
            'phone_number' => '5217531672288',
            'role' => 'operador',
            'is_active' => true,
            'max_concurrent_chats' => 5,
            'status' => 'available',
        ]);

        $this->assertNull($operator->last_activity_at);

        // Update last activity
        $now = now();
        $operator->update(['last_activity_at' => $now]);

        $operator->refresh();
        $this->assertNotNull($operator->last_activity_at);
        $this->assertEquals($now->format('Y-m-d H:i:s'), $operator->last_activity_at->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function operator_current_chats_count_can_be_incremented_and_decremented()
    {
        $operator = Operator::create([
            'name' => 'Test Operator',
            'email' => 'test@example.com',
            'phone_number' => '5217531672288',
            'role' => 'operador',
            'is_active' => true,
            'max_concurrent_chats' => 5,
            'status' => 'available',
            'current_chats_count' => 2,
        ]);

        $this->assertEquals(2, $operator->current_chats_count);

        // Increment
        $operator->increment('current_chats_count');
        $operator->refresh();
        $this->assertEquals(3, $operator->current_chats_count);

        // Decrement
        $operator->decrement('current_chats_count');
        $operator->refresh();
        $this->assertEquals(2, $operator->current_chats_count);
    }
}