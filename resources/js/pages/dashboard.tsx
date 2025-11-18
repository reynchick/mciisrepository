import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ResearchList } from '@/components/research-list';
import { useState } from 'react';

interface Adviser {
    id: number;
    first_name?: string;
    middle_name?: string;
    last_name?: string;
}

interface Program {
    id: number;
    name: string;
}

interface Research {
    id: number;
    research_title: string;
    published_year?: number;
    program?: Program;
    adviser?: Adviser;
    research_abstract?: string;
}

interface DashboardProps {
    researches: Research[];
    programmes: Program[];
    advisers: Adviser[];
    filters: {
        year?: string;
        adviser?: string;
        program?: string;
    };
}

export default function Dashboard({ researches, programmes, advisers, filters }: DashboardProps) {
    const [selectedYear, setSelectedYear] = useState(filters.year || '');
    const [selectedAdviser, setSelectedAdviser] = useState(filters.adviser || '');
    const [selectedProgram, setSelectedProgram] = useState(filters.program || '');

    const handleFilter = () => {
        const filterParams: Record<string, string> = {};
        if (selectedYear) filterParams.year = selectedYear;
        if (selectedAdviser) filterParams.adviser = selectedAdviser;
        if (selectedProgram) filterParams.program = selectedProgram;

        router.get('/dashboard', filterParams, { preserveState: true });
    };

    const handleClearFilters = () => {
        setSelectedYear('');
        setSelectedAdviser('');
        setSelectedProgram('');
        router.get('/dashboard', {});
    };

    // Get unique years from researches
    const years = [...new Set(researches.map(r => r.published_year))].sort((a, b) => (b || 0) - (a || 0));

    return (
        <AppLayout>
            <Head title="Dashboard" />
                {/* Research Filters and List */}
                <Card>
                    <CardContent className="space-y-4">
                        {/* Filters */}
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div className="space-y-2">
                                <label className="text-sm font-medium">Year</label>
                                <Select value={selectedYear} onValueChange={setSelectedYear}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select year..." />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {years.map((year) => (
                                            <SelectItem key={year} value={year?.toString() || ''}>
                                                {year}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>

                            <div className="space-y-2">
                                <label className="text-sm font-medium">Adviser</label>
                                <Select value={selectedAdviser} onValueChange={setSelectedAdviser}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select adviser..." />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {advisers.map((adviser) => (
                                            <SelectItem key={adviser.id} value={adviser.id.toString()}>
                                                {adviser.last_name}, {adviser.first_name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>

                            <div className="space-y-2">
                                <label className="text-sm font-medium">Program</label>
                                <Select value={selectedProgram} onValueChange={setSelectedProgram}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select program..." />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {programmes.map((program) => (
                                            <SelectItem key={program.id} value={program.id.toString()}>
                                                {program.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        {/* Filter Actions */}
                        <div className="flex gap-2">
                            <Button onClick={handleFilter}>
                                Apply Filters
                            </Button>
                            {(selectedYear || selectedAdviser || selectedProgram) && (
                                <Button variant="outline" onClick={handleClearFilters}>
                                    Clear Filters
                                </Button>
                            )}
                        </div>

                        {/* Research List */}
                        <div className="mt-6 border-t pt-6">
                            {researches.length > 0 ? (
                                <ResearchList researches={researches} />
                            ) : (
                                <div className="text-center py-8 text-muted-foreground">
                                    No researches found with the selected filters
                                </div>
                            )}
                        </div>
                    </CardContent>
                </Card>
        </AppLayout>
    );
}
