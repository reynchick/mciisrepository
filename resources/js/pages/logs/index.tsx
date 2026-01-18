import { Head, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/layouts/app/app-layout';
import Heading from '@/components/heading';
import HeadingSmall from '@/components/heading-small';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import LogFilters from '@/components/logs/log-filters';
import LogTable from '@/components/logs/log-table';
import type { LogFilterState, LogFilterOptions, LogType } from '@/components/logs/log-filters';
import type { LogTableColumn } from '@/components/logs/log-table';

interface Props {
    logs: {
        data: any[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    logType: string;
    logConfig: {
        title: string;
        description: string;
    };
    availableTypes: Array<{ value: string; label: string }>;
    filters: Record<string, any>;
    filterOptions?: LogFilterOptions;
}

export default function LogsIndex({ 
    logs, 
    logType, 
    logConfig, 
    availableTypes, 
    filters: initialFilters,
    filterOptions = {}
}: Props) {
    const page = usePage();
    const [filterState, setFilterState] = useState<LogFilterState>(initialFilters);

    // Map action types to user-friendly labels
    const getActionLabel = (actionType: string): string => {
        const actionMap: Record<string, string> = {
            // User Audit
            'create_user': 'Create User',
            'update_user': 'Update User',
            'deactivate_user': 'Deactivate User',
            // Faculty Audit
            'create_faculty': 'Create Faculty',
            'update_faculty': 'Update Faculty',
            'delete_faculty': 'Delete Faculty',
            // Research Entry
            'create_research_entry': 'Create Research Entry',
            'update_research_entry': 'Update Research Entry',
            'archive_research_entry': 'Archive Research Entry',
        };
        return actionMap[actionType] || actionType;
    };

    const handleTypeChange = (newType: string) => {
        router.get(`/logs/${newType}`);
    };

    const handleFilterChange = (newFilters: LogFilterState) => {
        setFilterState(newFilters);
    };

    const handleApplyFilters = (appliedFilters: LogFilterState) => {
        const params = new URLSearchParams();
        
        // Date filters
        if (appliedFilters.from) params.append('date_from', appliedFilters.from);
        if (appliedFilters.to) params.append('date_to', appliedFilters.to);
        
        // Action filters (user-audit, faculty-audit, research-entry)
        if (appliedFilters.actionTypes?.length) {
            appliedFilters.actionTypes.forEach(action => params.append('action', action));
        }
        
        // Modified by filter (research-entry)
        if (appliedFilters.modifiedByUserId) {
            params.append('modified_by_user_id', String(appliedFilters.modifiedByUserId));
        }
        
        // Research filter (research-access)
        if (appliedFilters.researchSearch) {
            params.append('research_search', appliedFilters.researchSearch);
        }
        
        // Keyword filter (keyword-search)
        if (appliedFilters.keywordSearch) {
            params.append('keyword_search', appliedFilters.keywordSearch);
        }

        router.get(`/logs/${logType}?${params.toString()}`);
    };

    const getTableColumns = (): LogTableColumn<any>[] => {
        switch (logType) {
            case 'user-audit':
                return [
                    { id: 'targetUser', header: 'Target User', cell: (row) => row.targetUser?.name || `ID: ${row.target_user_id}` },
                    { id: 'action_type', header: 'Action', cell: (row) => getActionLabel(row.action_type) },
                    { id: 'modifiedBy', header: 'Modified By', cell: (row) => `ID: ${row.modified_by}` },
                    { id: 'ip_address', header: 'IP Address', cell: (row) => row.ip_address || 'N/A' },
                    { id: 'created_at', header: 'Date', sortable: true, cell: (row) => new Date(row.created_at).toLocaleString() },
                ];
            case 'faculty-audit':
                return [
                    { id: 'targetFaculty', header: 'Faculty', cell: (row) => row.targetFaculty ? `${row.targetFaculty.first_name} ${row.targetFaculty.last_name}` : `ID: ${row.target_faculty_id}` },
                    { id: 'action_type', header: 'Action', cell: (row) => getActionLabel(row.action_type) },
                    { id: 'modifiedBy', header: 'Modified By', cell: (row) => `ID: ${row.modified_by}` },
                    { id: 'ip_address', header: 'IP Address', cell: (row) => row.ip_address || 'N/A' },
                    { id: 'created_at', header: 'Date', sortable: true, cell: (row) => new Date(row.created_at).toLocaleString() },
                ];
            case 'research-entry':
                return [
                    { id: 'targetResearch', header: 'Research', cell: (row) => row.targetResearch?.title || `ID: ${row.target_research_id}` },
                    { id: 'action_type', header: 'Action', cell: (row) => getActionLabel(row.action_type) },
                    { id: 'modifiedBy', header: 'Modified By', cell: (row) => `ID: ${row.modified_by}` },
                    { id: 'created_at', header: 'Date', sortable: true, cell: (row) => new Date(row.created_at).toLocaleString() },
                ];
            case 'research-access':
                return [
                    { id: 'research', header: 'Research', cell: (row) => `ID: ${row.research_id}` },
                    { id: 'user', header: 'Accessed By', cell: (row) => `ID: ${row.user_id}` },
                    { id: 'ip_address', header: 'IP Address', cell: (row) => row.ip_address || 'N/A' },
                    { id: 'created_at', header: 'Date', sortable: true, cell: (row) => new Date(row.created_at).toLocaleString() },
                ];
            case 'keyword-search':
                return [
                    { id: 'search_term', header: 'Search Term', cell: (row) => row.search_term || 'N/A' },
                    { id: 'keyword', header: 'Keyword', cell: (row) => row.keyword_id ? `ID: ${row.keyword_id}` : 'N/A' },
                    { id: 'user', header: 'Searched By', cell: (row) => row.user_id ? `ID: ${row.user_id}` : 'N/A' },
                    { id: 'ip_address', header: 'IP Address', cell: (row) => row.ip_address || 'N/A' },
                    { id: 'created_at', header: 'Date', sortable: true, cell: (row) => new Date(row.created_at).toLocaleString() },
                ];
            default:
                return [];
        }
    };

    return (
        <AppLayout>
            <Head title={logConfig.title} />
            <div className="space-y-6 p-4 sm:p-6">
                {/* Header with Log Type Selector */}
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading 
                        title="Activity Logs" 
                        description="Monitor system activities and changes" 
                    />
                    <Select value={logType} onValueChange={handleTypeChange}>
                        <SelectTrigger className="w-full sm:w-[250px]">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            {availableTypes.map((type) => (
                                <SelectItem key={type.value} value={type.value}>
                                    {type.label}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </div>

                {/* Current Log Type Info */}
                <Card>
                    <CardHeader>
                        <HeadingSmall 
                            title={logConfig.title} 
                            description={logConfig.description} 
                        />
                    </CardHeader>
                </Card>

                {/* Filters and Table Layout */}
                <div className="flex flex-col gap-6 lg:flex-row">
                    {/* Filters Sidebar */}
                    <LogFilters 
                        logType={logType as LogType}
                        value={filterState}
                        onChange={handleFilterChange}
                        onApply={handleApplyFilters}
                        options={filterOptions}
                        className="lg:sticky lg:top-6 lg:self-start"
                    />

                    {/* Logs Table */}
                    <div className="flex-1">
                        <Card>
                            <CardHeader>
                                <HeadingSmall 
                                    title="Log Entries" 
                                    description={`${logs.total} log entry(ies) found`} 
                                />
                            </CardHeader>
                            <CardContent>
                                <LogTable 
                                    data={logs.data}
                                    columns={getTableColumns()}
                                    getRowId={(row) => row.id}
                                    pagination={{
                                        meta: {
                                            current_page: logs.current_page,
                                            last_page: logs.last_page,
                                            per_page: logs.per_page,
                                            total: logs.total,
                                        },
                                        onChange: (page) => {
                                            router.get(`/logs/${logType}`, { ...filterState, page });
                                        },
                                        hrefBuilder: (page) => `/logs/${logType}?page=${page}`,
                                    }}
                                    emptyMessage="No log entries found"
                                />
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}