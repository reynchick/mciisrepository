import RegisteredUserController from '@/actions/App/Http/Controllers/Auth/RegisteredUserController';
import { login } from '@/routes';
import { Form, Head } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import { useState } from 'react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AuthLayout from '@/layouts/auth-layout';

export default function Register() {
    const [selectedRole, setSelectedRole] = useState('Student');

    return (
        <AuthLayout title="Create an account" description="Enter your details below to create your account">
            <Head title="Register" />
            <Form
                {...RegisteredUserController.store.form()}
                resetOnSuccess={['password', 'password_confirmation']}
                disableWhileProcessing
                className="flex flex-col gap-6"
            >
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-6">
                            {/* Name Fields */}
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="firstName">First Name *</Label>
                                    <Input
                                        id="firstName"
                                        type="text"
                                        required
                                        autoFocus
                                        tabIndex={1}
                                        autoComplete="given-name"
                                        name="firstName"
                                        placeholder="First name"
                                    />
                                    <InputError message={errors.firstName} className="mt-2" />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="middleName">Middle Name</Label>
                                    <Input
                                        id="middleName"
                                        type="text"
                                        tabIndex={2}
                                        autoComplete="additional-name"
                                        name="middleName"
                                        placeholder="Middle name"
                                    />
                                    <InputError message={errors.middleName} className="mt-2" />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="lastName">Last Name *</Label>
                                    <Input
                                        id="lastName"
                                        type="text"
                                        required
                                        tabIndex={3}
                                        autoComplete="family-name"
                                        name="lastName"
                                        placeholder="Last name"
                                    />
                                    <InputError message={errors.lastName} className="mt-2" />
                                </div>
                            </div>

                            {/* Student ID Field - Only show for Student role */}
                            {selectedRole === 'Student' && (
                                <div className="grid gap-2">
                                    <Label htmlFor="studentID">Student ID</Label>
                                    <Input
                                        id="studentID"
                                        type="text"
                                        tabIndex={4}
                                        name="studentID"
                                        placeholder="2023-xxxxx"
                                    />
                                    <InputError message={errors.studentID} className="mt-2" />
                                </div>
                            )}

                            {/* Faculty ID Field - Only show for Faculty role */}
                            {selectedRole === 'Faculty' && (
                                <div className="grid gap-2">
                                    <Label htmlFor="facultyID">Faculty ID *</Label>
                                    <Input
                                        id="facultyID"
                                        type="text"
                                        required
                                        tabIndex={4}
                                        name="faculty_id"
                                        placeholder="Faculty ID"
                                    />
                                    <InputError message={errors.faculty_id} className="mt-2" />
                                </div>
                            )}

                            {/* Contact Number */}
                            <div className="grid gap-2">
                                <Label htmlFor="contactNumber">Contact Number *</Label>
                                <Input
                                    id="contactNumber"
                                    type="tel"
                                    required
                                    tabIndex={5}
                                    autoComplete="tel"
                                    name="contactNumber"
                                    placeholder="09XXXXXXXXX or +63 9XXXXXXXXX"
                                />
                                <InputError message={errors.contactNumber} className="mt-2" />
                            </div>

                            {/* Email Field */}
                            <div className="grid gap-2">
                                <Label htmlFor="email">Email address *</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    required
                                    tabIndex={6}
                                    autoComplete="email"
                                    name="email"
                                    placeholder="email@usep.edu.ph"
                                />
                                <InputError message={errors.email} />
                            </div>

                            {/* Role Selection */}
                            <div className="grid gap-2">
                                <Label htmlFor="role">Role *</Label>
                                <Select 
                                    name="role" 
                                    defaultValue="Student"
                                    onValueChange={(value) => setSelectedRole(value)}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select your role" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="Student">Student</SelectItem>
                                        <SelectItem value="Faculty">Faculty</SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError message={errors.role} className="mt-2" />
                            </div>

                            {/* Password Fields */}
                            <div className="grid gap-2">
                                <Label htmlFor="password">Password *</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    required
                                    tabIndex={7}
                                    autoComplete="new-password"
                                    name="password"
                                    placeholder="Password"
                                />
                                <InputError message={errors.password} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password_confirmation">Confirm password *</Label>
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    required
                                    tabIndex={8}
                                    autoComplete="new-password"
                                    name="password_confirmation"
                                    placeholder="Confirm password"
                                />
                                <InputError message={errors.password_confirmation} />
                            </div>

                            <Button type="submit" className="mt-2 w-full" tabIndex={9}>
                                {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                                Create account
                            </Button>
                        </div>

                        <div className="text-center text-sm text-muted-foreground">
                            Already have an account?{' '}
                            <TextLink href={login()} tabIndex={10}>
                                Log in
                            </TextLink>
                        </div>
                    </>
                )}
            </Form>
        </AuthLayout>
    );
}