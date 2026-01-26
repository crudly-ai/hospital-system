<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Settings permissions
            'manage_settings',
            'edit_settings',

             // Media permissions
            'manage_media',
            'upload_media',
            'delete_media',
            'download_media',
            'manage_folders',

             // Role permissions
            'manage_all_role',
            'manage_own_role',
            'create_role',
            'view_role',
            'edit_role',
            'delete_role',

             // User permissions
            'manage_all_user',
            'manage_own_user',
            'create_user',
            'view_user',
            'edit_user',
            'delete_user',// Invoice permissions
            'manage_all_invoice',
            'manage_own_invoice',
            'view_invoice',
            'create_invoice',
            'edit_invoice',
            'delete_invoice',
            'export_invoice',

            // Project permissions
            'manage_all_projects',
            'manage_own_projects',
            'view_projects',
            'create_projects',
            'edit_projects',
            'delete_projects',
            'export_projects',

            // Task permissions
            'manage_all_tasks',
            'manage_own_tasks',
            'view_tasks',
            'create_tasks',
            'edit_tasks',
            'delete_tasks',
            'assign_tasks',

            // Charts permissions
            'view_charts',

            // Orders permissions
            'manage_all_orders',
            'manage_own_orders',
            'view_orders',
            'create_orders',
            'edit_orders',
            'delete_orders',
            'export_orders',

            // Email permissions
            'manage_all_email',
            'manage_own_email',
            'view_email',
            'create_email',
            'send_email',
            'delete_email',

            // Chat permissions
            'manage_all_chat',
            'manage_own_chat',
            'view_chat',
            'send_message',
            'delete_message',
            'make_call',

            // Blog permissions
            'manage_all_blog',
            'manage_own_blog',
            'view_blog',
            'create_blog',
            'edit_blog',
            'delete_blog',
            'publish_blog',

                    
            // Department permissions
            'manage_all_department',
            'manage_own_department',
            'view_department',
            'create_department', 
            'edit_department',
            'delete_department',
            
                    
            // Doctor permissions
            'manage_all_doctor',
            'manage_own_doctor',
            'view_doctor',
            'create_doctor', 
            'edit_doctor',
            'delete_doctor',
            
                    
            // Patient permissions
            'manage_all_patient',
            'manage_own_patient',
            'view_patient',
            'create_patient', 
            'edit_patient',
            'delete_patient',
            
                    
            // Treatment permissions
            'manage_all_treatment',
            'manage_own_treatment',
            'view_treatment',
            'create_treatment', 
            'edit_treatment',
            'delete_treatment',
            
                    
            // Appointment permissions
            'manage_all_appointment',
            'manage_own_appointment',
            'view_appointment',
            'create_appointment', 
            'edit_appointment',
            'delete_appointment',
            
                    
            // Admission permissions
            'manage_all_admission',
            'manage_own_admission',
            'view_admission',
            'create_admission', 
            'edit_admission',
            'delete_admission',
            
                    
            // Billing permissions
            'manage_all_billing',
            'manage_own_billing',
            'view_billing',
            'create_billing', 
            'edit_billing',
            'delete_billing',
            
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
