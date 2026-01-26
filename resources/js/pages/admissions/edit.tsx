import { Button } from '@/components/ui/form/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/form/select';
import { DatePicker } from '@/components/ui/form/date-picker';
import { Input } from '@/components/ui/form/input';
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
import { AdmissionEditModalProps } from './types';

export function AdmissionEditModal({ open, onOpenChange, admission, ...props }: AdmissionEditModalProps) {
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
        patient_id: null as number | null,
        admitted_on: '',
        discharged_on: '',
        room_number: '',
        status: 'active' as 'active' | 'inactive',

    });

    useEffect(() => {
        if (admission && open) {
            setData({
                patient_id: admission.patient_id,
                admitted_on: admission.admitted_on,
                discharged_on: admission.discharged_on,
                room_number: admission.room_number,
                status: admission.status,

            });

        } else if (!open) {
            reset();
        }
    }, [admission, open]);



    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (admission) {
            put(`/admissions/${admission.id}`, {
                onSuccess: () => {
                    toast.success(t('Admission updated successfully'));
                    reset();
                    onOpenChange(false);
                    window.dispatchEvent(new CustomEvent('refreshDataTable'));
                },
                onError: () => {
                    toast.error(t('Failed to update admission'), { description: t('Please check the form and try again') });
                },
            });
        }
    };

    const handleClose = () => {
        reset();
        onOpenChange(false);
    };

    if (!admission) return null;

    return (
        <Modal open={open} onOpenChange={handleClose}>
            <ModalContent className="sm:max-w-[800px] max-h-[90vh] overflow-y-auto">
                <form onSubmit={handleSubmit}>
                    <ModalHeader>
                        <ModalTitle>{t('Edit Admission')}</ModalTitle>
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
                            <Label htmlFor="admitted_on">{t('Admitted On')} *</Label>
                            <DatePicker
                                value={data.admitted_on}
                                onChange={(value) => setData('admitted_on', value)}
                                placeholder={t('Select Admitted On')}
                            />
                            {errors.admitted_on && <p className="text-red-500 text-sm mt-1">{errors.admitted_on}</p>}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="discharged_on">{t('Discharged On')}</Label>
                            <DatePicker
                                value={data.discharged_on}
                                onChange={(value) => setData('discharged_on', value)}
                                placeholder={t('Select Discharged On')}
                            />
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="room_number">{t('Room Number')} *</Label>
                            <Input
                                id="room_number"
                                value={data.room_number}
                                onChange={(e) => setData('room_number', e.target.value)}
                                error={errors.room_number}
                            />
                            {errors.room_number && <p className="text-red-500 text-sm mt-1">{errors.room_number}</p>}
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
                            {t('Update Admission')}
                        </Button>
                    </ModalFooter>
                </form>
            </ModalContent>
            <Toaster />
        </Modal>
    );
}