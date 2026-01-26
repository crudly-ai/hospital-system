import { BaseModel } from '@/types/crud';

export interface Admission extends BaseModel {
    patient_id: number;
    admitted_on: string;
    discharged_on: string;
    room_number: string;
    status: 'active' | 'inactive';

    created_at_formatted: string;
}

export interface AdmissionFormData {
    patient_id: number | null;
    admitted_on: string;
    discharged_on: string;
    room_number: string;
    status: 'active' | 'inactive';

}

export interface AdmissionIndexProps {
    patients?: { id: number; name?: string; title?: string }[];
    patient_idOptions?: { value: string; label: string }[];
    admitted_onOptions?: { value: string; label: string }[];
    discharged_onOptions?: { value: string; label: string }[];
    statusOptions?: { value: string; label: string }[];

}

export interface AdmissionCreateModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
}

export interface AdmissionEditModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    admission: Admission | null;
}

export interface AdmissionViewModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    admission: Admission | null;
    onEdit?: (admission: Admission) => void;
}