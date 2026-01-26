import { BaseModel } from '@/types/crud';

export interface Doctor extends BaseModel {
    name: string;
    specialty: string;
    department_id: number;
    phone: string;
    email: string;
    status: 'active' | 'inactive';

    created_at_formatted: string;
}

export interface DoctorFormData {
    name: string;
    specialty: string;
    department_id: number | null;
    phone: string;
    email: string;
    status: 'active' | 'inactive';

}

export interface DoctorIndexProps {
    departments?: { id: number; name?: string; title?: string }[];
    department_idOptions?: { value: string; label: string }[];
    statusOptions?: { value: string; label: string }[];

}

export interface DoctorCreateModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
}

export interface DoctorEditModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    doctor: Doctor | null;
}

export interface DoctorViewModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    doctor: Doctor | null;
    onEdit?: (doctor: Doctor) => void;
}