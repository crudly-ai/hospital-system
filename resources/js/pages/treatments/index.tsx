import { YajraDataTable } from '@/components/datatable';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { createBreadcrumbs } from '@/layouts/app/breadcrumbs';
import { Head, router } from '@inertiajs/react';
import { useFormatters } from '@/utils/formatters';
import { TreatmentCreateModal } from './create';
import { TreatmentEditModal } from './edit';
import { TreatmentViewModal } from './view';
import { TreatmentIndexProps } from './types';
import { usePermissions } from '@/hooks/use-permissions';
import { useTranslations } from '@/hooks/use-translations';
import { useToast } from '@/hooks/use-toast';
import { Button } from '@/components/ui/form/button';
import { Toaster } from '@/components/ui/feedback/toaster';
import { Card, CardContent } from '@/components/ui/layout/card';
import { Badge } from '@/components/ui/feedback/badge';
import { Plus, Eye, Edit, Trash2, Calendar, Activity } from 'lucide-react';
import React, { useState, useRef, useEffect } from 'react';
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';

export default function Index(props: TreatmentIndexProps) {
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
    const [selectedTreatment, setSelectedTreatment] = useState<any>(null);
    const tableRef = useRef<any>(null);


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
            data: 'description',
            name: 'description',
            title: t('Description'),
            orderable: true,
            searchable: true,
        },
        {
            data: 'cost',
            name: 'cost',
            title: t('Cost'),
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
            key: 'cost_min',
            label: t('Cost Min'),
            type: 'number' as const,
            placeholder: t('Min amount')
        },
        {
            key: 'cost_max',
            label: t('Cost Max'),
            type: 'number' as const,
            placeholder: t('Max amount')
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
            {hasPermission('create_treatment') && (
                <Button onClick={() => setCreateModalOpen(true)}>
                    <Plus className="h-4 w-4 mr-2" />
                    {t('Create Treatment')}
                </Button>
            )}
        </>
    );

    const customRowActions = (item: any) => (
        <>
            {hasPermission('view_treatment') && (
                <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => {
                        setSelectedTreatment(item);
                        setViewModalOpen(true);
                    }}
                >
                    <Eye className="h-4 w-4" />
                </Button>
            )}
            {hasPermission('edit_treatment') && (
                <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => {
                        setSelectedTreatment(item);
                        setEditModalOpen(true);
                    }}
                >
                    <Edit className="h-4 w-4" />
                </Button>
            )}
            {hasPermission('delete_treatment') && (
                <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => {
                        setSelectedTreatment(item);
                        setDeleteDialogOpen(true);
                    }}
                >
                    <Trash2 className="h-4 w-4" />
                </Button>
            )}
        </>
    );

    const renderGridItem = (treatment: any) => (
        <Card className="bg-white border hover:shadow-md transition-shadow">
            <CardContent className="p-4">
                <div className="space-y-3">
                    <div className="flex items-start gap-3">
                        <div className="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center border-2 border-blue-200 flex-shrink-0">
                            <Activity className="h-6 w-6 text-blue-600" />
                        </div>
                        <div className="flex-1 min-w-0">
                            <h3 className="font-semibold text-gray-900 truncate text-sm leading-tight">
                                {treatment.name || `Treatment #${treatment.id}`}
                            </h3>
                            <p className="text-xs text-gray-500 truncate mt-0.5">
                                {treatment.description || '-'}
                            </p>
                        </div>
                        <div className="flex items-center gap-1 flex-shrink-0">
                            {customRowActions(treatment)}
                        </div>
                    </div>
                    
                    <div className="space-y-2">
                        <div className="flex flex-wrap gap-1">
                            <Badge variant={treatment.status === 'active' ? 'default' : 'secondary'} className="text-xs px-2 py-0.5 capitalize">
                                {treatment.status}
                            </Badge>
                        </div>
                        <div className="grid grid-cols-2 gap-2 text-xs">
                            <div className="flex justify-between">
                                <span className="text-gray-600 truncate">Cost:</span>
                                <span className="font-medium text-gray-900 truncate ml-1">
{treatment.cost ? `$${treatment.cost}` : '-'}                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div className="flex items-center justify-between text-xs text-gray-500 pt-2 border-t">
                        <div className="flex items-center gap-1">
                            <Activity className="h-3 w-3" />
                            <span>{t('Treatment')}</span>
                        </div>
                        <div className="flex items-center gap-1">
                            <Calendar className="h-3 w-3" />
                            <span>{formatDate(treatment.created_at_formatted)}</span>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>
    );

    const breadcrumbs = createBreadcrumbs('treatments');

    return (
        <AuthenticatedLayout breadcrumbs={breadcrumbs}>
            <Head title={t('Treatments')} />
            <div className="p-6">
                <YajraDataTable
                    ref={tableRef}
                    url="/treatments"
                    columns={columns}
                    title={t('Treatments')}
                    customActions={customActions}
                    customRowActions={customRowActions}
                    filters={filters}
                    gridViewEnabled={true}
                    renderGridItem={renderGridItem}
                    emptyStateIcon={<Activity className="h-8 w-8 text-gray-400" />}
                />
            
                {hasPermission('create_treatment') && (
                    <TreatmentCreateModal
                        open={createModalOpen}
                        onOpenChange={setCreateModalOpen}
                        {...props}
                    />
                )}
                
                {hasPermission('edit_treatment') && (
                    <TreatmentEditModal
                        open={editModalOpen}
                        onOpenChange={setEditModalOpen}
                        treatment={selectedTreatment}
                        {...props}
                    />
                )}
                
                {hasPermission('view_treatment') && (
                    <TreatmentViewModal
                        open={viewModalOpen}
                        onOpenChange={setViewModalOpen}
                        treatment={selectedTreatment}
                        onEdit={hasPermission('edit_treatment') ? (treatment) => {
                            setSelectedTreatment(treatment);
                            setViewModalOpen(false);
                            setEditModalOpen(true);
                        } : undefined}
                    />
                )}
                
                <ConfirmationDialog
                    open={deleteDialogOpen}
                    onOpenChange={setDeleteDialogOpen}
                    title={t('Delete Treatment')}
                    description={`Are you sure you want to delete ${selectedTreatment?.name}? This action cannot be undone.`}
                    confirmText={t('Delete')}
                    variant="destructive"
                    onConfirm={() => {
                        router.delete(`/treatments/${selectedTreatment.id}`, {
                            onSuccess: () => {
                                toast.success(t('Treatment deleted successfully'));
                                window.dispatchEvent(new CustomEvent('refreshDataTable'));
                                setDeleteDialogOpen(false);
                            },
                            onError: () => {
                                toast.error(t('Failed to delete treatment'), { description: t('Please try again') });
                            },
                        });
                    }}
                />
                <Toaster />
            </div>
        </AuthenticatedLayout>
    );
}