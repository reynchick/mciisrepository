import { Head, useForm } from '@inertiajs/react';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Textarea } from '@/components/ui/textarea';
import { ArrowLeft, Save, User, Edit3 } from 'lucide-react';
import { Link } from '@inertiajs/react';

interface Faculty {
    id: number;
    facultyID: string;
    firstName: string;
    middleName?: string;
    lastName: string;
    position?: string;
    designation?: string;
    email?: string;
    ORCID?: string;
    contactNumber?: string;
    educationalAttainment?: string;
    fieldOfSpecialization?: string;
    researchInterest?: string;
}

interface Props {
    faculty: Faculty;
}

export default function FacultyEdit({ faculty }: Props) {
    const { data, setData, put, processing, errors } = useForm({
        facultyID: faculty.facultyID,
        firstName: faculty.firstName,
        middleName: faculty.middleName || '',
        lastName: faculty.lastName,
        position: faculty.position || '',
        designation: faculty.designation || '',
        email: faculty.email || '',
        ORCID: faculty.ORCID || '',
        contactNumber: faculty.contactNumber || '',
        educationalAttainment: faculty.educationalAttainment || '',
        fieldOfSpecialization: faculty.fieldOfSpecialization || '',
        researchInterest: faculty.researchInterest || '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(`/faculties/${faculty.id}`);
    };

    const getFullName = () => {
        let name = faculty.firstName;
        if (faculty.middleName) {
            name += ` ${faculty.middleName}`;
        }
        name += ` ${faculty.lastName}`;
        return name;
    };

    return (
        <AppSidebarLayout>
            <Head title={`Edit ${getFullName()} - Faculty`} />
            
            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-4">
                        <Button variant="outline" size="sm" asChild>
                            <Link href={`/faculties/${faculty.id}`}>
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back to Faculty
                            </Link>
                        </Button>
                        <div>
                            <h1 className="text-3xl font-bold tracking-tight">Edit Faculty Member</h1>
                            <p className="text-muted-foreground">
                                Update information for {getFullName()}
                            </p>
                        </div>
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    {/* Basic Information */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center">
                                <User className="mr-2 h-5 w-5" />
                                Basic Information
                            </CardTitle>
                            <CardDescription>
                                Update the basic details of the faculty member
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div className="space-y-2">
                                    <Label htmlFor="facultyID">Faculty ID *</Label>
                                    <Input
                                        id="facultyID"
                                        value={data.facultyID}
                                        onChange={(e) => setData('facultyID', e.target.value)}
                                        placeholder="e.g., F2024-001"
                                        className={errors.facultyID ? 'border-red-500' : ''}
                                    />
                                    {errors.facultyID && (
                                        <p className="text-sm text-red-500">{errors.facultyID}</p>
                                    )}
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="position">Position</Label>
                                    <Input
                                        id="position"
                                        value={data.position}
                                        onChange={(e) => setData('position', e.target.value)}
                                        placeholder="e.g., Professor, Instructor, Lecturer"
                                        className={errors.position ? 'border-red-500' : ''}
                                    />
                                    {errors.position && (
                                        <p className="text-sm text-red-500">{errors.position}</p>
                                    )}
                                </div>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div className="space-y-2">
                                    <Label htmlFor="firstName">First Name *</Label>
                                    <Input
                                        id="firstName"
                                        value={data.firstName}
                                        onChange={(e) => setData('firstName', e.target.value)}
                                        placeholder="First name"
                                        className={errors.firstName ? 'border-red-500' : ''}
                                    />
                                    {errors.firstName && (
                                        <p className="text-sm text-red-500">{errors.firstName}</p>
                                    )}
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="middleName">Middle Name</Label>
                                    <Input
                                        id="middleName"
                                        value={data.middleName}
                                        onChange={(e) => setData('middleName', e.target.value)}
                                        placeholder="Middle name (optional)"
                                    />
                                    {errors.middleName && (
                                        <p className="text-sm text-red-500">{errors.middleName}</p>
                                    )}
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="lastName">Last Name *</Label>
                                    <Input
                                        id="lastName"
                                        value={data.lastName}
                                        onChange={(e) => setData('lastName', e.target.value)}
                                        placeholder="Last name"
                                        className={errors.lastName ? 'border-red-500' : ''}
                                    />
                                    {errors.lastName && (
                                        <p className="text-sm text-red-500">{errors.lastName}</p>
                                    )}
                                </div>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div className="space-y-2">
                                    <Label htmlFor="designation">Designation</Label>
                                    <Input
                                        id="designation"
                                        value={data.designation}
                                        onChange={(e) => setData('designation', e.target.value)}
                                        placeholder="e.g., Department Head, Program Coordinator"
                                        className={errors.designation ? 'border-red-500' : ''}
                                    />
                                    {errors.designation && (
                                        <p className="text-sm text-red-500">{errors.designation}</p>
                                    )}
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="educationalAttainment">Educational Attainment</Label>
                                    <Input
                                        id="educationalAttainment"
                                        value={data.educationalAttainment}
                                        onChange={(e) => setData('educationalAttainment', e.target.value)}
                                        placeholder="e.g., PhD, Master of Science, Bachelor of Arts"
                                        className={errors.educationalAttainment ? 'border-red-500' : ''}
                                    />
                                    {errors.educationalAttainment && (
                                        <p className="text-sm text-red-500">{errors.educationalAttainment}</p>
                                    )}
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Contact Information */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Contact Information</CardTitle>
                            <CardDescription>
                                Update contact details and professional identifiers
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div className="space-y-2">
                                    <Label htmlFor="email">Email Address</Label>
                                    <Input
                                        id="email"
                                        type="email"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        placeholder="faculty@usep.edu.ph"
                                        className={errors.email ? 'border-red-500' : ''}
                                    />
                                    {errors.email && (
                                        <p className="text-sm text-red-500">{errors.email}</p>
                                    )}
                                    <p className="text-xs text-muted-foreground">
                                        Must be a valid USeP email address
                                    </p>
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="contactNumber">Contact Number</Label>
                                    <Input
                                        id="contactNumber"
                                        value={data.contactNumber}
                                        onChange={(e) => setData('contactNumber', e.target.value)}
                                        placeholder="09XXXXXXXXX or +63 9XXXXXXXXX"
                                        className={errors.contactNumber ? 'border-red-500' : ''}
                                    />
                                    {errors.contactNumber && (
                                        <p className="text-sm text-red-500">{errors.contactNumber}</p>
                                    )}
                                </div>
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="ORCID">ORCID ID</Label>
                                <Input
                                    id="ORCID"
                                    value={data.ORCID}
                                    onChange={(e) => setData('ORCID', e.target.value)}
                                    placeholder="0000-0000-0000-0000"
                                    className={errors.ORCID ? 'border-red-500' : ''}
                                />
                                {errors.ORCID && (
                                    <p className="text-sm text-red-500">{errors.ORCID}</p>
                                )}
                                <p className="text-xs text-muted-foreground">
                                    Your unique ORCID identifier for research activities
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Academic Information */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Academic Information</CardTitle>
                            <CardDescription>
                                Update specialization and research interests
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="space-y-2">
                                <Label htmlFor="fieldOfSpecialization">Field of Specialization</Label>
                                <Input
                                    id="fieldOfSpecialization"
                                    value={data.fieldOfSpecialization}
                                    onChange={(e) => setData('fieldOfSpecialization', e.target.value)}
                                    placeholder="e.g., Computer Science, Information Technology"
                                    className={errors.fieldOfSpecialization ? 'border-red-500' : ''}
                                />
                                {errors.fieldOfSpecialization && (
                                    <p className="text-sm text-red-500">{errors.fieldOfSpecialization}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="researchInterest">Research Interests</Label>
                                <Textarea
                                    id="researchInterest"
                                    value={data.researchInterest}
                                    onChange={(e) => setData('researchInterest', e.target.value)}
                                    placeholder="Describe your research interests and areas of expertise..."
                                    rows={4}
                                    className={errors.researchInterest ? 'border-red-500' : ''}
                                />
                                {errors.researchInterest && (
                                    <p className="text-sm text-red-500">{errors.researchInterest}</p>
                                )}
                            </div>
                        </CardContent>
                    </Card>

                    {/* Form Actions */}
                    <div className="flex items-center justify-end space-x-4">
                        <Button variant="outline" asChild>
                            <Link href={`/faculties/${faculty.id}`}>Cancel</Link>
                        </Button>
                        <Button type="submit" disabled={processing}>
                            <Save className="mr-2 h-4 w-4" />
                            {processing ? 'Updating...' : 'Update Faculty Member'}
                        </Button>
                    </div>
                </form>
            </div>
        </AppSidebarLayout>
    );
}
