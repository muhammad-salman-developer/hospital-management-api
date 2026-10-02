<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ===== PERMISSIONS =====
        $permissions = [
            // Users / Staff Management
            'user-add', 'user-view', 'user-update', 'user-delete',
            // departments
            'department-add', 'department-view', 'department-update', 'department-delete',
            // Doctors
            'doctor-add', 'doctor-view', 'doctor-update', 'doctor-delete',
            // dr schedule
            'doctor-schedule-view',
            'doctor-schedule-add',
            'doctor-schedule-update',
            'doctor-schedule-delete',
            // Patients
            'patient-add', 'patient-view', 'patient-update', 'patient-delete',
            'payment-add', 'payment-view', 'payment-update', 'payment-delete',

            // Appointments
            'appointment-add', 'appointment-view', 'appointment-update', 'appointment-delete',

            // Lab Tests / Reports
            'lab-test-add', 'lab-test-view', 'lab-test-update', 'lab-test-delete',
            'lab-report-add', 'lab-report-view', 'lab-report-update', 'lab-report-delete',

            // Pharmacy / Medicines
            'medicine-add', 'medicine-view', 'medicine-update', 'medicine-delete',
            'prescription-add', 'prescription-view', 'prescription-update', 'prescription-delete',

            // Billing / Invoices
            'billing-add', 'billing-view', 'billing-update', 'billing-delete',

            // Reports / Dashboard
            'report-view',

            // Settings
            'settings-update','review-delete',
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ===== ROLES =====
        $admin = Role::updateOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $doctor = Role::updateOrCreate(['name' => 'doctor', 'guard_name' => 'web']);
        $receptionist = Role::updateOrCreate(['name' => 'receptionist', 'guard_name' => 'web']);
        $labTech = Role::updateOrCreate(['name' => 'lab_technician', 'guard_name' => 'web']);
        $pharmacist = Role::updateOrCreate(['name' => 'pharmacist', 'guard_name' => 'web']);
        $patient = Role::updateOrCreate(['name' => 'patient', 'guard_name' => 'web']);

        // ===== ASSIGN PERMISSIONS =====

        // Admin -> sab kuch
        $admin->givePermissionTo(Permission::all());

        // Doctor -> patients dekh/update kar sakta, appointments dekh sakta, lab reports dekh sakta, prescription likh sakta
        $doctor->givePermissionTo([
            'patient-view', 'patient-update',
            'appointment-view', 'appointment-update',
            'lab-report-view',
            'prescription-add', 'prescription-view', 'prescription-update',
        ]);

        // Receptionist -> patient register, appointment book/manage, billing
        $receptionist->givePermissionTo([
            'patient-add', 'patient-view', 'patient-update',
            'appointment-add', 'appointment-view', 'appointment-update', 'appointment-delete',
            'billing-add', 'billing-view', 'billing-update',
            'payment-add', 'payment-view', 'payment-update', 'payment-delete',

        ]);

        // Lab Technician -> sirf lab module
        $labTech->givePermissionTo([
            'lab-test-add', 'lab-test-view', 'lab-test-update', 'lab-test-delete',
            'lab-report-add', 'lab-report-view', 'lab-report-update', 'lab-report-delete',
            'patient-view', // sirf dekhne ke liye, edit nahi
        ]);

        // Pharmacist -> medicines aur prescriptions fulfill karna
        $pharmacist->givePermissionTo([
            'medicine-add', 'medicine-view', 'medicine-update', 'medicine-delete',
            'prescription-view', 'prescription-update',
        ]);

        // Patient -> sirf apni cheezein dekhna
        $patient->givePermissionTo([
            'patient-view',
            'patient-update',
            'appointment-add', 'appointment-view',
            'lab-report-view',
            'prescription-view',
            'billing-view',
            'patient-add'
        ]);
    }
}
