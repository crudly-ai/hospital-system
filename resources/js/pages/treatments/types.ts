import { BaseModel } from '@/types/crud';

export interface Treatment extends BaseModel {
    name: string;
    description: string;
    cost: string;
    status: 'active' | 'inactive';

    created_at_formatted: string;
}

export interface TreatmentFormData {
    name: string;
    description: string;
    cost: string;
    status: 'active' | 'inactive';

}

export interface TreatmentIndexProps {
    costOptions?: { value: string; label: string }[];
    statusOptions?: { value: string; label: string }[];

}

export interface TreatmentCreateModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
}

export interface TreatmentEditModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    treatment: Treatment | null;
}

export interface TreatmentViewModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    treatment: Treatment | null;
    onEdit?: (treatment: Treatment) => void;
}