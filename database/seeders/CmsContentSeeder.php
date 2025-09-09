<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CmsContent;

class CmsContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CmsContent::updateOrCreate(
            ['slug' => 'about', 'locale' => 'en'],
            [
                'title' => 'About BHMS',
                'content' => '<p>BHMS is a comprehensive Hospital Management SaaS platform designed to streamline healthcare operations. Our solution integrates patient management, appointment scheduling, billing, pharmacy inventory, laboratory reports, and multi-branch support in one secure, user-friendly system.</p>
                            <p>Built with modern technology and adhering to healthcare standards, BHMS helps hospitals of all sizes deliver better patient care while optimizing operational efficiency and ensuring data security.</p>
                            <h3>Our Mission</h3>
                            <p>To provide healthcare institutions with smart, scalable, and secure management solutions that improve patient outcomes and operational workflows.</p>
                            <h3>Key Features</h3>
                            <ul>
                                <li><strong>Patient Management:</strong> Comprehensive patient registration, records, and history tracking</li>
                                <li><strong>Appointment Scheduling:</strong> Intelligent scheduling system with doctor availability and waitlist management</li>
                                <li><strong>Billing & Payments:</strong> Integrated billing system with insurance processing and payment tracking</li>
                                <li><strong>Pharmacy Management:</strong> Inventory control, prescription management, and automated stock alerts</li>
                                <li><strong>Laboratory Integration:</strong> Real-time lab reports, result tracking, and reference ranges</li>
                                <li><strong>Multi-Branch Support:</strong> Centralized management across multiple hospital locations</li>
                            </ul>',
                'type' => 'page',
                'status' => 'published',
            ]
        );

        CmsContent::updateOrCreate(
            ['slug' => 'home', 'locale' => 'en'],
            [
                'title' => 'BHMS - Smart Hospital Management SaaS',
                'content' => '<p>Professional hospital management software solution for modern healthcare facilities.</p>
                              <p>Streamline operations, improve patient care, and grow your business with our comprehensive SaaS platform.</p>',
                'type' => 'page',
                'status' => 'published',
            ]
        );

        CmsContent::updateOrCreate(
            ['slug' => 'hero_title', 'locale' => 'en'],
            [
                'title' => 'Smart, Scalable, Secure Hospital Management SaaS',
                'content' => '',
                'type' => 'block',
                'status' => 'published',
            ]
        );

        CmsContent::updateOrCreate(
            ['slug' => 'hero_subtitle', 'locale' => 'en'],
            [
                'title' => '',
                'content' => 'Transform your healthcare operations with our cloud-based hospital management system. Affordable, secure, and ready to scale with your medical facility.',
                'type' => 'block',
                'status' => 'published',
            ]
        );
    }
}