import { Button } from '@/components/ui/form/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/form/select';
import { DatePicker } from '@/components/ui/form/date-picker';
import { TimePicker } from '@/components/ui/form/time-picker';
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
import { AppointmentEditModalProps } from './types';

export function AppointmentEditModal({ open, onOpenChange, appointment, ...props }: AppointmentEditModalProps) {
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
        doctor_id: null as number | null,
        patient_id: null as number | null,
        date: '',
        time: '',
        status: 'active' as 'active' | 'inactive',

    });

    useEffect(() => {
        if (appointment && open) {
            setData({
                doctor_id: appointment.doctor_id,
                patient_id: appointment.patient_id,
                date: appointment.date,
                time: appointment.time,
                status: appointment.status,

            });

        } else if (!open) {
            reset();
        }
    }, [appointment, open]);



    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (appointment) {
            put(`/appointments/${appointment.id}`, {
                onSuccess: () => {
                    toast.success(t('Appointment updated successfully'));
                    reset();
                    onOpenChange(false);
                    window.dispatchEvent(new CustomEvent('refreshDataTable'));
                },
                onError: () => {
                    toast.error(t('Failed to update appointment'), { description: t('Please check the form and try again') });
                },
            });
        }
    };

    const handleClose = () => {
        reset();
        onOpenChange(false);
    };

    if (!appointment) return null;

    return (
        <Modal open={open} onOpenChange={handleClose}>
            <ModalContent className="sm:max-w-[800px] max-h-[90vh] overflow-y-auto">
                <form onSubmit={handleSubmit}>
                    <ModalHeader>
                        <ModalTitle>{t('Edit Appointment')}</ModalTitle>
                    </ModalHeader>
                    
                    <div className="space-y-6 py-4">
                        <div className="space-y-2">
                            <Label htmlFor="doctor_id">{t('Doctor Id')}</Label>
                            <Select value={data.doctor_id?.toString()} onValueChange={(value) => setData('doctor_id', parseInt(value))}>
                                <SelectTrigger>
                                    <SelectValue placeholder={t('Select Doctor')} />
                                </SelectTrigger>
                                <SelectContent>
                                    {relationshipData.doctors?.map((doctor) => (
                                        <SelectItem key={doctor.id} value={doctor.id.toString()}>
                                            {doctor.name || doctor.title}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>

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
                            <Label htmlFor="date">{t('Date')} *</Label>
                            <DatePicker
                                value={data.date}
                                onChange={(value) => setData('date', value)}
                                placeholder={t('Select Date')}
                            />
                            {errors.date && <p className="text-red-500 text-sm mt-1">{errors.date}</p>}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="time">{t('Time')} *</Label>
                            <TimePicker
                                value={data.time}
                                onChange={(value) => setData('time', value)}
                                placeholder={t('Select Time')}
                            />
                            {errors.time && <p className="text-red-500 text-sm mt-1">{errors.time}</p>}
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
                            {t('Update Appointment')}
                        </Button>
                    </ModalFooter>
                </form>
            </ModalContent>
            <Toaster />
        </Modal>
    );
}