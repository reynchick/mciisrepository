import { Head, Link, router } from '@inertiajs/react';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Eye, Search, X } from 'lucide-react';
import { useState, useEffect } from 'react';


interface Research {
    id: number;
    research_title: string;
    publication_date: string;
    program: {
        id: number;
        name: string;
    };
    adviser: {
        id: number;
        first_name: string;
        middle_name: string;
        last_name: string;
    };
}


interface Programme {
    id: number;
    name: string;
}


interface Adviser {
    id: number;
    first_name: string;
    middle_name: string;
    last_name: string;
}


interface Props {
    researches: Research[];
    programmes: Programme[];
    advisers: Adviser[];
    filters: {
        search?: string;
        year?: string;
        adviser?: string;
        program?: string;
    };
}


export default function Home({ researches, programmes, advisers, filters }: Props) {
    const [search, setSearch] = useState(filters.search || '');
    const [selectedYear, setSelectedYear] = useState(filters.year || 'all');
    const [selectedAdviser, setSelectedAdviser] = useState(filters.adviser || 'all');
    const [selectedProgram, setSelectedProgram] = useState(filters.program || 'all');


    // Debounce search
    useEffect(() => {
        const timer = setTimeout(() => {
            handleFilter();
        }, 500);


        return () => clearTimeout(timer);
    }, [search, selectedYear, selectedAdviser, selectedProgram]);


    const handleFilter = () => {
        router.get(
            '/',
            {
                search: search || undefined,
                year: selectedYear !== 'all' ? selectedYear : undefined,
                adviser: selectedAdviser !== 'all' ? selectedAdviser : undefined,
                program: selectedProgram !== 'all' ? selectedProgram : undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    };


    const clearFilters = () => {
        setSearch('');
        setSelectedYear('all');
        setSelectedAdviser('all');
        setSelectedProgram('all');
        router.get('/', {}, { preserveState: true, preserveScroll: true });
    };


    const hasActiveFilters = search || selectedYear !== 'all' || selectedAdviser !== 'all' || selectedProgram !== 'all';
    const getAdviserName = (adviser: Adviser) => {
        let name = adviser.first_name || '';
        if (adviser.middle_name) {
            name += ` ${adviser.middle_name}`;
        }
        if (adviser.last_name) {
            name += ` ${adviser.last_name}`;
        }
        return name;
    };


    const formatDate = (date: string) => {
        return new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
    };


    return (
        <AppSidebarLayout>
            <Head title="MCIIS Repository" />


            <div className="space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-3xl font-bold tracking-tight">MCIIS Repository</h1>
                    <p className="text-muted-foreground">Browse research papers and theses</p>
                </div>


                {/* Search and Filters */}
                <Card>
                    <CardContent className="pt-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <div className="lg:col-span-2">
                                <div className="relative">
                                    <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                                    <Input
                                        placeholder="Search by title or keyword..."
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                        className="pl-9"
                                    />
                                </div>
                            </div>
                           
                            <Select value={selectedProgram} onValueChange={setSelectedProgram}>
                                <SelectTrigger>
                                    <SelectValue placeholder="All Programs" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Programs</SelectItem>
                                    {programmes.map((prog) => (
                                        <SelectItem key={prog.id} value={prog.id.toString()}>
                                            {prog.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>


                            <Select value={selectedAdviser} onValueChange={setSelectedAdviser}>
                                <SelectTrigger>
                                    <SelectValue placeholder="All Advisers" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Advisers</SelectItem>
                                    {advisers.map((adv) => (
                                        <SelectItem key={adv.id} value={adv.id.toString()}>
                                            {getAdviserName(adv)}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>


                            <Select value={selectedYear} onValueChange={setSelectedYear}>
                                <SelectTrigger>
                                    <SelectValue placeholder="All Years" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Years</SelectItem>
                                    {Array.from(new Set(researches
                                        .filter(r => r.publication_date)
                                        .map(r => r.publication_date.substring(0, 4))))
                                        .sort((a, b) => b.localeCompare(a))
                                        .map((year) => (
                                            <SelectItem key={year} value={year}>
                                                {year}
                                            </SelectItem>
                                        ))}
                                </SelectContent>
                            </Select>
                        </div>
                       
                        {hasActiveFilters && (
                            <div className="mt-4 flex items-center justify-between">
                                <p className="text-sm text-muted-foreground">
                                    Showing {researches.length} research {researches.length === 1 ? 'result' : 'results'}
                                </p>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    onClick={clearFilters}
                                    className="flex items-center gap-2"
                                >
                                    <X className="h-4 w-4" />
                                    Clear Filters
                                </Button>
                            </div>
                        )}
                    </CardContent>
                </Card>


                {/* Research List */}
                <Card>
                    <CardHeader>
                        <CardTitle>Research Papers</CardTitle>
                        <CardDescription>
                            {researches.length} {researches.length === 1 ? 'paper' : 'papers'} available
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {researches.length === 0 ? (
                            <div className="text-center py-12">
                                <p className="text-muted-foreground text-lg">No research papers found</p>
                                <p className="text-sm text-muted-foreground mt-2">Check back later for new publications</p>
                            </div>
                        ) : (
                            <div className="space-y-4">
                                {researches.map((research) => (
                                    <div
                                        key={research.id}
                                        className="flex items-start justify-between p-4 border rounded-lg hover:bg-accent/50 transition-colors"
                                    >
                                        <div className="flex-1 space-y-2">
                                            <Link
                                                href={`/research/${research.id}`}
                                                className="font-medium text-lg hover:underline line-clamp-2"
                                            >
                                                {research.research_title}
                                            </Link>
                                            <div className="flex items-center gap-2 text-sm text-muted-foreground flex-wrap">
                                                <Badge variant="secondary">{research.program.name}</Badge>
                                                <span>•</span>
                                                <span>Adviser: {getAdviserName(research.adviser)}</span>
                                                <span>•</span>
                                                <span>Published: {formatDate(research.publication_date)}</span>
                                            </div>
                                        </div>
                                        <Button asChild variant="default" size="sm" className="ml-4">
                                            <Link href={`/research/${research.id}`}>
                                                <Eye className="h-4 w-4 mr-2" />
                                                View
                                            </Link>
                                        </Button>
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