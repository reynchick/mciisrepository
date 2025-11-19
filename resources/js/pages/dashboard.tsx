import { Head, router } from '@inertiajs/react';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { BookOpen, Users, FileText, TrendingUp, Clock, LineChart, Building2 } from 'lucide-react';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useState } from 'react';
import ProgramBarChart from '@/components/dashboard/ProgramBarChart';
import YearBarChart from '@/components/dashboard/YearBarChart';
import TopKeywords from '@/components/dashboard/TopKeywords';
import { exportNodeAsPng } from '@/lib/chart-export';


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

    const programChartData = collegeView.map((item) => ({
        program: item.program,
        count: item.count,
        topAlignments: [
            { label: 'SDG', percentage: item.sdg_percentage },
            { label: 'SRIG', percentage: item.srig_percentage },
            { label: 'Agenda', percentage: item.agenda_percentage },
        ],
    }));

    const yearChartData = programView.map((item) => ({
        year: item.year,
        count: item.count,
        topAlignments: [
            { label: 'SDG', percentage: item.sdg_percentage },
            { label: 'SRIG', percentage: item.srig_percentage },
            { label: 'Agenda', percentage: item.agenda_percentage },
        ],
    }));

    const keywordItems = topKeywords.map((item) => ({ keyword: item.name ?? '', count: item.count, trend: 'flat' as const }));

    const goProgram = (program: string) => router.get('/', { program }, { preserveState: true, preserveScroll: true });
    const goYear = (year: number) => router.get('/', { year }, { preserveState: true, preserveScroll: true });

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


<ProgramBarChart data={programChartData} onBarClick={goProgram} onExport={(node) => exportNodeAsPng(node, 'program-chart.png')} />


<YearBarChart data={yearChartData} onBarClick={goYear} onExport={(node) => exportNodeAsPng(node, 'year-chart.png')} />


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
                            <TopKeywords items={keywordItems} />
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppSidebarLayout>
    );
}