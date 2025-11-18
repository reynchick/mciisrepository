import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import InputError from '@/components/input-error';
import AuthLayout from '@/layouts/auth-layout';
import { Head, useForm } from '@inertiajs/react';

interface User {
    first_name: string;
    middle_name?: string;
    last_name: string;
    full_name: string;
    contact_number?: string;
    email: string;
    avatar?: string;
}

interface Faculty {
    id: number;
    faculty_id: string;
    first_name: string;
    middle_name?: string;
    last_name: string;
    position?: string;
    designation?: string;
    contact_number?: string;
    educational_attainment?: string;
    field_of_specialization?: string;
    research_interest?: string;
    orcid?: string;
}

interface Props {
    user: User;
    faculty: Faculty;
}

export default function CompleteFacultyProfile({ user, faculty }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        first_name: user.first_name || '',
        middle_name: user.middle_name || '',
        last_name: user.last_name || '',
        position: faculty.position || '',
        designation: faculty.designation || '',
        contact_number: user.contact_number || faculty.contact_number || '',
        educational_attainment: faculty.educational_attainment || '',
        field_of_specialization: faculty.field_of_specialization || '',
        research_interest: faculty.research_interest || '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/faculty/profile/complete');
    };

    return (
        <AuthLayout title="Complete Your Faculty Profile" description="Please provide your complete information">
            <Head title="Complete Faculty Profile" />

            <Card className="max-w-2xl mx-auto">
                <CardHeader>
                    <CardTitle>Welcome, {user.first_name}! 👋</CardTitle>
                    <CardDescription>
                        Complete your faculty profile to access the system
                    </CardDescription>
                </CardHeader>
                
                <CardContent>
                    {/* Show read-only information */}
                    <div className="mb-6 rounded-lg border p-4 bg-muted/50">
                        <p className="text-sm text-muted-foreground mb-3">Your Faculty Information (Read-only):</p>
                        <div className="flex items-center gap-3 mb-4">
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
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            <div>
                                <span className="text-muted-foreground">Faculty ID:</span>
                                <span className="ml-2 font-mono font-medium">{faculty.faculty_id}</span>
                            </div>
                            {faculty.orcid && (
                                <div>
                                    <span className="text-muted-foreground">ORCID:</span>
                                    <span className="ml-2 font-mono font-medium">{faculty.orcid}</span>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Form for faculty information */}
                    <form onSubmit={submit} className="space-y-6">
                        {/* Name section */}
                        <div className="space-y-4">
                            <p className="text-sm font-medium text-muted-foreground">
                                Please verify and correct your name if needed:
                            </p>
                            
                            <div className="space-y-4">
                                <div>
                                    <Label htmlFor="first_name">First Name *</Label>
                                    <Input 
                                        id="first_name"
                                        name="first_name"
                                        value={data.first_name}
                                        onChange={(e) => setData('first_name', e.target.value)}
                                        required
                                    />
                                    <InputError message={errors.first_name} />
                                </div>

                                <div>
                                    <Label htmlFor="middle_name">Middle Name</Label>
                                    <Input 
                                        id="middle_name"
                                        name="middle_name"
                                        value={data.middle_name}
                                        onChange={(e) => setData('middle_name', e.target.value)}
                                    />
                                    <InputError message={errors.middle_name} />
                                </div>

                                <div>
                                    <Label htmlFor="last_name">Last Name *</Label>
                                    <Input 
                                        id="last_name"
                                        name="last_name"
                                        value={data.last_name}
                                        onChange={(e) => setData('last_name', e.target.value)}
                                        required
                                    />
                                    <InputError message={errors.last_name} />
                                </div>
                            </div>
                        </div>

                        <div className="border-t pt-4">
                            <p className="text-sm font-medium text-muted-foreground mb-4">
                                Required Information:
                            </p>
                        </div>

                        {/* Required fields */}
                        <div className="space-y-4">
                            <div>
                                <Label htmlFor="position">Position *</Label>
                                <Input 
                                    id="position"
                                    name="position"
                                    value={data.position}
                                    onChange={(e) => setData('position', e.target.value)}
                                    placeholder="e.g., Assistant Professor, Associate Professor"
                                    required
                                    autoFocus
                                />
                                <InputError message={errors.position} />
                            </div>

                            <div>
                                <Label htmlFor="designation">Designation *</Label>
                                <Input 
                                    id="designation"
                                    name="designation"
                                    value={data.designation}
                                    onChange={(e) => setData('designation', e.target.value)}
                                    placeholder="e.g., Department Chair, Program Coordinator"
                                    required
                                />
                                <InputError message={errors.designation} />
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
                        </div>

                        <div className="border-t pt-4">
                            <p className="text-sm font-medium text-muted-foreground mb-4">
                                Optional Information:
                            </p>
                        </div>

                        {/* Optional fields */}
                        <div className="space-y-4">
                            <div>
                                <Label htmlFor="educational_attainment">Educational Attainment</Label>
                                <Input 
                                    id="educational_attainment"
                                    name="educational_attainment"
                                    value={data.educational_attainment}
                                    onChange={(e) => setData('educational_attainment', e.target.value)}
                                    placeholder="e.g., PhD in Computer Science"
                                />
                                <InputError message={errors.educational_attainment} />
                            </div>

                            <div>
                                <Label htmlFor="field_of_specialization">Field of Specialization</Label>
                                <Textarea 
                                    id="field_of_specialization"
                                    name="field_of_specialization"
                                    value={data.field_of_specialization}
                                    onChange={(e) => setData('field_of_specialization', e.target.value)}
                                    placeholder="e.g., Machine Learning, Data Science"
                                    rows={3}
                                />
                                <InputError message={errors.field_of_specialization} />
                            </div>

                            <div>
                                <Label htmlFor="research_interest">Research Interest</Label>
                                <Textarea 
                                    id="research_interest"
                                    name="research_interest"
                                    value={data.research_interest}
                                    onChange={(e) => setData('research_interest', e.target.value)}
                                    placeholder="e.g., Artificial Intelligence, Natural Language Processing"
                                    rows={3}
                                />
                                <InputError message={errors.research_interest} />
                            </div>
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