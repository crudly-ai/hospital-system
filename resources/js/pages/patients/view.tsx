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
import { PatientViewModalProps } from './types';

export function PatientViewModal({ open, onOpenChange, patient, onEdit }: PatientViewModalProps) {
    const { t } = useTranslations();
    const { formatDateTime } = useFormatters();
    if (!patient) return null;

    return (
        <Modal open={open} onOpenChange={onOpenChange}>
            <ModalContent className="sm:max-w-[800px]">
                <ModalHeader>
                    <ModalTitle>{t('Patient Details')}</ModalTitle>
                </ModalHeader>
                
                <div className="space-y-6 py-4">
                    <div className="space-y-4">
                        <div>
                            <h3 className="text-sm font-medium text-gray-500">{t('Name')}</h3>
                            <p className="mt-1 text-sm text-gray-900">{patient.name}</p>
                        </div>
                        
                        <div>
                            <h3 className="text-sm font-medium text-gray-500">{t('Dob')}</h3>
                            <p className="mt-1 text-sm text-gray-900">{patient.dob}</p>
                        </div>
                        
                        <div>
                            <h3 className="text-sm font-medium text-gray-500">{t('Phone')}</h3>
                            <p className="mt-1 text-sm text-gray-900">{patient.phone}</p>
                        </div>
                        
                        <div>
                            <h3 className="text-sm font-medium text-gray-500">{t('Email')}</h3>
                            <p className="mt-1 text-sm text-gray-900">{patient.email}</p>
                        </div>
                        
                        <div>
                            <h3 className="text-sm font-medium text-gray-500">{t('Address')}</h3>
                            <p className="mt-1 text-sm text-gray-900">
                                {patient.address || t('No description provided')}
                            </p>
                        </div>
                        
                        <div>
                            <h3 className="text-sm font-medium text-gray-500">{t('Status')}</h3>
                            <span className={`inline-flex px-3 py-1 rounded-full text-xs font-medium capitalize ${
                                patient.status === 'active' 
                                    ? 'bg-green-100 text-green-800' 
                                    : 'bg-red-100 text-red-800'
                            }`}>
                                {patient.status}
                            </span>
                        </div>
                        

                        <div>
                            <h3 className="text-sm font-medium text-gray-500">{t('Created At')}</h3>
                            <p className="mt-1 text-sm text-gray-900">
                                {formatDateTime(patient.created_at)}
                            </p>
                        </div>
                        
                        <div>
                            <h3 className="text-sm font-medium text-gray-500">{t('Updated At')}</h3>
                            <p className="mt-1 text-sm text-gray-900">
                                {formatDateTime(patient.updated_at)}
                            </p>
                        </div>
                    </div>
                </div>

                <ModalFooter>
                    {onEdit && (
                        <Button
                            variant="outline"
                            onClick={() => {
                                onEdit(patient);
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