<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Operator;
use App\Models\ChatConversation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OperatorBusinessMethodsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations to ensure database is set up
        $this->artisan('migrate');
    }

    /** @test */
    public function canTakeMoreChats_returns_true_when_operator_is_available_and_under_limit()
    {
        $operator = Operator::create([
            'name' => 'Test Operator',
            'email' => 'test@example.com',
            'phone_number' => '+1234567890',
            'role' => 'operador',
            'is_active' => true,
            'max_concurrent_chats' => 5,
            'current_chats_count' => 3,
            'status' => 'available',
        ]);

        $this->assertTrue($operator->canTakeMoreChats());
    }

    /** @test */
    public function canTakeMoreChats_returns_false_when_operator_is_inactive()
    {
        $operator = Operator::create([
            'name' => 'Test Operator',
            'email' => 'test@example.com',
            'phone_number' => '+1234567890',
            'role' => 'operador',
            'is_active' => false,
            'max_concurrent_chats' => 5,
            'current_chats_count' => 3,
            'status' => 'available',
        ]);

        $this->assertFalse($operator->canTakeMoreChats());
    }

    /** @test */
    public function canTakeMoreChats_returns_false_when_operator_status_is_not_available()
    {
        $operator = Operator::create([
            'name' => 'Test Operator',
            'email' => 'test@example.com',
            'phone_number' => '+1234567890',
            'role' => 'operador',
            'is_active' => true,
            'max_concurrent_chats' => 5,
            'current_chats_count' => 3,
            'status' => 'busy',
        ]);

        $this->assertFalse($operator->canTakeMoreChats());
    }

    /** @test */
    public function canTakeMoreChats_returns_false_when_operator_is_at_max_capacity()
    {
        $operator = Operator::create([
            'name' => 'Test Operator',
            'email' => 'test@example.com',
            'phone_number' => '+1234567890',
            'role' => 'operador',
            'is_active' => true,
            'max_concurrent_chats' => 5,
            'current_chats_count' => 5,
            'status' => 'available',
        ]);

        $this->assertFalse($operator->canTakeMoreChats());
    }

    /** @test */
    public function assignConversation_successfully_assigns_when_operator_can_take_more_chats()
    {
        $operator = Operator::create([
            'name' => 'Test Operator',
            'email' => 'test@example.com',
            'phone_number' => '+1234567890',
            'role' => 'operador',
            'is_active' => true,
            'max_concurrent_chats' => 5,
            'current_chats_count' => 2,
            'status' => 'available',
        ]);

        $conversation = ChatConversation::create([
            'phone_number' => '+0987654321',
            'contact_name' => 'Test Customer',
            'status' => 'nuevo',
        ]);

        $result = $operator->assignConversation($conversation);

        $this->assertTrue($result);
        
        // Verify conversation was updated
        $conversation->refresh();
        $this->assertEquals($operator->id, $conversation->assigned_to);
        $this->assertEquals('en_proceso', $conversation->status);
        $this->assertEquals('human', $conversation->mode);
        
        // Verify operator chat count was incremented
        $operator->refresh();
        $this->assertEquals(3, $operator->current_chats_count);
        
        // Verify last_activity_at was updated
        $this->assertNotNull($operator->last_activity_at);
    }

    /** @test */
    public function assignConversation_fails_when_operator_cannot_take_more_chats()
    {
        $operator = Operator::create([
            'name' => 'Test Operator',
            'email' => 'test@example.com',
            'phone_number' => '+1234567890',
            'role' => 'operador',
            'is_active' => true,
            'max_concurrent_chats' => 5,
            'current_chats_count' => 5, // At max capacity
            'status' => 'available',
        ]);

        $conversation = ChatConversation::create([
            'phone_number' => '+0987654321',
            'contact_name' => 'Test Customer',
            'status' => 'nuevo',
        ]);

        $result = $operator->assignConversation($conversation);

        $this->assertFalse($result);
        
        // Verify conversation was not updated
        $conversation->refresh();
        $this->assertNull($conversation->assigned_to);
        $this->assertEquals('nuevo', $conversation->status);
        
        // Verify operator chat count was not incremented
        $operator->refresh();
        $this->assertEquals(5, $operator->current_chats_count);
    }

    /** @test */
    public function releaseConversation_successfully_releases_assigned_conversation()
    {
        $operator = Operator::create([
            'name' => 'Test Operator',
            'email' => 'test@example.com',
            'phone_number' => '+1234567890',
            'role' => 'operador',
            'is_active' => true,
            'max_concurrent_chats' => 5,
            'current_chats_count' => 3,
            'status' => 'available',
        ]);

        $conversation = ChatConversation::create([
            'phone_number' => '+0987654321',
            'contact_name' => 'Test Customer',
            'status' => 'en_proceso',
            'assigned_to' => $operator->id,
            'mode' => 'human',
        ]);

        $operator->releaseConversation($conversation);

        // Verify conversation was updated
        $conversation->refresh();
        $this->assertNull($conversation->assigned_to);
        $this->assertEquals('nuevo', $conversation->status);
        $this->assertEquals('ai', $conversation->mode);
        
        // Verify operator chat count was decremented
        $operator->refresh();
        $this->assertEquals(2, $operator->current_chats_count);
    }

    /** @test */
    public function assignConversation_updates_last_activity_timestamp()
    {
        $operator = Operator::create([
            'name' => 'Test Operator',
            'email' => 'test@example.com',
            'phone_number' => '+1234567890',
            'role' => 'operador',
            'is_active' => true,
            'max_concurrent_chats' => 5,
            'current_chats_count' => 2,
            'status' => 'available',
            'last_activity_at' => null,
        ]);

        $conversation = ChatConversation::create([
            'phone_number' => '+0987654321',
            'contact_name' => 'Test Customer',
            'status' => 'nuevo',
        ]);

        $operator->assignConversation($conversation);

        $operator->refresh();
        $this->assertNotNull($operator->last_activity_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $operator->last_activity_at);
    }

    /** @test */
    public function business_methods_work_together_in_realistic_scenario()
    {
        // Create operator with capacity for 3 chats
        $operator = Operator::create([
            'name' => 'Test Operator',
            'email' => 'test@example.com',
            'phone_number' => '+1234567890',
            'role' => 'operador',
            'is_active' => true,
            'max_concurrent_chats' => 3,
            'current_chats_count' => 0,
            'status' => 'available',
        ]);

        // Create 4 conversations
        $conversations = [];
        for ($i = 1; $i <= 4; $i++) {
            $conversations[] = ChatConversation::create([
                'phone_number' => "+098765432{$i}",
                'contact_name' => "Customer {$i}",
                'status' => 'nuevo',
            ]);
        }

        // Should be able to assign first 3 conversations
        $this->assertTrue($operator->canTakeMoreChats());
        $this->assertTrue($operator->assignConversation($conversations[0]));
        
        $this->assertTrue($operator->canTakeMoreChats());
        $this->assertTrue($operator->assignConversation($conversations[1]));
        
        $this->assertTrue($operator->canTakeMoreChats());
        $this->assertTrue($operator->assignConversation($conversations[2]));
        
        // Should not be able to assign 4th conversation (at capacity)
        $this->assertFalse($operator->canTakeMoreChats());
        $this->assertFalse($operator->assignConversation($conversations[3]));
        
        // Verify operator state
        $operator->refresh();
        $this->assertEquals(3, $operator->current_chats_count);
        
        // Release one conversation
        $operator->releaseConversation($conversations[1]);
        
        // Should now be able to take the 4th conversation
        $this->assertTrue($operator->canTakeMoreChats());
        $this->assertTrue($operator->assignConversation($conversations[3]));
        
        // Final verification
        $operator->refresh();
        $this->assertEquals(3, $operator->current_chats_count);
        
        // Verify which conversations are assigned
        foreach ($conversations as $index => $conversation) {
            $conversation->refresh();
            if ($index === 1) {
                // This one was released
                $this->assertNull($conversation->assigned_to);
                $this->assertEquals('nuevo', $conversation->status);
                $this->assertEquals('ai', $conversation->mode);
            } else {
                // These should be assigned
                $this->assertEquals($operator->id, $conversation->assigned_to);
                $this->assertEquals('en_proceso', $conversation->status);
                $this->assertEquals('human', $conversation->mode);
            }
        }
    }
}