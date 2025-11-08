import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import InputError from '@/components/input-error';
import AuthLayout from '@/layouts/auth-layout';
import { Head, useForm } from '@inertiajs/react';

interface User {
    first_name: string;
    last_name: string;
    full_name: string;
    email: string;
    avatar?: string;
}

export default function CompleteProfile({ user }: { user: User }) {
    const { data, setData, post, processing, errors } = useForm({
        student_id: '',
        contact_number: '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/profile/complete');
    };

    return (
        <AuthLayout title="Complete Your Profile" description="Just a few more details to get started">
            <Head title="Complete Profile" />

            <Card className="max-w-md mx-auto">
                <CardHeader>
                    <CardTitle>Welcome, {user.first_name}! 👋</CardTitle>
                    <CardDescription>
                        Complete your profile to access the system
                    </CardDescription>
                </CardHeader>
                
                <CardContent>
                    {/* Show what we already have from Google */}
                    <div className="mb-6 rounded-lg border p-4 bg-muted/50">
                        <p className="text-sm text-muted-foreground mb-2">From your Google account:</p>
                        <div className="flex items-center gap-3">
                            <Avatar>
                                <AvatarImage src={user.avatar} />
                                <AvatarFallback>{user.first_name[0]}{user.last_name[0]}</AvatarFallback>
                            </Avatar>
                            <div>
                                <p className="font-medium">{user.full_name}</p>
                                <p className="text-sm text-muted-foreground">
                                    {user.email} <Badge variant="secondary" className="ml-1">Verified ✓</Badge>
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Form for missing information */}
                    <form onSubmit={submit} className="space-y-4">
                        <div>
                            <Label htmlFor="student_id">Student ID *</Label>
                            <Input 
                                id="student_id"
                                name="student_id"
                                value={data.student_id}
                                onChange={(e) => setData('student_id', e.target.value)}
                                placeholder="2023-00800"
                                pattern="\d{4}-\d{5}"
                                required
                                autoFocus
                            />
                            <p className="text-xs text-muted-foreground mt-1">
                                Format: YYYY-NNNNN (e.g., 2023-00800)
                            </p>
                            <InputError message={errors.student_id} />
                        </div>

                        <div>
                            <Label htmlFor="contact_number">Contact Number *</Label>
                            <Input 
                                id="contact_number"
                                name="contact_number"
                                value={data.contact_number}
                                onChange={(e) => setData('contact_number', e.target.value)}
                                placeholder="09123456789"
                                required
                            />
                            <p className="text-xs text-muted-foreground mt-1">
                                Philippine mobile number (09XXXXXXXXX)
                            </p>
                            <InputError message={errors.contact_number} />
                        </div>

                        <Button type="submit" className="w-full" disabled={processing}>
                            {processing ? 'Saving...' : 'Complete Profile & Continue'}
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </AuthLayout>
    );
}