import { Button } from '@/components/ui/form/button';
import {
    Modal,
    ModalContent,
    ModalFooter,
    ModalHeader,
    ModalTitle,
} from '@/components/ui/overlay/modal';
import { useTranslations } from '@/hooks/use-translations';
import { useFormatters } from '@/utils/formatters';
import { DoctorViewModalProps } from './types';

export function DoctorViewModal({ open, onOpenChange, doctor, onEdit }: DoctorViewModalProps) {
    const { t } = useTranslations();
    const { formatDateTime } = useFormatters();
    if (!doctor) return null;

    return (
        <Modal open={open} onOpenChange={onOpenChange}>
            <ModalContent className="sm:max-w-[800px]">
                <ModalHeader>
                    <ModalTitle>{t('Doctor Details')}</ModalTitle>
                </ModalHeader>
                
                <div className="space-y-6 py-4">
                    <div className="space-y-4">
                        <div>
                            <h3 className="text-sm font-medium text-gray-500">{t('Name')}</h3>
                            <p className="mt-1 text-sm text-gray-900">{doctor.name}</p>
                        </div>
                        
                        <div>
                            <h3 className="text-sm font-medium text-gray-500">{t('Specialty')}</h3>
                            <p className="mt-1 text-sm text-gray-900">{doctor.specialty}</p>
                        </div>
                        
                        <div>
                            <h3 className="text-sm font-medium text-gray-500">{t('Department')}</h3>
                            <p className="mt-1 text-sm text-gray-900">
                                {doctor.department?.name || doctor.department?.title || '-'}
                            </p>
                        </div>
                        
                        <div>
                            <h3 className="text-sm font-medium text-gray-500">{t('Phone')}</h3>
                            <p className="mt-1 text-sm text-gray-900">{doctor.phone}</p>
                        </div>
                        
                        <div>
                            <h3 className="text-sm font-medium text-gray-500">{t('Email')}</h3>
                            <p className="mt-1 text-sm text-gray-900">{doctor.email}</p>
                        </div>
                        
                        <div>
                            <h3 className="text-sm font-medium text-gray-500">{t('Status')}</h3>
                            <span className={`inline-flex px-3 py-1 rounded-full text-xs font-medium capitalize ${
                                doctor.status === 'active' 
                                    ? 'bg-green-100 text-green-800' 
                                    : 'bg-red-100 text-red-800'
                            }`}>
                                {doctor.status}
                            </span>
                        </div>
                        

                        <div>
                            <h3 className="text-sm font-medium text-gray-500">{t('Created At')}</h3>
                            <p className="mt-1 text-sm text-gray-900">
                                {formatDateTime(doctor.created_at)}
                            </p>
                        </div>
                        
                        <div>
                            <h3 className="text-sm font-medium text-gray-500">{t('Updated At')}</h3>
                            <p className="mt-1 text-sm text-gray-900">
                                {formatDateTime(doctor.updated_at)}
                            </p>
                        </div>
                    </div>
                </div>

                <ModalFooter>
                    {onEdit && (
                        <Button
                            variant="outline"
                            onClick={() => {
                                onEdit(doctor);
                                onOpenChange(false);
                            }}
                        >
                            {t('Edit')}
                        </Button>
                    )}
                    <Button variant="outline" onClick={() => onOpenChange(false)}>
                        {t('Close')}
                    </Button>
                </ModalFooter>
            </ModalContent>
        </Modal>
    );
}