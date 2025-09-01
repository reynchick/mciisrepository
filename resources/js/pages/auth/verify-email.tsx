// Components
import EmailVerificationNotificationController from '@/actions/App/Http/Controllers/Auth/EmailVerificationNotificationController';
import { login, logout } from '@/routes';
import { Form, Head } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';

import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/auth-layout';

export default function VerifyEmail({ status, isGuest }: { status?: string; isGuest?: boolean }) {
    return (
        <AuthLayout title="Verify email" description="Please verify your email address by clicking on the link we just emailed to you.">
            <Head title="Email verification" />

            {status === 'verification-link-sent' && (
                <div className="mb-4 text-center text-sm font-medium text-green-600">
                    A new verification link has been sent to the email address you provided during registration.
                </div>
            )}

            {status && status !== 'verification-link-sent' && (
                <div className="mb-4 text-center text-sm font-medium text-green-600">
                    {status}
                </div>
            )}

            {!isGuest ? (
                <Form {...EmailVerificationNotificationController.store.form()} className="space-y-6 text-center">
                    {({ processing }) => (
                        <>
                            <Button disabled={processing} variant="secondary">
                                {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                                Resend verification email
                            </Button>

                            <TextLink href={logout()} className="mx-auto block text-sm">
                                Log out
                            </TextLink>
                        </>
                    )}
                </Form>
            ) : (
                <div className="space-y-6 text-center">
                    <p className="text-sm text-muted-foreground">
                        Please check your email for a verification link. Once verified, you can log in to your account.
                    </p>
                    
                    <TextLink href={login()} className="mx-auto block text-sm">
                        Back to login
                    </TextLink>
                </div>
            )}
        </AuthLayout>
    );
}
