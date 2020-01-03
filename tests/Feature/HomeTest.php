<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function testHomeViewWithoutRollUpPlugin(): void
    {
        Config::set('analytics-service.public_dashboard');

        $this->get(route('home'))
            ->assertViewIs('pages.home')
            ->assertViewHasAll([
                'publicAdministrationsCount' => 0,
                'websitesCount' => 0,
                'widgets' => [],
            ]);
    }

    public function testHomeViewWithRollUpPlugin(): void
    {
        Config::set('analytics-service.public_dashboard', 2);

        $widgets = Yaml::parseFile(resource_path('data/widgets.yml'))['public'];

        $this->get(route('home'))
            ->assertViewIs('pages.home')
            ->assertViewHasAll([
                'publicAdministrationsCount' => 0,
                'websitesCount' => 0,
                'widgets' => $widgets,
            ]);
    }

    public function testFaqView(): void
    {
        $this->get(route('faq'))
            ->assertViewIs('pages.faq')
            ->assertViewHas([
                'faqs',
                'themes',
            ]);
    }

    public function testContactView(): void
    {
        $this->get(route('contacts'))
            ->assertViewIs('pages.contacts');
    }

    public function testOpenDataView(): void
    {
        $this->get(route('open-data'))
            ->assertViewIs('pages.open-data');
    }

    public function testPrivacyView(): void
    {
        $this->get(route('privacy'))
            ->assertViewIs('pages.privacy');
    }

    public function testLegalNotesView(): void
    {
        $this->get(route('legal-notes'))
            ->assertViewIs('pages.legal_notes');
    }
}
