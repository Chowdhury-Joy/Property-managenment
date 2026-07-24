<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_home_page_renders(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_about_page_renders(): void
    {
        $response = $this->get('/about');
        $response->assertStatus(200);
    }

    public function test_services_page_renders(): void
    {
        $response = $this->get('/services');
        $response->assertStatus(200);
    }

    public function test_contact_page_renders(): void
    {
        $response = $this->get('/contact');
        $response->assertStatus(200);
    }

    public function test_properties_page_renders(): void
    {
        $response = $this->get('/properties');
        $response->assertStatus(200);
    }

    public function test_blog_index_page_renders(): void
    {
        $response = $this->get('/blog');
        $response->assertStatus(200);
    }

    public function test_faq_page_renders(): void
    {
        $response = $this->get('/faq');
        $response->assertStatus(200);
    }
}
