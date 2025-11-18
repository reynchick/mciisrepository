import { Card } from '@/components/ui/card';

interface Program { id: number; name: string }
interface Adviser { id: number; first_name?: string; last_name?: string }
interface Research {
    id: number;
    research_title: string;
    published_year?: number;
    program?: Program;
    adviser?: Adviser;
    abstract?: string;
}

interface ResearchListProps {
    researches: Research[];
    compact?: boolean;
}

export function ResearchList({ researches, compact = false }: ResearchListProps) {
    if (researches.length === 0) {
        return (
            <div className="text-center py-8 text-muted-foreground">
                No researches found
            </div>
        );
    }

    return (
        <div className="space-y-4">
            {researches.map((research) => (
                <div
                    key={research.id}
                    className="group border-b border-neutral-200 pb-4 last:border-b-0 dark:border-neutral-800 hover:bg-neutral-50/50 dark:hover:bg-neutral-900/50 px-2 py-2 rounded transition-colors"
                >
                    <div className="space-y-1">
                        {/* Title */}
                        <h3 className="text-base font-semibold text-blue-600 dark:text-blue-400 hover:underline cursor-pointer line-clamp-2">
                            {research.research_title}
                        </h3>

                        {/* Metadata */}
                        <div className="flex flex-wrap items-center gap-2 text-sm text-muted-foreground">
                            {research.adviser && (
                                <span>
                                    {research.adviser.last_name}
                                    {research.adviser.first_name && `, ${research.adviser.first_name}`}
                                </span>
                            )}
                            {research.adviser && research.program && <span>•</span>}
                            {research.program && <span>{research.program.name}</span>}
                            {(research.adviser || research.program) && research.published_year && <span>•</span>}
                            {research.published_year && <span>{research.published_year}</span>}
                        </div>

                        {/* Abstract (if not compact) */}
                        {!compact && research.abstract && (
                            <p className="text-sm text-muted-foreground line-clamp-2 mt-2">
                                {research.abstract}
                            </p>
                        )}
                    </div>
                </div>
            ))}
        </div>
    );
}