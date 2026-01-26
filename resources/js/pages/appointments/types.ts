import { BaseModel } from '@/types/crud';

export interface Appointment extends BaseModel {
    doctor_id: number;
    patient_id: number;
    date: string;
    time: string;
    status: 'active' | 'inactive';

    created_at_formatted: string;
}

export interface AppointmentFormData {
    doctor_id: number | null;
    patient_id: number | null;
    date: string;
    time: string;
    status: 'active' | 'inactive';

}

export interface AppointmentIndexProps {
    doctors?: { id: number; name?: string; title?: string }[];
    doctor_idOptions?: { value: string; label: string }[];
    patients?: { id: number; name?: string; title?: string }[];
    patient_idOptions?: { value: string; label: string }[];
    dateOptions?: { value: string; label: string }[];
    timeOptions?: { value: string; label: string }[];
    statusOptions?: { value: string; label: string }[];

}

export interface AppointmentCreateModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
}

export interface AppointmentEditModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    appointment: Appointment | null;
}

export interface AppointmentViewModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    appointment: Appointment | null;
    onEdit?: (appointment: Appointment) => void;
}