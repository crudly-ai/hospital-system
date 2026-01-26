import { BaseModel } from '@/types/crud';

export interface Billing extends BaseModel {
    patient_id: number;
    amount: string;
    payment_date: string;
    status: 'active' | 'inactive';

    created_at_formatted: string;
}

export interface BillingFormData {
    patient_id: number | null;
    amount: string;
    payment_date: string;
    status: 'active' | 'inactive';

}

export interface BillingIndexProps {
    patients?: { id: number; name?: string; title?: string }[];
    patient_idOptions?: { value: string; label: string }[];
    amountOptions?: { value: string; label: string }[];
    payment_dateOptions?: { value: string; label: string }[];
    statusOptions?: { value: string; label: string }[];

}

export interface BillingCreateModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
}

export interface BillingEditModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    billing: Billing | null;
}

export interface BillingViewModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    billing: Billing | null;
    onEdit?: (billing: Billing) => void;
}