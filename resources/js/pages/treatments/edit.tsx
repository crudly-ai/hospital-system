import { Button } from '@/components/ui/form/button';
import { Input } from '@/components/ui/form/input';
import { Textarea } from '@/components/ui/form/textarea';
import { CurrencyInput } from '@/components/ui/form/currency-input';
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
import { TreatmentEditModalProps } from './types';

export function TreatmentEditModal({ open, onOpenChange, treatment, ...props }: TreatmentEditModalProps) {
    const { t } = useTranslations();
    const { toast } = useToast();

    
    // Extract relationship data from props
    const relationshipData = Object.keys(props).reduce((acc, key) => {
        if (Array.isArray(props[key]) && props[key].length > 0 && props[key][0]?.id) {
            acc[key] = props[key];
        }
        return acc;
    }, {} as any);
    const { data, setData, put, processing, errors, reset } = useForm({
        name: '',
        description: '',
        cost: '0.00',
        status: 'active' as 'active' | 'inactive',

    });

    useEffect(() => {
        if (treatment && open) {
            setData({
                name: treatment.name,
                description: treatment.description || '',
                cost: treatment.cost,
                status: treatment.status,

            });

        } else if (!open) {
            reset();
        }
    }, [treatment, open]);



    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (treatment) {
            put(`/treatments/${treatment.id}`, {
                onSuccess: () => {
                    toast.success(t('Treatment updated successfully'));
                    reset();
                    onOpenChange(false);
                    window.dispatchEvent(new CustomEvent('refreshDataTable'));
                },
                onError: () => {
                    toast.error(t('Failed to update treatment'), { description: t('Please check the form and try again') });
                },
            });
        }
    };

    const handleClose = () => {
        reset();
        onOpenChange(false);
    };

    if (!treatment) return null;

    return (
        <Modal open={open} onOpenChange={handleClose}>
            <ModalContent className="sm:max-w-[800px] max-h-[90vh] overflow-y-auto">
                <form onSubmit={handleSubmit}>
                    <ModalHeader>
                        <ModalTitle>{t('Edit Treatment')}</ModalTitle>
                    </ModalHeader>
                    
                    <div className="space-y-6 py-4">
                        <div className="space-y-2">
                            <Label htmlFor="name">{t('Name')} *</Label>
                            <Input
                                id="name"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                error={errors.name}
                            />
                            {errors.name && <p className="text-red-500 text-sm mt-1">{errors.name}</p>}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="description">{t('Description')}</Label>
                            <Textarea
                                id="description"
                                value={data.description}
                                onChange={(e) => setData('description', e.target.value)}
                                rows={4}
                            />
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="cost">{t('Cost')} *</Label>
                            <CurrencyInput
                                value={data.cost}
                                onChange={(value) => setData('cost', value)}
                                placeholder={t('Enter Cost')}
                                currencySymbol={window.currencySettings?.currency_symbol || '$'}
                                currencyPosition={window.currencySettings?.currency_position || 'before'}
                                decimalSeparator={window.currencySettings?.decimal_separator || '.'}
                                thousandSeparator={window.currencySettings?.thousand_separator || ','}
                            />
                            {errors.cost && <p className="text-red-500 text-sm mt-1">{errors.cost}</p>}
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
                            {t('Update Treatment')}
                        </Button>
                    </ModalFooter>
                </form>
            </ModalContent>
            <Toaster />
        </Modal>
    );
}