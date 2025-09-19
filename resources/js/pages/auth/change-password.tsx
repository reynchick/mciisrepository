import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';
import { Form, Head } from '@inertiajs/react';
import { LoaderCircle, Shield, User } from 'lucide-react';

interface ChangePasswordProps {
    user: {
        name: string;
        email: string;
        role: string;
    };
}

export default function ChangePassword({ user }: ChangePasswordProps) {
    return (
        <AuthLayout 
            title="Change Password" 
            description="Please change your temporary password to something more secure"
        >
            <Head title="Change Password" />

            <div className="mb-6 rounded-lg border border-amber-200 bg-amber-50 p-4">
                <div className="flex items-start gap-3">
                    <Shield className="h-5 w-5 text-amber-600 mt-0.5" />
                    <div>
                        <h3 className="font-medium text-amber-800">Security Notice</h3>
                        <p className="text-sm text-amber-700 mt-1">
                            For security reasons, you must change your temporary password before accessing the system.
                        </p>
                    </div>
                </div>
            </div>

            <div className="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
                <div className="flex items-center gap-3">
                    <User className="h-5 w-5 text-gray-600" />
                    <div>
                        <p className="font-medium text-gray-900">{user.name}</p>
                        <p className="text-sm text-gray-600">{user.email}</p>
                        <p className="text-sm text-gray-600">{user.role}</p>
                    </div>
                </div>
            </div>

            <Form
                action="/change-password"
                method="post"
                className="flex flex-col gap-6"
                resetOnSuccess={['current_password', 'password', 'password_confirmation']}
            >
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-6">
                            <div className="grid gap-2">
                                <Label htmlFor="current_password">Current Password</Label>
                                <Input
                                    id="current_password"
                                    type="password"
                                    name="current_password"
                                    required
                                    autoFocus
                                    tabIndex={1}
                                    autoComplete="current-password"
                                    placeholder="Enter your current password"
                                />
                                <InputError message={errors.current_password} />
                                <p className="text-xs text-gray-600">
                                    Use your temporary password: <code className="bg-gray-100 px-1 rounded">TempPassword123!</code>
                                </p>
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password">New Password</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    tabIndex={2}
                                    autoComplete="new-password"
                                    placeholder="Enter your new password"
                                />
                                <InputError message={errors.password} />
                                <p className="text-xs text-gray-600">
                                    Password must be at least 8 characters long and include a mix of letters, numbers, and symbols.
                                </p>
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password_confirmation">Confirm New Password</Label>
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    required
                                    tabIndex={3}
                                    autoComplete="new-password"
                                    placeholder="Confirm your new password"
                                />
                                <InputError message={errors.password_confirmation} />
                            </div>

                            <Button type="submit" className="mt-4 w-full" tabIndex={4} disabled={processing}>
                                {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                                Change Password
                            </Button>
                        </div>

                        <div className="text-center text-sm text-muted-foreground">
                            <p>Once you change your password, you'll be redirected to the dashboard.</p>
                        </div>
                    </>
                )}
            </Form>
        </AuthLayout>
    );
}
