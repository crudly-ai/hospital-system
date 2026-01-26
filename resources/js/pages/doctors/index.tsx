import { YajraDataTable } from '@/components/datatable';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { createBreadcrumbs } from '@/layouts/app/breadcrumbs';
import { Head, router } from '@inertiajs/react';
import { useFormatters } from '@/utils/formatters';
import { DoctorCreateModal } from './create';
import { DoctorEditModal } from './edit';
import { DoctorViewModal } from './view';
import { DoctorIndexProps } from './types';
import { usePermissions } from '@/hooks/use-permissions';
import { useTranslations } from '@/hooks/use-translations';
import { useToast } from '@/hooks/use-toast';
import { Button } from '@/components/ui/form/button';
import { Toaster } from '@/components/ui/feedback/toaster';
import { Card, CardContent } from '@/components/ui/layout/card';
import { Badge } from '@/components/ui/feedback/badge';
import { Plus, Eye, Edit, Trash2, Calendar, User } from 'lucide-react';
import React, { useState, useRef, useEffect } from 'react';
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';

export default function Index(props: DoctorIndexProps) {
    const { hasPermission } = usePermissions();
    const { t } = useTranslations();
    const { toast } = useToast();
    const { formatDate } = useFormatters();
    
    // Set currency settings globally
    React.useEffect(() => {
        if (props.currencySettings) {
            window.currencySettings = props.currencySettings;
        }
    }, [props.currencySettings]);
    const [createModalOpen, setCreateModalOpen] = useState(false);
    const [editModalOpen, setEditModalOpen] = useState(false);
    const [viewModalOpen, setViewModalOpen] = useState(false);
    const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
    const [selectedDoctor, setSelectedDoctor] = useState<any>(null);
    const tableRef = useRef<any>(null);


        const departmentsOptions = props.departments?.map((department) => ({
            value: department.id.toString(),
            label: department.name || department.title || department.id.toString()
        })) || [];

        const statusOptions = [
            { value: 'active', label: t('Active') },
            { value: 'inactive', label: t('Inactive') },
        ];


    useEffect(() => {
        const handleRefresh = () => {
            if (tableRef.current) {
                tableRef.current.refresh();
            }
        };

        window.addEventListener('refreshDataTable', handleRefresh);
        return () => window.removeEventListener('refreshDataTable', handleRefresh);
    }, []);



    const columns = [
        {
            data: 'id',
            name: 'id',
            title: t('ID'),
            orderable: true,
            searchable: false,
        },
        {
            data: 'name',
            name: 'name',
            title: t('Name'),
            orderable: true,
            searchable: true,
        },
        {
            data: 'specialty',
            name: 'specialty',
            title: t('Specialty'),
            orderable: true,
            searchable: true,
        },
        {
            data: 'department_name',
            name: 'department_name',
            title: t('Department'),
            orderable: true,
            searchable: false,
            render: (data: any) => data || '-',
        },
        {
            data: 'phone',
            name: 'phone',
            title: t('Phone'),
            orderable: true,
            searchable: true,
        },
        {
            data: 'email',
            name: 'email',
            title: t('Email'),
            orderable: true,
            searchable: true,
        },
        {
            data: 'status',
            name: 'status',
            title: t('Status'),
            orderable: true,
            searchable: true,
            render: (data: string) => (
                <Badge variant={data === 'active' ? 'default' : 'destructive'} className="text-xs capitalize">
                    {data}
                </Badge>
            ),
        },
        {
            data: 'created_at_formatted',
            name: 'created_at',
            title: t('Created At'),
            orderable: true,
            searchable: false,
            render: (data: string) => formatDate(data)
        },
    ];

    const filters = [
        {
            key: 'department_id_filter',
            label: t('Department'),
            type: 'select' as const,
            options: departmentsOptions,
            placeholder: t('Filter by department')
        },
        {
            key: 'status_filter',
            label: t('Status'),
            type: 'select' as const,
            options: statusOptions,
            placeholder: t('Filter by status')
        },
        {
            key: 'date_from',
            label: 'Created From',
            type: 'date' as const,
            placeholder: 'Select start date'
        },
        {
            key: 'date_to',
            label: 'Created To',
            type: 'date' as const,
            placeholder: 'Select end date'
        }
    ];

    const customActions = (
        <>
            {hasPermission('create_doctor') && (
                <Button onClick={() => setCreateModalOpen(true)}>
                    <Plus className="h-4 w-4 mr-2" />
                    {t('Create Doctor')}
                </Button>
            )}
        </>
    );

    const customRowActions = (item: any) => (
        <>
            {hasPermission('view_doctor') && (
                <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => {
                        setSelectedDoctor(item);
                        setViewModalOpen(true);
                    }}
                >
                    <Eye className="h-4 w-4" />
                </Button>
            )}
            {hasPermission('edit_doctor') && (
                <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => {
                        setSelectedDoctor(item);
                        setEditModalOpen(true);
                    }}
                >
                    <Edit className="h-4 w-4" />
                </Button>
            )}
            {hasPermission('delete_doctor') && (
                <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => {
                        setSelectedDoctor(item);
                        setDeleteDialogOpen(true);
                    }}
                >
                    <Trash2 className="h-4 w-4" />
                </Button>
            )}
        </>
    );

    const renderGridItem = (doctor: any) => (
        <Card className="bg-white border hover:shadow-md transition-shadow">
            <CardContent className="p-4">
                <div className="space-y-3">
                    <div className="flex items-start gap-3">
                        <div className="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center border-2 border-blue-200 flex-shrink-0">
                            <User className="h-6 w-6 text-blue-600" />
                        </div>
                        <div className="flex-1 min-w-0">
                            <h3 className="font-semibold text-gray-900 truncate text-sm leading-tight">
                                {doctor.name || `Doctor #${doctor.id}`}
                            </h3>
                            <p className="text-xs text-gray-500 truncate mt-0.5">
                                {doctor.email || '-'}
                            </p>
                        </div>
                        <div className="flex items-center gap-1 flex-shrink-0">
                            {customRowActions(doctor)}
                        </div>
                    </div>
                    
                    <div className="space-y-2">
                        <div className="flex flex-wrap gap-1">
                            <Badge variant={doctor.status === 'active' ? 'default' : 'secondary'} className="text-xs px-2 py-0.5 capitalize">
                                {doctor.status}
                            </Badge>
                        </div>
                        <div className="grid grid-cols-2 gap-2 text-xs">
                            <div className="flex justify-between">
                                <span className="text-gray-600 truncate">Specialty:</span>
                                <span className="font-medium text-gray-900 truncate ml-1">
{doctor.specialty || '-'}                                </span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-600 truncate">Department Id:</span>
                                <span className="font-medium text-gray-900 truncate ml-1">
{doctor.department?.name || doctor.department?.title || '-'}                                </span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-600 truncate">Phone:</span>
                                <span className="font-medium text-gray-900 truncate ml-1">
{doctor.phone || '-'}                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div className="flex items-center justify-between text-xs text-gray-500 pt-2 border-t">
                        <div className="flex items-center gap-1">
                            <User className="h-3 w-3" />
                            <span>{t('Doctor')}</span>
                        </div>
                        <div className="flex items-center gap-1">
                            <Calendar className="h-3 w-3" />
                            <span>{formatDate(doctor.created_at_formatted)}</span>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>
    );

    const breadcrumbs = createBreadcrumbs('doctors');

    return (
        <AuthenticatedLayout breadcrumbs={breadcrumbs}>
            <Head title={t('Doctors')} />
            <div className="p-6">
                <YajraDataTable
                    ref={tableRef}
                    url="/doctors"
                    columns={columns}
                    title={t('Doctors')}
                    customActions={customActions}
                    customRowActions={customRowActions}
                    filters={filters}
                    gridViewEnabled={true}
                    renderGridItem={renderGridItem}
                    emptyStateIcon={<User className="h-8 w-8 text-gray-400" />}
                />
            
                {hasPermission('create_doctor') && (
                    <DoctorCreateModal
                        open={createModalOpen}
                        onOpenChange={setCreateModalOpen}
                        {...props}
                    />
                )}
                
                {hasPermission('edit_doctor') && (
                    <DoctorEditModal
                        open={editModalOpen}
                        onOpenChange={setEditModalOpen}
                        doctor={selectedDoctor}
                        {...props}
                    />
                )}
                
                {hasPermission('view_doctor') && (
                    <DoctorViewModal
                        open={viewModalOpen}
                        onOpenChange={setViewModalOpen}
                        doctor={selectedDoctor}
                        onEdit={hasPermission('edit_doctor') ? (doctor) => {
                            setSelectedDoctor(doctor);
                            setViewModalOpen(false);
                            setEditModalOpen(true);
                        } : undefined}
                    />
                )}
                
                <ConfirmationDialog
                    open={deleteDialogOpen}
                    onOpenChange={setDeleteDialogOpen}
                    title={t('Delete Doctor')}
                    description={`Are you sure you want to delete ${selectedDoctor?.name}? This action cannot be undone.`}
                    confirmText={t('Delete')}
                    variant="destructive"
                    onConfirm={() => {
                        router.delete(`/doctors/${selectedDoctor.id}`, {
                            onSuccess: () => {
                                toast.success(t('Doctor deleted successfully'));
                                window.dispatchEvent(new CustomEvent('refreshDataTable'));
                                setDeleteDialogOpen(false);
                            },
                            onError: () => {
                                toast.error(t('Failed to delete doctor'), { description: t('Please try again') });
                            },
                        });
                    }}
                />
                <Toaster />
            </div>
        </AuthenticatedLayout>
    );
}