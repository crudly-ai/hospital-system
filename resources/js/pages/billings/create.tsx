import { Button } from '@/components/ui/form/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/form/select';
import { CurrencyInput } from '@/components/ui/form/currency-input';
import { DatePicker } from '@/components/ui/form/date-picker';
import { Switch } from '@/components/ui/form/switch';
import { Label } from '@/components/ui/form/label';
import {
    Modal,
    ModalContent,
    ModalFooter,
    ModalHeader,
    ModalTitle,
} from '@/components/ui/overlay/modal';
import { useForm } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { useTranslations } from '@/hooks/use-translations';
import { useToast } from '@/hooks/use-toast';
import { Toaster } from '@/components/ui/feedback/toaster';
import { BillingCreateModalProps } from './types';

export function BillingCreateModal({ open, onOpenChange, ...props }: BillingCreateModalProps) {
    const { t } = useTranslations();
    const { toast } = useToast();

    
    // Extract relationship data from props
    const relationshipData = Object.keys(props).reduce((acc, key) => {
        if (Array.isArray(props[key]) && props[key].length > 0 && props[key][0]?.id) {
            acc[key] = props[key];
        }
        return acc;
    }, {} as any);
    const { data, setData, post, processing, errors, reset } = useForm({
        patient_id: null as number | null,
        amount: '0.00',
        payment_date: '',
        status: 'active' as 'active' | 'inactive',

    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/billings', {
            onSuccess: () => {
                toast.success(t('Billing created successfully'));
                reset();
                onOpenChange(false);
                window.dispatchEvent(new CustomEvent('refreshDataTable'));
            },
            onError: () => {
                toast.error(t('Failed to create billing'), { description: t('Please check the form and try again') });
            },
        });
    };

    const handleClose = () => {
        reset();
        onOpenChange(false);
    };

    useEffect(() => {
        if (!open) {
            reset();
        }
    }, [open]);



    return (
        <Modal open={open} onOpenChange={handleClose}>
            <ModalContent className="sm:max-w-[800px] max-h-[90vh] overflow-y-auto">
                <form onSubmit={handleSubmit}>
                    <ModalHeader>
                        <ModalTitle>{t('Create Billing')}</ModalTitle>
                    </ModalHeader>
                    
                    <div className="space-y-6 py-4">
                        <div className="space-y-2">
                            <Label htmlFor="patient_id">{t('Patient Id')}</Label>
                            <Select value={data.patient_id?.toString()} onValueChange={(value) => setData('patient_id', parseInt(value))}>
                                <SelectTrigger>
                                    <SelectValue placeholder={t('Select Patient')} />
                                </SelectTrigger>
                                <SelectContent>
                                    {relationshipData.patients?.map((patient) => (
                                        <SelectItem key={patient.id} value={patient.id.toString()}>
                                            {patient.name || patient.title}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="amount">{t('Amount')} *</Label>
                            <CurrencyInput
                                value={data.amount}
                                onChange={(value) => setData('amount', value)}
                                placeholder={t('Enter Amount')}
                                currencySymbol={window.currencySettings?.currency_symbol || '$'}
                                currencyPosition={window.currencySettings?.currency_position || 'before'}
                                decimalSeparator={window.currencySettings?.decimal_separator || '.'}
                                thousandSeparator={window.currencySettings?.thousand_separator || ','}
                            />
                            {errors.amount && <p className="text-red-500 text-sm mt-1">{errors.amount}</p>}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="payment_date">{t('Payment Date')} *</Label>
                            <DatePicker
                                value={data.payment_date}
                                onChange={(value) => setData('payment_date', value)}
                                placeholder={t('Select Payment Date')}
                            />
                            {errors.payment_date && <p className="text-red-500 text-sm mt-1">{errors.payment_date}</p>}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="status">{t('Status')} *</Label>
                            <div className="flex items-center space-x-2">
                                <Switch
                                    id="status"
                                    checked={data.status === 'active'}
                                    onCheckedChange={(checked) => setData('status', checked ? 'active' : 'inactive')}
                                />
                                <Label htmlFor="status">{t('Active Status')}</Label>
                            </div>
                        </div>


                    </div>

                    <ModalFooter>
                        <Button type="button" variant="outline" onClick={handleClose}>
                            {t('Cancel')}
                        </Button>
                        <Button type="submit" disabled={processing}>
                            {t('Create Billing')}
                        </Button>
                    </ModalFooter>
                </form>
            </ModalContent>
            <Toaster />
        </Modal>
    );
}