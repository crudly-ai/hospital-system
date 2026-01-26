import { BaseModel } from '@/types/crud';

export interface Patient extends BaseModel {
    name: string;
    dob: string;
    phone: string;
    email: string;
    address: string;
    status: 'active' | 'inactive';

    created_at_formatted: string;
}

export interface PatientFormData {
    name: string;
    dob: string;
    phone: string;
    email: string;
    address: string;
    status: 'active' | 'inactive';

}

export interface PatientIndexProps {
    dobOptions?: { value: string; label: string }[];
    statusOptions?: { value: string; label: string }[];

}

export interface PatientCreateModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
}

export interface PatientEditModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    patient: Patient | null;
}

export interface PatientViewModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    patient: Patient | null;
    onEdit?: (patient: Patient) => void;
}