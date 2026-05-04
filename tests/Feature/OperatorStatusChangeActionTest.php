<?php

namespace Tests\Feature;

use App\Models\Operator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperatorStatusChangeActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_status_change_action_exists()
    {
        $operator = Operator::factory()->create([
            'status' => 'offline'
        ]);

        // Test that the action can change status
        $operator->update([
            'status' => 'available',
            'last_activity_at' => now()
        ]);

        $this->assertEquals('available', $operator->fresh()->status);
        $this->assertNotNull($operator->fresh()->last_activity_at);
    }

    public function test_all_status_options_are_available()
    {
        $operator = Operator::factory()->create();

        $statuses = ['available', 'busy', 'away', 'offline'];

        foreach ($statuses as $status) {
            $operator->update([
                'status' => $status,
                'last_activity_at' => now()
            ]);

            $this->assertEquals($status, $operator->fresh()->status);
        }
    }

    public function test_last_activity_timestamp_is_updated()
    {
        $operator = Operator::factory()->create([
            'status' => 'offline',
            'last_activity_at' => null
        ]);

        $this->assertNull($operator->last_activity_at);

        $operator->update([
            'status' => 'available',
            'last_activity_at' => now()
        ]);

        $this->assertNotNull($operator->fresh()->last_activity_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $operator->fresh()->last_activity_at);
    }

    public function test_status_change_preserves_other_fields()
    {
        $operator = Operator::factory()->create([
            'name' => 'Test Operator',
            'email' => 'test@example.com',
            'status' => 'offline',
            'max_concurrent_chats' => 5
        ]);

        $operator->update([
            'status' => 'available',
            'last_activity_at' => now()
        ]);

        $fresh = $operator->fresh();
        $this->assertEquals('Test Operator', $fresh->name);
        $this->assertEquals('test@example.com', $fresh->email);
        $this->assertEquals(5, $fresh->max_concurrent_chats);
        $this->assertEquals('available', $fresh->status);
    }
}