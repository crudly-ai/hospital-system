// Components
import { Head, useForm } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import { FormEventHandler } from 'react';
import { useTranslations } from '@/hooks/use-translations';
import { useToast } from '@/hooks/use-toast';
import { Toaster } from '@/components/ui/feedback/toaster';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/form/button';
import { Input } from '@/components/ui/form/input';
import { Label } from '@/components/ui/form/label';
import AuthLayout from '@/layouts/auth-layout';

export default function ForgotPassword({ status }: { status?: string }) {
    const { t } = useTranslations();
    const { toast } = useToast();
    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('password.email'), {
            onSuccess: () => {
                toast.success(t('Reset link sent'), { description: t('Check your email for the password reset link') });
            },
            onError: () => {
                toast.error(t('Failed to send reset link'), { description: t('Please check your email and try again') });
            },
        });
    };

    return (
        <AuthLayout title={t('Forgot password')} description={t('Enter your email to receive a password reset link')}>
            <Head title={t('Forgot password')} />

            {status && <div className="mb-4 text-center text-sm font-medium text-green-600">{status}</div>}

            <div className="space-y-6">
                <form onSubmit={submit}>
                    <div className="grid gap-2">
                        <Label htmlFor="email">{t('Email address')}</Label>
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            autoComplete="off"
                            value={data.email}
                            autoFocus
                            onChange={(e) => setData('email', e.target.value)}
                            placeholder={t('email@example.com')}
                        />

                        <InputError message={errors.email} />
                    </div>

                    <div className="my-6 flex items-center justify-start">
                        <Button className="w-full" disabled={processing}>
                            {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                            {t('Email password reset link')}
                        </Button>
                    </div>
                </form>

                <div className="text-muted-foreground space-x-1 text-center text-sm">
                    <span>{t('Or, return to')}</span>
                    <TextLink href={route('login')}>{t('log in')}</TextLink>
                </div>
            </div>
            <Toaster />
        </AuthLayout>
    );
}
