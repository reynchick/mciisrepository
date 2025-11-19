import { Head, usePage } from '@inertiajs/react';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Separator } from '@/components/ui/separator';
import { Mail, UserCircle, Shield } from 'lucide-react';
import { SharedData } from '@/types';


export default function Profile() {
    const { auth } = usePage<SharedData>().props;
    const user = auth.user;


    const getInitials = () => {
        const first = user.first_name?.[0] || '';
        const last = user.last_name?.[0] || '';
        return `${first}${last}`.toUpperCase();
    };


    const getFullName = () => {
        let name = user.first_name || '';
        if (user.middle_name) {
            name += ` ${user.middle_name}`;
        }
        if (user.last_name) {
            name += ` ${user.last_name}`;
        }
        return name || 'No Name';
    };


    return (
        <AppSidebarLayout>
            <Head title="My Profile" />
           
            <div className="space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-3xl font-bold tracking-tight">My Profile</h1>
                    <p className="text-muted-foreground">
                        View your account information
                    </p>
                </div>


                {/* Profile Card */}
                <Card>
                    <CardHeader>
                        <CardTitle>Profile Information</CardTitle>
                        <CardDescription>Your personal details and account information</CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-6">
                        {/* Avatar and Name */}
                        <div className="flex items-center space-x-4">
                            <Avatar className="h-20 w-20">
                                <AvatarImage src={user.avatar} alt={getFullName()} />
                                <AvatarFallback className="text-lg">{getInitials()}</AvatarFallback>
                            </Avatar>
                            <div>
                                <h2 className="text-2xl font-semibold">{getFullName()}</h2>
                                <p className="text-muted-foreground">User ID: {user.id}</p>
                            </div>
                        </div>


                        <Separator />


                        {/* Email */}
                        <div className="flex items-start space-x-3">
                            <Mail className="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div className="flex-1">
                                <p className="text-sm font-medium text-muted-foreground">Email Address</p>
                                <p className="text-base">{user.email}</p>
                            </div>
                        </div>


                        <Separator />


                        {/* Roles */}
                        <div className="flex items-start space-x-3">
                            <Shield className="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div className="flex-1">
                                <p className="text-sm font-medium text-muted-foreground mb-2">Roles</p>
                                <div className="flex flex-wrap gap-2">
                                    {user.roles && user.roles.length > 0 ? (
                                        user.roles.map((role) => (
                                            <Badge key={role.id} variant="secondary">
                                                {role.name}
                                            </Badge>
                                        ))
                                    ) : (
                                        <Badge variant="outline">No role assigned</Badge>
                                    )}
                                </div>
                            </div>
                        </div>


                        <Separator />


                        {/* Additional Info */}
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {user.student_id && (
                                <div>
                                    <p className="text-sm font-medium text-muted-foreground">Student ID</p>
                                    <p className="text-base">{user.student_id}</p>
                                </div>
                            )}
                            {user.contact_number && (
                                <div>
                                    <p className="text-sm font-medium text-muted-foreground">Contact Number</p>
                                    <p className="text-base">{user.contact_number}</p>
                                </div>
                            )}
                            <div>
                                <p className="text-sm font-medium text-muted-foreground">Account Status</p>
                                {user.email_verified_at ? (
                                    <Badge variant="default">Active</Badge>
                                ) : (
                                    <Badge variant="secondary">Inactive</Badge>
                                )}
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppSidebarLayout>
    );
}