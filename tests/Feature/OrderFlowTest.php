<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\TicketType;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_order_without_coupon()
    {
        // Crear datos de prueba
        $user = User::factory()->create();
        $event = Event::factory()->create();
        $ticketType = TicketType::factory()->create([
            'event_id' => $event->id,
            'quantity' => 10,
            'price' => 100
        ]);

        $orderData = [
            'event_id' => $event->id,
            'tickets' => [
                [
                    'ticket_type_id' => $ticketType->id,
                    'quantity' => 2
                ]
            ],
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com'
        ];

        $response = $this->postJson('/api/v1/orders', $orderData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'order',
                        'tickets',
                        'total',
                        'discount',
                        'taxes'
                    ]
                ]);

        $this->assertDatabaseHas('orders', [
            'event_id' => $event->id,
            'total' => ($ticketType->price * 2) * 1.16 // Con IVA
        ]);
    }

    public function test_can_create_order_with_coupon()
    {
        // Crear datos de prueba
        $event = Event::factory()->create();
        $ticketType = TicketType::factory()->create([
            'event_id' => $event->id,
            'quantity' => 10,
            'price' => 100
        ]);
        $coupon = Coupon::factory()->create([
            'code' => 'DESCUENTO20',
            'discount_percentage' => 20
        ]);

        $orderData = [
            'event_id' => $event->id,
            'tickets' => [
                [
                    'ticket_type_id' => $ticketType->id,
                    'quantity' => 2
                ]
            ],
            'coupon_code' => 'DESCUENTO20',
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com'
        ];

        $response = $this->postJson('/api/v1/orders', $orderData);

        $response->assertStatus(201);
        
        // Verificar que se aplicó el descuento
        $order = json_decode($response->getContent(), true);
        $this->assertEquals(40, $order['data']['discount']); // 20% de 200
    }

    public function test_cannot_create_order_with_insufficient_tickets()
    {
        $event = Event::factory()->create();
        $ticketType = TicketType::factory()->create([
            'event_id' => $event->id,
            'quantity' => 1
        ]);

        $orderData = [
            'event_id' => $event->id,
            'tickets' => [
                [
                    'ticket_type_id' => $ticketType->id,
                    'quantity' => 5 // Más de lo disponible
                ]
            ],
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com'
        ];

        $response = $this->postJson('/api/v1/orders', $orderData);

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false
                ]);
    }

    public function test_can_get_events()
    {
        Event::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/events');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'date',
                            'location',
                            'ticket_types'
                        ]
                    ]
                ]);
    }

    public function test_can_get_specific_event()
    {
        $event = Event::factory()->create();

        $response = $this->getJson("/api/v1/events/{$event->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'id' => $event->id,
                        'name' => $event->name
                    ]
                ]);
    }

    public function test_can_validate_ticket()
    {
        $event = Event::factory()->create();
        $ticketType = TicketType::factory()->create(['event_id' => $event->id]);
        $order = \App\Models\Order::factory()->create(['event_id' => $event->id]);
        $ticket = \App\Models\Ticket::factory()->create(['order_id' => $order->id]);

        $response = $this->getJson("/api/v1/tickets/{$ticket->id}/validate");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'valid' => true
                    ]
                ]);
    }

    public function test_cannot_validate_used_ticket()
    {
        $event = Event::factory()->create();
        $ticketType = TicketType::factory()->create(['event_id' => $event->id]);
        $order = \App\Models\Order::factory()->create(['event_id' => $event->id]);
        $ticket = \App\Models\Ticket::factory()->create([
            'order_id' => $order->id,
            'used' => true
        ]);
        \App\Models\Checkin::factory()->create(['ticket_id' => $ticket->id]);

        $response = $this->getJson("/api/v1/tickets/{$ticket->id}/validate");

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Ticket already used'
                ]);
    }
}
