import { YajraDataTable } from '@/components/datatable';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { createBreadcrumbs } from '@/layouts/app/breadcrumbs';
import { Head, router } from '@inertiajs/react';
import { useFormatters } from '@/utils/formatters';
import { BillingCreateModal } from './create';
import { BillingEditModal } from './edit';
import { BillingViewModal } from './view';
import { BillingIndexProps } from './types';
import { usePermissions } from '@/hooks/use-permissions';
import { useTranslations } from '@/hooks/use-translations';
import { useToast } from '@/hooks/use-toast';
import { Button } from '@/components/ui/form/button';
import { Toaster } from '@/components/ui/feedback/toaster';
import { Card, CardContent } from '@/components/ui/layout/card';
import { Badge } from '@/components/ui/feedback/badge';
import { Plus, Eye, Edit, Trash2, Calendar, DollarSign } from 'lucide-react';
import React, { useState, useRef, useEffect } from 'react';
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';

export default function Index(props: BillingIndexProps) {
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
    const [selectedBilling, setSelectedBilling] = useState<any>(null);
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
            data: 'amount',
            name: 'amount',
            title: t('Amount'),
            orderable: true,
            searchable: false,
            render: (data: any) => {
                const formatCurrency = (amount: any) => {
                    const num = parseFloat(amount) || 0;
                    const settings = window.currencySettings || {};
                    const symbol = settings.currency_symbol || '$';
                    const position = settings.currency_position || 'before';
                    const thousandSep = settings.thousand_separator || ',';
                    const decimalSep = settings.decimal_separator || '.';
                    const parts = num.toFixed(2).split('.');
                    parts[0] = parts[0].replace(/\\B(?=(\\d{3})+(?!\\d))/g, thousandSep);
                    const formatted = parts.join(decimalSep);
                    return position === 'before' ? `${symbol}${formatted}` : `${formatted}${symbol}`;
                };
                return <span className="font-mono">{formatCurrency(data)}</span>;
            },
        },
        {
            data: 'payment_date',
            name: 'payment_date',
            title: t('Payment Date'),
            orderable: true,
            searchable: false,
            render: (data: string) => data ? formatDate(data) : '-',
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
            key: 'amount_min',
            label: t('Amount Min'),
            type: 'number' as const,
            placeholder: t('Min amount')
        },
        {
            key: 'amount_max',
            label: t('Amount Max'),
            type: 'number' as const,
            placeholder: t('Max amount')
        },
        {
            key: 'payment_date_from',
            label: t('Payment Date From'),
            type: 'date' as const,
            placeholder: t('Select start date')
        },
        {
            key: 'payment_date_to',
            label: t('Payment Date To'),
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
            {hasPermission('create_billing') && (
                <Button onClick={() => setCreateModalOpen(true)}>
                    <Plus className="h-4 w-4 mr-2" />
                    {t('Create Billing')}
                </Button>
            )}
        </>
    );

    const customRowActions = (item: any) => (
        <>
            {hasPermission('view_billing') && (
                <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => {
                        setSelectedBilling(item);
                        setViewModalOpen(true);
                    }}
                >
                    <Eye className="h-4 w-4" />
                </Button>
            )}
            {hasPermission('edit_billing') && (
                <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => {
                        setSelectedBilling(item);
                        setEditModalOpen(true);
                    }}
                >
                    <Edit className="h-4 w-4" />
                </Button>
            )}
            {hasPermission('delete_billing') && (
                <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => {
                        setSelectedBilling(item);
                        setDeleteDialogOpen(true);
                    }}
                >
                    <Trash2 className="h-4 w-4" />
                </Button>
            )}
        </>
    );

    const renderGridItem = (billing: any) => (
        <Card className="bg-white border hover:shadow-md transition-shadow">
            <CardContent className="p-4">
                <div className="space-y-3">
                    <div className="flex items-start gap-3">
                        <div className="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center border-2 border-blue-200 flex-shrink-0">
                            <DollarSign className="h-6 w-6 text-blue-600" />
                        </div>
                        <div className="flex-1 min-w-0">
                            <h3 className="font-semibold text-gray-900 truncate text-sm leading-tight">
                                {billing.patient_id || `Billing #${billing.id}`}
                            </h3>
                            <p className="text-xs text-gray-500 truncate mt-0.5">
                                {billing.patient?.name || billing.patient?.title || '-'}
                            </p>
                        </div>
                        <div className="flex items-center gap-1 flex-shrink-0">
                            {customRowActions(billing)}
                        </div>
                    </div>
                    
                    <div className="space-y-2">
                        <div className="flex flex-wrap gap-1">
                            <Badge variant={billing.status === 'active' ? 'default' : 'secondary'} className="text-xs px-2 py-0.5 capitalize">
                                {billing.status}
                            </Badge>
                        </div>
                        <div className="grid grid-cols-2 gap-2 text-xs">
                            <div className="flex justify-between">
                                <span className="text-gray-600 truncate">Amount:</span>
                                <span className="font-medium text-gray-900 truncate ml-1">
{billing.amount ? `$${billing.amount}` : '-'}                                </span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-600 truncate">Payment Date:</span>
                                <span className="font-medium text-gray-900 truncate ml-1">
{billing.payment_date ? formatDate(billing.payment_date) : '-'}                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div className="flex items-center justify-between text-xs text-gray-500 pt-2 border-t">
                        <div className="flex items-center gap-1">
                            <DollarSign className="h-3 w-3" />
                            <span>{t('Billing')}</span>
                        </div>
                        <div className="flex items-center gap-1">
                            <Calendar className="h-3 w-3" />
                            <span>{formatDate(billing.created_at_formatted)}</span>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>
    );

    const breadcrumbs = createBreadcrumbs('billings');

    return (
        <AuthenticatedLayout breadcrumbs={breadcrumbs}>
            <Head title={t('Billings')} />
            <div className="p-6">
                <YajraDataTable
                    ref={tableRef}
                    url="/billings"
                    columns={columns}
                    title={t('Billings')}
                    customActions={customActions}
                    customRowActions={customRowActions}
                    filters={filters}
                    gridViewEnabled={true}
                    renderGridItem={renderGridItem}
                    emptyStateIcon={<DollarSign className="h-8 w-8 text-gray-400" />}
                />
            
                {hasPermission('create_billing') && (
                    <BillingCreateModal
                        open={createModalOpen}
                        onOpenChange={setCreateModalOpen}
                        {...props}
                    />
                )}
                
                {hasPermission('edit_billing') && (
                    <BillingEditModal
                        open={editModalOpen}
                        onOpenChange={setEditModalOpen}
                        billing={selectedBilling}
                        {...props}
                    />
                )}
                
                {hasPermission('view_billing') && (
                    <BillingViewModal
                        open={viewModalOpen}
                        onOpenChange={setViewModalOpen}
                        billing={selectedBilling}
                        onEdit={hasPermission('edit_billing') ? (billing) => {
                            setSelectedBilling(billing);
                            setViewModalOpen(false);
                            setEditModalOpen(true);
                        } : undefined}
                    />
                )}
                
                <ConfirmationDialog
                    open={deleteDialogOpen}
                    onOpenChange={setDeleteDialogOpen}
                    title={t('Delete Billing')}
                    description={`Are you sure you want to delete ${selectedBilling?.name}? This action cannot be undone.`}
                    confirmText={t('Delete')}
                    variant="destructive"
                    onConfirm={() => {
                        router.delete(`/billings/${selectedBilling.id}`, {
                            onSuccess: () => {
                                toast.success(t('Billing deleted successfully'));
                                window.dispatchEvent(new CustomEvent('refreshDataTable'));
                                setDeleteDialogOpen(false);
                            },
                            onError: () => {
                                toast.error(t('Failed to delete billing'), { description: t('Please try again') });
                            },
                        });
                    }}
                />
                <Toaster />
            </div>
        </AuthenticatedLayout>
    );
}