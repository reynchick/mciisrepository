import { Head } from '@inertiajs/react';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { BookOpen, Users, FileText, TrendingUp, Clock, LineChart, Building2 } from 'lucide-react';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useState } from 'react';


interface Stats {
    total_research: number;
    active_research: number;
    total_programs: number;
    total_faculty: number;
    total_users: number;
}


interface Activity {
    type: string;
    action: string;
    user: string;
    created_at: string;
}


interface TopItem {
    title?: string;
    name?: string;
    count: number;
}


interface CollegeViewData {
    program: string;
    count: number;
    sdg_percentage: number;
    srig_percentage: number;
    agenda_percentage: number;
}


interface ProgramViewData {
    year: number;
    count: number;
    sdg_percentage: number;
    srig_percentage: number;
    agenda_percentage: number;
}


interface Props {
    stats: Stats;
    recentActivities: Activity[];
    topResearch: TopItem[];
    topKeywords: TopItem[];
    collegeView: CollegeViewData[];
    programView: ProgramViewData[];
}


export default function Dashboard({ stats, recentActivities, topResearch, topKeywords, collegeView, programView }: Props) {
    const [selectedProgram, setSelectedProgram] = useState<string | null>(null);
    const [selectedYear, setSelectedYear] = useState<number | null>(null);


    const formatDate = (date: string) => {
        return new Date(date).toLocaleString('en-US', {
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };


    const getActionBadge = (action: string) => {
        if (action.includes('create')) return <Badge variant="default">Created</Badge>;
        if (action.includes('update')) return <Badge variant="secondary">Updated</Badge>;
        if (action.includes('delete') || action.includes('archive')) return <Badge variant="destructive">Deleted</Badge>;
        return <Badge variant="outline">{action}</Badge>;
    };


    return (
        <AppSidebarLayout>
            <Head title="Admin Dashboard" />


            <div className="space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-3xl font-bold tracking-tight">Dashboard</h1>
                    <p className="text-muted-foreground">System overview and analytics</p>
                </div>


                {/* Stats Cards */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Research</CardTitle>
                            <BookOpen className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.total_research}</div>
                            <p className="text-xs text-muted-foreground">{stats.active_research} active</p>
                        </CardContent>
                    </Card>


                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Programs</CardTitle>
                            <FileText className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.total_programs}</div>
                            <p className="text-xs text-muted-foreground">Academic programs</p>
                        </CardContent>
                    </Card>


                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Faculty</CardTitle>
                            <Users className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.total_faculty}</div>
                            <p className="text-xs text-muted-foreground">Faculty members</p>
                        </CardContent>
                    </Card>


                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Users</CardTitle>
                            <Users className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.total_users}</div>
                            <p className="text-xs text-muted-foreground">Registered users</p>
                        </CardContent>
                    </Card>
                </div>


                {/* College View - Research per Program with SDG/SRIG/Agenda Breakdown */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <Building2 className="h-5 w-5" />
                            College View
                        </CardTitle>
                        <CardDescription>Research distribution by program with SDG, SRIG, and Agenda breakdowns</CardDescription>
                    </CardHeader>
                    <CardContent>
                        {collegeView.length === 0 ? (
                            <div className="text-center py-8 text-muted-foreground">No data available</div>
                        ) : (
                            <div className="space-y-4">
                                {collegeView.map((item, index) => {
                                    const maxCount = Math.max(...collegeView.map((d) => d.count));
                                    const width = maxCount > 0 ? (item.count / maxCount) * 100 : 0;
                                    const isSelected = selectedProgram === item.program;


                                    return (
                                        <div key={index} className="space-y-2">
                                            <div
                                                className={`p-3 rounded-lg border transition-all cursor-pointer hover:bg-accent ${
                                                    isSelected ? 'bg-accent border-primary' : ''
                                                }`}
                                                onClick={() => setSelectedProgram(isSelected ? null : item.program)}
                                            >
                                                <div className="flex items-center justify-between text-sm mb-2">
                                                    <span className="font-medium truncate flex-1">{item.program}</span>
                                                    <span className="text-muted-foreground ml-2">
                                                        {item.count} {item.count === 1 ? 'research' : 'researches'}
                                                    </span>
                                                </div>
                                                <div className="w-full bg-secondary rounded-full h-3">
                                                    <div
                                                        className="bg-primary rounded-full h-3 transition-all"
                                                        style={{ width: `${width}%`, minWidth: width > 0 ? '8px' : '0' }}
                                                    />
                                                </div>


                                                {/* Show percentages when clicked */}
                                                {isSelected && (
                                                    <div className="mt-4 pt-4 border-t space-y-3">
                                                        <div className="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-3">
                                                            Research Distribution
                                                        </div>


                                                        {/* SDG Percentage */}
                                                        <div className="space-y-1">
                                                            <div className="flex items-center justify-between text-sm">
                                                                <span className="font-medium flex items-center gap-2">
                                                                    <div className="w-3 h-3 rounded-full bg-blue-500" />
                                                                    SDG (Sustainable Development Goals)
                                                                </span>
                                                                <span className="text-muted-foreground">{item.sdg_percentage}%</span>
                                                            </div>
                                                            <div className="w-full bg-secondary rounded-full h-2">
                                                                <div
                                                                    className="bg-blue-500 rounded-full h-2 transition-all"
                                                                    style={{ width: `${item.sdg_percentage}%` }}
                                                                />
                                                            </div>
                                                        </div>


                                                        {/* SRIG Percentage */}
                                                        <div className="space-y-1">
                                                            <div className="flex items-center justify-between text-sm">
                                                                <span className="font-medium flex items-center gap-2">
                                                                    <div className="w-3 h-3 rounded-full bg-green-500" />
                                                                    SRIG (Strategic Research Interest Groups)
                                                                </span>
                                                                <span className="text-muted-foreground">{item.srig_percentage}%</span>
                                                            </div>
                                                            <div className="w-full bg-secondary rounded-full h-2">
                                                                <div
                                                                    className="bg-green-500 rounded-full h-2 transition-all"
                                                                    style={{ width: `${item.srig_percentage}%` }}
                                                                />
                                                            </div>
                                                        </div>


                                                        {/* Agenda Percentage */}
                                                        <div className="space-y-1">
                                                            <div className="flex items-center justify-between text-sm">
                                                                <span className="font-medium flex items-center gap-2">
                                                                    <div className="w-3 h-3 rounded-full bg-purple-500" />
                                                                    Agendas
                                                                </span>
                                                                <span className="text-muted-foreground">{item.agenda_percentage}%</span>
                                                            </div>
                                                            <div className="w-full bg-secondary rounded-full h-2">
                                                                <div
                                                                    className="bg-purple-500 rounded-full h-2 transition-all"
                                                                    style={{ width: `${item.agenda_percentage}%` }}
                                                                />
                                                            </div>
                                                        </div>
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        )}
                    </CardContent>
                </Card>


                {/* Program View - Research per Year with SDG/SRIG/Agenda Breakdown */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <LineChart className="h-5 w-5" />
                            Program View
                        </CardTitle>
                        <CardDescription>Research distribution by year with SDG, SRIG, and Agenda breakdowns</CardDescription>
                    </CardHeader>
                    <CardContent>
                        {programView.length === 0 ? (
                            <div className="text-center py-8 text-muted-foreground">No data available</div>
                        ) : (
                            <div className="space-y-4">
                                {programView.map((item, index) => {
                                    const maxCount = Math.max(...programView.map((d) => d.count));
                                    const width = maxCount > 0 ? (item.count / maxCount) * 100 : 0;
                                    const isSelected = selectedYear === item.year;


                                    return (
                                        <div key={index} className="space-y-2">
                                            <div
                                                className={`p-3 rounded-lg border transition-all cursor-pointer hover:bg-accent ${
                                                    isSelected ? 'bg-accent border-primary' : ''
                                                }`}
                                                onClick={() => setSelectedYear(isSelected ? null : item.year)}
                                            >
                                                <div className="flex items-center justify-between text-sm mb-2">
                                                    <span className="font-medium truncate flex-1">{item.year}</span>
                                                    <span className="text-muted-foreground ml-2">
                                                        {item.count} {item.count === 1 ? 'research' : 'researches'}
                                                    </span>
                                                </div>
                                                <div className="w-full bg-secondary rounded-full h-3">
                                                    <div
                                                        className="bg-primary rounded-full h-3 transition-all"
                                                        style={{ width: `${width}%`, minWidth: width > 0 ? '8px' : '0' }}
                                                    />
                                                </div>


                                                {/* Show percentages when clicked */}
                                                {isSelected && (
                                                    <div className="mt-4 pt-4 border-t space-y-3">
                                                        <div className="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-3">
                                                            Research Distribution
                                                        </div>


                                                        {/* SDG Percentage */}
                                                        <div className="space-y-1">
                                                            <div className="flex items-center justify-between text-sm">
                                                                <span className="font-medium flex items-center gap-2">
                                                                    <div className="w-3 h-3 rounded-full bg-blue-500" />
                                                                    SDG (Sustainable Development Goals)
                                                                </span>
                                                                <span className="text-muted-foreground">{item.sdg_percentage}%</span>
                                                            </div>
                                                            <div className="w-full bg-secondary rounded-full h-2">
                                                                <div
                                                                    className="bg-blue-500 rounded-full h-2 transition-all"
                                                                    style={{ width: `${item.sdg_percentage}%` }}
                                                                />
                                                            </div>
                                                        </div>


                                                        {/* SRIG Percentage */}
                                                        <div className="space-y-1">
                                                            <div className="flex items-center justify-between text-sm">
                                                                <span className="font-medium flex items-center gap-2">
                                                                    <div className="w-3 h-3 rounded-full bg-green-500" />
                                                                    SRIG (Strategic Research Interest Groups)
                                                                </span>
                                                                <span className="text-muted-foreground">{item.srig_percentage}%</span>
                                                            </div>
                                                            <div className="w-full bg-secondary rounded-full h-2">
                                                                <div
                                                                    className="bg-green-500 rounded-full h-2 transition-all"
                                                                    style={{ width: `${item.srig_percentage}%` }}
                                                                />
                                                            </div>
                                                        </div>


                                                        {/* Agenda Percentage */}
                                                        <div className="space-y-1">
                                                            <div className="flex items-center justify-between text-sm">
                                                                <span className="font-medium flex items-center gap-2">
                                                                    <div className="w-3 h-3 rounded-full bg-purple-500" />
                                                                    Agendas
                                                                </span>
                                                                <span className="text-muted-foreground">{item.agenda_percentage}%</span>
                                                            </div>
                                                            <div className="w-full bg-secondary rounded-full h-2">
                                                                <div
                                                                    className="bg-purple-500 rounded-full h-2 transition-all"
                                                                    style={{ width: `${item.agenda_percentage}%` }}
                                                                />
                                                            </div>
                                                        </div>
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        )}
                    </CardContent>
                </Card>


                {/* Two Column Layout */}
                <div className="grid gap-6 md:grid-cols-2">
                    {/* Recent Activities */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Clock className="h-5 w-5" />
                                Recent Activities
                            </CardTitle>
                            <CardDescription>Latest system changes</CardDescription>
                        </CardHeader>
                        <CardContent>
                            {recentActivities.length === 0 ? (
                                <div className="text-center py-8 text-muted-foreground">No recent activities</div>
                            ) : (
                                <div className="space-y-4">
                                    {recentActivities.map((activity, index) => (
                                        <div key={index} className="flex items-start justify-between border-b pb-3 last:border-0">
                                            <div className="space-y-1">
                                                <div className="flex items-center gap-2">
                                                    {getActionBadge(activity.action)}
                                                    <Badge variant="outline">{activity.type}</Badge>
                                                </div>
                                                <p className="text-sm text-muted-foreground">{activity.user}</p>
                                            </div>
                                            <span className="text-xs text-muted-foreground whitespace-nowrap">
                                                {formatDate(activity.created_at)}
                                            </span>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </CardContent>
                    </Card>


                    {/* Top Accessed Research */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <TrendingUp className="h-5 w-5" />
                                Top Accessed Research
                            </CardTitle>
                            <CardDescription>Most viewed research papers</CardDescription>
                        </CardHeader>
                        <CardContent>
                            {topResearch.length === 0 ? (
                                <div className="text-center py-8 text-muted-foreground">No access data yet</div>
                            ) : (
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Title</TableHead>
                                            <TableHead className="text-right">Views</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {topResearch.map((item, index) => (
                                            <TableRow key={index}>
                                                <TableCell className="font-medium">
                                                    <div className="line-clamp-1">{item.title}</div>
                                                </TableCell>
                                                <TableCell className="text-right">{item.count}</TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            )}
                        </CardContent>
                    </Card>
                </div>


                {/* Top Keywords */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <TrendingUp className="h-5 w-5" />
                            Top Search Keywords
                        </CardTitle>
                        <CardDescription>Most searched keywords</CardDescription>
                    </CardHeader>
                    <CardContent>
                        {topKeywords.length === 0 ? (
                            <div className="text-center py-8 text-muted-foreground">No search data yet</div>
                        ) : (
                            <div className="flex flex-wrap gap-3">
                                {topKeywords.map((item, index) => (
                                    <div key={index} className="flex items-center gap-2 px-3 py-2 border rounded-lg">
                                        <Badge variant="secondary">{item.name}</Badge>
                                        <span className="text-sm text-muted-foreground">{item.count} searches</span>
                                    </div>
                                ))}
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppSidebarLayout>
    );
}