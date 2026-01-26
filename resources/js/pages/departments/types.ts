import { BaseModel } from '@/types/crud';

export interface Department extends BaseModel {
    name: string;
    description: string;
    status: 'active' | 'inactive';

    created_at_formatted: string;
}

export interface DepartmentFormData {
    name: string;
    description: string;
    status: 'active' | 'inactive';

}

export interface DepartmentIndexProps {
    statusOptions?: { value: string; label: string }[];

}

export interface DepartmentCreateModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
}

export interface DepartmentEditModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    department: Department | null;
}

export interface DepartmentViewModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    department: Department | null;
    onEdit?: (department: Department) => void;
}