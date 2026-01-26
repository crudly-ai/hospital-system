import { YajraDataTable } from '@/components/datatable';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { createBreadcrumbs } from '@/layouts/app/breadcrumbs';
import { Head, router } from '@inertiajs/react';
import { useFormatters } from '@/utils/formatters';
import { AppointmentCreateModal } from './create';
import { AppointmentEditModal } from './edit';
import { AppointmentViewModal } from './view';
import { AppointmentIndexProps } from './types';
import { usePermissions } from '@/hooks/use-permissions';
import { useTranslations } from '@/hooks/use-translations';
import { useToast } from '@/hooks/use-toast';
import { Button } from '@/components/ui/form/button';
import { Toaster } from '@/components/ui/feedback/toaster';
import { Card, CardContent } from '@/components/ui/layout/card';
import { Badge } from '@/components/ui/feedback/badge';
import { Plus, Eye, Edit, Trash2, Calendar } from 'lucide-react';
import React, { useState, useRef, useEffect } from 'react';
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';

export default function Index(props: AppointmentIndexProps) {
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
    const [selectedAppointment, setSelectedAppointment] = useState<any>(null);
    const tableRef = useRef<any>(null);


        const doctorsOptions = props.doctors?.map((doctor) => ({
            value: doctor.id.toString(),
            label: doctor.name || doctor.title || doctor.id.toString()
        })) || [];

        const patientsOptions = props.patients?.map((patient) => ({
            value: patient.id.toString(),
            label: patient.name || patient.title || patient.id.toString()
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
            data: 'doctor_name',
            name: 'doctor_name',
            title: t('Doctor'),
            orderable: true,
            searchable: false,
            render: (data: any) => data || '-',
        },
        {
            data: 'patient_name',
            name: 'patient_name',
            title: t('Patient'),
            orderable: true,
            searchable: false,
            render: (data: any) => data || '-',
        },
        {
            data: 'date',
            name: 'date',
            title: t('Date'),
            orderable: true,
            searchable: false,
            render: (data: string) => data ? formatDate(data) : '-',
        },
        {
            data: 'time',
            name: 'time',
            title: t('Time'),
            orderable: true,
            searchable: false,
            render: (data: string) => data ? new Date('1970-01-01T' + data).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }) : '-',
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
            key: 'doctor_id_filter',
            label: t('Doctor'),
            type: 'select' as const,
            options: doctorsOptions,
            placeholder: t('Filter by doctor')
        },
        {
            key: 'patient_id_filter',
            label: t('Patient'),
            type: 'select' as const,
            options: patientsOptions,
            placeholder: t('Filter by patient')
        },
        {
            key: 'date_from',
            label: t('Date From'),
            type: 'date' as const,
            placeholder: t('Select start date')
        },
        {
            key: 'date_to',
            label: t('Date To'),
            type: 'date' as const,
            placeholder: t('Select end date')
        },
        {
            key: 'time_from',
            label: t('Time From'),
            type: 'time' as const,
            placeholder: t('Select start time')
        },
        {
            key: 'time_to',
            label: t('Time To'),
            type: 'time' as const,
            placeholder: t('Select end time')
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
            {hasPermission('create_appointment') && (
                <Button onClick={() => setCreateModalOpen(true)}>
                    <Plus className="h-4 w-4 mr-2" />
                    {t('Create Appointment')}
                </Button>
            )}
        </>
    );

    const customRowActions = (item: any) => (
        <>
            {hasPermission('view_appointment') && (
                <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => {
                        setSelectedAppointment(item);
                        setViewModalOpen(true);
                    }}
                >
                    <Eye className="h-4 w-4" />
                </Button>
            )}
            {hasPermission('edit_appointment') && (
                <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => {
                        setSelectedAppointment(item);
                        setEditModalOpen(true);
                    }}
                >
                    <Edit className="h-4 w-4" />
                </Button>
            )}
            {hasPermission('delete_appointment') && (
                <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => {
                        setSelectedAppointment(item);
                        setDeleteDialogOpen(true);
                    }}
                >
                    <Trash2 className="h-4 w-4" />
                </Button>
            )}
        </>
    );

    const renderGridItem = (appointment: any) => (
        <Card className="bg-white border hover:shadow-md transition-shadow">
            <CardContent className="p-4">
                <div className="space-y-3">
                    <div className="flex items-start gap-3">
                        <div className="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center border-2 border-blue-200 flex-shrink-0">
                            <Calendar className="h-6 w-6 text-blue-600" />
                        </div>
                        <div className="flex-1 min-w-0">
                            <h3 className="font-semibold text-gray-900 truncate text-sm leading-tight">
                                {appointment.doctor_id || `Appointment #${appointment.id}`}
                            </h3>
                            <p className="text-xs text-gray-500 truncate mt-0.5">
                                {appointment.doctor?.name || appointment.doctor?.title || '-'}
                            </p>
                        </div>
                        <div className="flex items-center gap-1 flex-shrink-0">
                            {customRowActions(appointment)}
                        </div>
                    </div>
                    
                    <div className="space-y-2">
                        <div className="flex flex-wrap gap-1">
                            <Badge variant={appointment.status === 'active' ? 'default' : 'secondary'} className="text-xs px-2 py-0.5 capitalize">
                                {appointment.status}
                            </Badge>
                        </div>
                        <div className="grid grid-cols-2 gap-2 text-xs">
                            <div className="flex justify-between">
                                <span className="text-gray-600 truncate">Patient Id:</span>
                                <span className="font-medium text-gray-900 truncate ml-1">
{appointment.patient?.name || appointment.patient?.title || '-'}                                </span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-600 truncate">Date:</span>
                                <span className="font-medium text-gray-900 truncate ml-1">
{appointment.date ? formatDate(appointment.date) : '-'}                                </span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-600 truncate">Time:</span>
                                <span className="font-medium text-gray-900 truncate ml-1">
{appointment.time || '-'}                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div className="flex items-center justify-between text-xs text-gray-500 pt-2 border-t">
                        <div className="flex items-center gap-1">
                            <Calendar className="h-3 w-3" />
                            <span>{t('Appointment')}</span>
                        </div>
                        <div className="flex items-center gap-1">
                            <Calendar className="h-3 w-3" />
                            <span>{formatDate(appointment.created_at_formatted)}</span>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>
    );

    const breadcrumbs = createBreadcrumbs('appointments');

    return (
        <AuthenticatedLayout breadcrumbs={breadcrumbs}>
            <Head title={t('Appointments')} />
            <div className="p-6">
                <YajraDataTable
                    ref={tableRef}
                    url="/appointments"
                    columns={columns}
                    title={t('Appointments')}
                    customActions={customActions}
                    customRowActions={customRowActions}
                    filters={filters}
                    gridViewEnabled={true}
                    renderGridItem={renderGridItem}
                    emptyStateIcon={<Calendar className="h-8 w-8 text-gray-400" />}
                />
            
                {hasPermission('create_appointment') && (
                    <AppointmentCreateModal
                        open={createModalOpen}
                        onOpenChange={setCreateModalOpen}
                        {...props}
                    />
                )}
                
                {hasPermission('edit_appointment') && (
                    <AppointmentEditModal
                        open={editModalOpen}
                        onOpenChange={setEditModalOpen}
                        appointment={selectedAppointment}
                        {...props}
                    />
                )}
                
                {hasPermission('view_appointment') && (
                    <AppointmentViewModal
                        open={viewModalOpen}
                        onOpenChange={setViewModalOpen}
                        appointment={selectedAppointment}
                        onEdit={hasPermission('edit_appointment') ? (appointment) => {
                            setSelectedAppointment(appointment);
                            setViewModalOpen(false);
                            setEditModalOpen(true);
                        } : undefined}
                    />
                )}
                
                <ConfirmationDialog
                    open={deleteDialogOpen}
                    onOpenChange={setDeleteDialogOpen}
                    title={t('Delete Appointment')}
                    description={`Are you sure you want to delete ${selectedAppointment?.name}? This action cannot be undone.`}
                    confirmText={t('Delete')}
                    variant="destructive"
                    onConfirm={() => {
                        router.delete(`/appointments/${selectedAppointment.id}`, {
                            onSuccess: () => {
                                toast.success(t('Appointment deleted successfully'));
                                window.dispatchEvent(new CustomEvent('refreshDataTable'));
                                setDeleteDialogOpen(false);
                            },
                            onError: () => {
                                toast.error(t('Failed to delete appointment'), { description: t('Please try again') });
                            },
                        });
                    }}
                />
                <Toaster />
            </div>
        </AuthenticatedLayout>
    );
}