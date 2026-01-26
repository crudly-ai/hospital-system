import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem, useSidebar } from '@/components/ui/layout/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import {     BookOpen, Settings, LayoutGrid, Shield, FileImage, FormInput, Users, Building2, Briefcase, UserCheck, Code, Sparkles, Anchor, Package, Receipt, FolderOpen, CheckSquare, BarChart3, ShoppingCart, AlertTriangle, Mail, MessageCircle, FileText , Activity , Calendar , Home , DollarSign , User } from 'lucide-react';
import AppLogo from './app-logo';
import { useBrand } from '@/contexts/brand-context';

import { useTranslations } from '@/hooks/use-translations';

export function AppSidebar() {
    const { t } = useTranslations();
    const { state } = useSidebar();
    const { getSidebarStyles, getSidebarClasses, getSidebarVariant, settings } = useBrand();

    // Determine sidebar side based on layout direction
    const sidebarSide = settings.layout_direction === 'rtl' ? 'right' : 'left';

    const mainNavItems: NavItem[] = [
        {
            title: t('Dashboard'),
            url: '/dashboard',
            icon: LayoutGrid,
        },
        {
            title: t('Users'),
            url: '/users',
            icon: BookOpen,
            permissions: ['manage_all_user', 'manage_own_user'],
        },
        {
            title: t('Roles'),
            url: '/roles',
            icon: Shield,
            permissions: ['manage_all_role', 'manage_own_role'],
        },
        {
            title: t('Departments'),
            url: '/departments',
            icon: Briefcase,
            permissions: ['manage_all_department', 'manage_own_department'],
        },
        {
            title: t('Doctors'),
            url: '/doctors',
            icon: User,
            permissions: ['manage_all_doctor', 'manage_own_doctor'],
        },
        {
            title: t('Patients'),
            url: '/patients',
            icon: Users,
            permissions: ['manage_all_patient', 'manage_own_patient'],
        },
        {
            title: t('Treatments'),
            url: '/treatments',
            icon: Activity,
            permissions: ['manage_all_treatment', 'manage_own_treatment'],
        },
        {
            title: t('Appointments'),
            url: '/appointments',
            icon: Calendar,
            permissions: ['manage_all_appointment', 'manage_own_appointment'],
        },
        {
            title: t('Admissions'),
            url: '/admissions',
            icon: Home,
            permissions: ['manage_all_admission', 'manage_own_admission'],
        },
        {
            title: t('Billings'),
            url: '/billings',
            icon: DollarSign,
            permissions: ['manage_all_billing', 'manage_own_billing'],
        },
        {
            title: t('Media'),
            url: '/media-library',
            icon: FileImage,
            permission: 'manage_media',
        },
        {
            title: 'All Projects',
            url: '/projects',
            icon: FolderOpen,
            permissions: ['manage_all_crudly_project', 'manage_own_crudly_project'],
        },
    ];

    const pagesNavItems: NavItem[] = [
        {
            title: t('Projects'),
            url: '/project-management',
            icon: Briefcase,
            permissions: ['manage_all_projects', 'manage_own_projects'],
        },
        {
            title: t('Tasks'),
            url: '/task-management',
            icon: CheckSquare,
            permissions: ['manage_all_tasks', 'manage_own_tasks'],
        },
        {
            title: t('Invoices'),
            url: '/invoices',
            icon: Receipt,
            permissions: ['manage_all_invoice', 'manage_own_invoice'],
        },
        {
            title: t('Orders'),
            url: '/orders',
            icon: ShoppingCart,
            permissions: ['manage_all_orders', 'manage_own_orders'],
        },
        {
            title: t('Charts'),
            url: '/charts',
            icon: BarChart3,
            permission: 'view_charts',
        },
        {
            title: t('Email'),
            url: '/email',
            icon: Mail,
            permissions: ['manage_all_email', 'manage_own_email'],
        },
        {
            title: t('Chat'),
            url: '/chat',
            icon: MessageCircle,
            permissions: ['manage_all_chat', 'manage_own_chat'],
        },
        {
            title: t('Blog'),
            url: '/blog',
            icon: FileText,
            permissions: ['manage_all_blog', 'manage_own_blog'],
        },
        {
            title: 'Error Pages',
            icon: AlertTriangle,
            items: [
                {
                    title: 'Unauthorized',
                    url: '/error-pages/unauthorized',
                },
                {
                    title: 'Forbidden',
                    url: '/error-pages/forbidden',
                },
                {
                    title: 'Not Found',
                    url: '/error-pages/not-found',
                },
                {
                    title: 'Server Error',
                    url: '/error-pages/server-error',
                },
                {
                    title: 'Maintenance',
                    url: '/error-pages/maintenance',
                },
            ],
        },
    ];

    const footerNavItems: NavItem[] = [{
            title: t('Settings'),
            url: '/system-settings',
            icon: Settings,
            permission: 'manage_settings',
        },

    ];

    const sidebarStyles = getSidebarStyles();
    const sidebarClasses = getSidebarClasses();

    return (
        <Sidebar
            collapsible="icon"
            variant={getSidebarVariant()}
            side={sidebarSide}
            style={sidebarStyles}
            className={sidebarClasses}
        >
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/dashboard" prefetch>
                                <AppLogo collapsed={state === 'collapsed'} />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
                <NavMain items={pagesNavItems} groupLabel="Pages" />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
