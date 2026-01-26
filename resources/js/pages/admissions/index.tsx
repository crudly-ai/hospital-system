import { YajraDataTable } from '@/components/datatable';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { createBreadcrumbs } from '@/layouts/app/breadcrumbs';
import { Head, router } from '@inertiajs/react';
import { useFormatters } from '@/utils/formatters';
import { AdmissionCreateModal } from './create';
import { AdmissionEditModal } from './edit';
import { AdmissionViewModal } from './view';
import { AdmissionIndexProps } from './types';
import { usePermissions } from '@/hooks/use-permissions';
import { useTranslations } from '@/hooks/use-translations';
import { useToast } from '@/hooks/use-toast';
import { Button } from '@/components/ui/form/button';
import { Toaster } from '@/components/ui/feedback/toaster';
import { Card, CardContent } from '@/components/ui/layout/card';
import { Badge } from '@/components/ui/feedback/badge';
import { Plus, Eye, Edit, Trash2, Calendar, Home } from 'lucide-react';
import React, { useState, useRef, useEffect } from 'react';
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';

export default function Index(props: AdmissionIndexProps) {
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
    const [selectedAdmission, setSelectedAdmission] = useState<any>(null);
    const tableRef = useRef<any>(null);


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
            data: 'patient_name',
            name: 'patient_name',
            title: t('Patient'),
            orderable: true,
            searchable: false,
            render: (data: any) => data || '-',
        },
        {
            data: 'admitted_on',
            name: 'admitted_on',
            title: t('Admitted On'),
            orderable: true,
            searchable: false,
            render: (data: string) => data ? formatDate(data) : '-',
        },
        {
            data: 'discharged_on',
            name: 'discharged_on',
            title: t('Discharged On'),
            orderable: true,
            searchable: false,
            render: (data: string) => data ? formatDate(data) : '-',
        },
        {
            data: 'room_number',
            name: 'room_number',
            title: t('Room Number'),
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
            key: 'patient_id_filter',
            label: t('Patient'),
            type: 'select' as const,
            options: patientsOptions,
            placeholder: t('Filter by patient')
        },
        {
            key: 'admitted_on_from',
            label: t('Admitted On From'),
            type: 'date' as const,
            placeholder: t('Select start date')
        },
        {
            key: 'admitted_on_to',
            label: t('Admitted On To'),
            type: 'date' as const,
            placeholder: t('Select end date')
        },
        {
            key: 'discharged_on_from',
            label: t('Discharged On From'),
            type: 'date' as const,
            placeholder: t('Select start date')
        },
        {
            key: 'discharged_on_to',
            label: t('Discharged On To'),
            type: 'date' as const,
            placeholder: t('Select end date')
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
            {hasPermission('create_admission') && (
                <Button onClick={() => setCreateModalOpen(true)}>
                    <Plus className="h-4 w-4 mr-2" />
                    {t('Create Admission')}
                </Button>
            )}
        </>
    );

    const customRowActions = (item: any) => (
        <>
            {hasPermission('view_admission') && (
                <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => {
                        setSelectedAdmission(item);
                        setViewModalOpen(true);
                    }}
                >
                    <Eye className="h-4 w-4" />
                </Button>
            )}
            {hasPermission('edit_admission') && (
                <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => {
                        setSelectedAdmission(item);
                        setEditModalOpen(true);
                    }}
                >
                    <Edit className="h-4 w-4" />
                </Button>
            )}
            {hasPermission('delete_admission') && (
                <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => {
                        setSelectedAdmission(item);
                        setDeleteDialogOpen(true);
                    }}
                >
                    <Trash2 className="h-4 w-4" />
                </Button>
            )}
        </>
    );

    const renderGridItem = (admission: any) => (
        <Card className="bg-white border hover:shadow-md transition-shadow">
            <CardContent className="p-4">
                <div className="space-y-3">
                    <div className="flex items-start gap-3">
                        <div className="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center border-2 border-blue-200 flex-shrink-0">
                            <Home className="h-6 w-6 text-blue-600" />
                        </div>
                        <div className="flex-1 min-w-0">
                            <h3 className="font-semibold text-gray-900 truncate text-sm leading-tight">
                                {admission.room_number || `Admission #${admission.id}`}
                            </h3>
                            <p className="text-xs text-gray-500 truncate mt-0.5">
                                {admission.patient?.name || admission.patient?.title || '-'}
                            </p>
                        </div>
                        <div className="flex items-center gap-1 flex-shrink-0">
                            {customRowActions(admission)}
                        </div>
                    </div>
                    
                    <div className="space-y-2">
                        <div className="flex flex-wrap gap-1">
                            <Badge variant={admission.status === 'active' ? 'default' : 'secondary'} className="text-xs px-2 py-0.5 capitalize">
                                {admission.status}
                            </Badge>
                        </div>
                        <div className="grid grid-cols-2 gap-2 text-xs">
                            <div className="flex justify-between">
                                <span className="text-gray-600 truncate">Admitted On:</span>
                                <span className="font-medium text-gray-900 truncate ml-1">
{admission.admitted_on ? formatDate(admission.admitted_on) : '-'}                                </span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-600 truncate">Discharged On:</span>
                                <span className="font-medium text-gray-900 truncate ml-1">
{admission.discharged_on ? formatDate(admission.discharged_on) : '-'}                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div className="flex items-center justify-between text-xs text-gray-500 pt-2 border-t">
                        <div className="flex items-center gap-1">
                            <Home className="h-3 w-3" />
                            <span>{t('Admission')}</span>
                        </div>
                        <div className="flex items-center gap-1">
                            <Calendar className="h-3 w-3" />
                            <span>{formatDate(admission.created_at_formatted)}</span>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>
    );

    const breadcrumbs = createBreadcrumbs('admissions');

    return (
        <AuthenticatedLayout breadcrumbs={breadcrumbs}>
            <Head title={t('Admissions')} />
            <div className="p-6">
                <YajraDataTable
                    ref={tableRef}
                    url="/admissions"
                    columns={columns}
                    title={t('Admissions')}
                    customActions={customActions}
                    customRowActions={customRowActions}
                    filters={filters}
                    gridViewEnabled={true}
                    renderGridItem={renderGridItem}
                    emptyStateIcon={<Home className="h-8 w-8 text-gray-400" />}
                />
            
                {hasPermission('create_admission') && (
                    <AdmissionCreateModal
                        open={createModalOpen}
                        onOpenChange={setCreateModalOpen}
                        {...props}
                    />
                )}
                
                {hasPermission('edit_admission') && (
                    <AdmissionEditModal
                        open={editModalOpen}
                        onOpenChange={setEditModalOpen}
                        admission={selectedAdmission}
                        {...props}
                    />
                )}
                
                {hasPermission('view_admission') && (
                    <AdmissionViewModal
                        open={viewModalOpen}
                        onOpenChange={setViewModalOpen}
                        admission={selectedAdmission}
                        onEdit={hasPermission('edit_admission') ? (admission) => {
                            setSelectedAdmission(admission);
                            setViewModalOpen(false);
                            setEditModalOpen(true);
                        } : undefined}
                    />
                )}
                
                <ConfirmationDialog
                    open={deleteDialogOpen}
                    onOpenChange={setDeleteDialogOpen}
                    title={t('Delete Admission')}
                    description={`Are you sure you want to delete ${selectedAdmission?.name}? This action cannot be undone.`}
                    confirmText={t('Delete')}
                    variant="destructive"
                    onConfirm={() => {
                        router.delete(`/admissions/${selectedAdmission.id}`, {
                            onSuccess: () => {
                                toast.success(t('Admission deleted successfully'));
                                window.dispatchEvent(new CustomEvent('refreshDataTable'));
                                setDeleteDialogOpen(false);
                            },
                            onError: () => {
                                toast.error(t('Failed to delete admission'), { description: t('Please try again') });
                            },
                        });
                    }}
                />
                <Toaster />
            </div>
        </AuthenticatedLayout>
    );
}