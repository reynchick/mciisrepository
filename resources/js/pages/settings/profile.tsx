import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { send } from '@/routes/verification';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Transition } from '@headlessui/react';
import { Form, Head, Link, usePage } from '@inertiajs/react';

import DeleteUser from '@/components/delete-user';
import HeadingSmall from '@/components/heading-small';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { edit } from '@/routes/profile';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: edit().url,
    },
];

export default function Profile({ mustVerifyEmail, status }: { mustVerifyEmail: boolean; status?: string }) {
    const { auth } = usePage<SharedData>().props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Profile settings" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall title="Profile information" description="Update your profile information" />

                    <Form
                        {...ProfileController.update.form()}
                        options={{
                            preserveScroll: true,
                        }}
                        className="space-y-6"
                    >
                        {({ processing, recentlySuccessful, errors }) => (
                            <>
                                {/* Name Fields */}
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div className="grid gap-2">
                                        <Label htmlFor="firstName">First Name *</Label>
                                        <Input
                                            id="firstName"
                                            className="mt-1 block w-full"
                                            defaultValue={auth.user.firstName}
                                            name="firstName"
                                            required
                                            autoComplete="given-name"
                                            placeholder="First name"
                                        />
                                        <InputError className="mt-2" message={errors.firstName} />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="middleName">Middle Name</Label>
                                        <Input
                                            id="middleName"
                                            className="mt-1 block w-full"
                                            defaultValue={auth.user.middleName}
                                            name="middleName"
                                            autoComplete="additional-name"
                                            placeholder="Middle name"
                                        />
                                        <InputError className="mt-2" message={errors.middleName} />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="lastName">Last Name *</Label>
                                        <Input
                                            id="lastName"
                                            className="mt-1 block w-full"
                                            defaultValue={auth.user.lastName}
                                            name="lastName"
                                            required
                                            autoComplete="family-name"
                                            placeholder="Last name"
                                        />
                                        <InputError className="mt-2" message={errors.lastName} />
                                    </div>
                                </div>

                                {/* Student ID Field */}
                                <div className="grid gap-2">
                                    <Label htmlFor="studentID">Student ID</Label>
                                    <Input
                                        id="studentID"
                                        className="mt-1 block w-full"
                                        defaultValue={auth.user.studentID}
                                        name="studentID"
                                        placeholder="2023-00800"
                                    />
                                    <InputError className="mt-2" message={errors.studentID} />
                                </div>

                                {/* Contact Number */}
                                <div className="grid gap-2">
                                    <Label htmlFor="contactNumber">Contact Number *</Label>
                                    <Input
                                        id="contactNumber"
                                        type="tel"
                                        className="mt-1 block w-full"
                                        defaultValue={auth.user.contactNumber}
                                        name="contactNumber"
                                        required
                                        autoComplete="tel"
                                        placeholder="09XXXXXXXXX or +63 9XXXXXXXXX"
                                    />
                                    <InputError className="mt-2" message={errors.contactNumber} />
                                </div>

                                {/* Email Field */}
                                <div className="grid gap-2">
                                    <Label htmlFor="email">Email address *</Label>
                                    <Input
                                        id="email"
                                        type="email"
                                        className="mt-1 block w-full"
                                        defaultValue={auth.user.email}
                                        name="email"
                                        required
                                        autoComplete="username"
                                        placeholder="email@usep.edu.ph"
                                    />
                                    <InputError className="mt-2" message={errors.email} />
                                </div>

                                {/* Role Selection */}
                                <div className="grid gap-2">
                                    <Label htmlFor="role">Role *</Label>
                                    <Select name="role" defaultValue={auth.user.role}>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select your role" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="Student">Student</SelectItem>
                                            <SelectItem value="Faculty">Faculty</SelectItem>
                                            <SelectItem value="MCIIS Staff">MCIIS Staff</SelectItem>
                                            <SelectItem value="Administrator">Administrator</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <InputError className="mt-2" message={errors.role} />
                                </div>

                                {mustVerifyEmail && auth.user.email_verified_at === null && (
                                    <div>
                                        <p className="-mt-4 text-sm text-muted-foreground">
                                            Your email address is unverified.{' '}
                                            <Link
                                                href={send()}
                                                as="button"
                                                className="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                                            >
                                                Click here to resend the verification email.
                                            </Link>
                                        </p>

                                        {status === 'verification-link-sent' && (
                                            <div className="mt-2 text-sm font-medium text-green-600">
                                                A new verification link has been sent to your email address.
                                            </div>
                                        )}
                                    </div>
                                )}

                                <div className="flex items-center gap-4">
                                    <Button disabled={processing}>Save</Button>

                                    <Transition
                                        show={recentlySuccessful}
                                        enter="transition ease-in-out"
                                        enterFrom="opacity-0"
                                        leave="transition ease-in-out"
                                        leaveTo="opacity-0"
                                    >
                                        <p className="text-sm text-neutral-600">Saved</p>
                                    </Transition>
                                </div>
                            </>
                        )}
                    </Form>
                </div>

                <DeleteUser />
            </SettingsLayout>
        </AppLayout>
    );
}