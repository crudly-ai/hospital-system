import AppLogoIcon from '@/components/app-logo-icon';
import { Link } from '@inertiajs/react';
import { BrandProvider } from '@/contexts/brand-context';
import { LanguageSwitcher } from '@/components/language-switcher';

interface AuthLayoutProps {
    children: React.ReactNode;
    name?: string;
    title?: string;
    description?: string;
}

export default function AuthSimpleLayout({ children, title, description }: AuthLayoutProps) {
    return (
        <BrandProvider>
            <div className="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-4 md:p-10 relative">
                <div className="absolute top-6 right-6">
                    <LanguageSwitcher />
                </div>
                <div className="w-full max-w-sm">
                    <div className="flex flex-col gap-8 shadow-xl rounded-xl p-4" style={{boxShadow:'0 0 5px #0000003b'}}>
                        <div className="flex flex-col items-center gap-4">
                            <Link href={route('login')} className="flex flex-col items-center gap-2 font-medium">
                                <div className="mb-1 flex h-auto w-28 items-center justify-center rounded-md">
                                    <AppLogoIcon className="h-auto w-auto object-contain" />
                                </div>
                                <span className="sr-only">{title}</span>
                            </Link>

                            <div className="space-y-2 text-center">
                                <h1 className="md:text-2xl text-xl font-medium">{title}</h1>
                                <p className="text-muted-foreground text-center text-sm">{description}</p>
                            </div>
                        </div>
                        {children}
                    </div>
                </div>
            </div>
        </BrandProvider>
    );
}
