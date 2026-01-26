import { Button } from '@/components/ui/form/button';
import { Input } from '@/components/ui/form/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/form/select';
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
import { DoctorCreateModalProps } from './types';

export function DoctorCreateModal({ open, onOpenChange, ...props }: DoctorCreateModalProps) {
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
        name: '',
        specialty: '',
        department_id: null as number | null,
        phone: '',
        email: '',
        status: 'active' as 'active' | 'inactive',

    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/doctors', {
            onSuccess: () => {
                toast.success(t('Doctor created successfully'));
                reset();
                onOpenChange(false);
                window.dispatchEvent(new CustomEvent('refreshDataTable'));
            },
            onError: () => {
                toast.error(t('Failed to create doctor'), { description: t('Please check the form and try again') });
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
                        <ModalTitle>{t('Create Doctor')}</ModalTitle>
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
                            <Label htmlFor="specialty">{t('Specialty')} *</Label>
                            <Input
                                id="specialty"
                                value={data.specialty}
                                onChange={(e) => setData('specialty', e.target.value)}
                                error={errors.specialty}
                            />
                            {errors.specialty && <p className="text-red-500 text-sm mt-1">{errors.specialty}</p>}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="department_id">{t('Department Id')}</Label>
                            <Select value={data.department_id?.toString()} onValueChange={(value) => setData('department_id', parseInt(value))}>
                                <SelectTrigger>
                                    <SelectValue placeholder={t('Select Department')} />
                                </SelectTrigger>
                                <SelectContent>
                                    {relationshipData.departments?.map((department) => (
                                        <SelectItem key={department.id} value={department.id.toString()}>
                                            {department.name || department.title}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="phone">{t('Phone')}</Label>
                            <Input
                                id="phone"
                                value={data.phone}
                                onChange={(e) => setData('phone', e.target.value)}
                            />
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="email">{t('Email')}</Label>
                            <Input
                                id="email"
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                            />
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
                            {t('Create Doctor')}
                        </Button>
                    </ModalFooter>
                </form>
            </ModalContent>
            <Toaster />
        </Modal>
    );
}