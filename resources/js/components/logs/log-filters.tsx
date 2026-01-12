import { useEffect, useMemo, useState } from 'react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Checkbox } from '@/components/ui/checkbox'
import { Badge } from '@/components/ui/badge'
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible'
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet'
import { cn } from '@/lib/utils'
import { useIsMobile } from '@/hooks/use-mobile'
import { Filter, SlidersHorizontal, X, Save, Clock, CalendarRange, Users, Tag } from 'lucide-react'

type LogType = 'user_audit' | 'faculty_audit' | 'research_entry' | 'research_access' | 'keyword_search'

type Option = { value: string | number; label: string }

type FilterState = {
  datePreset?: 'today' | 'yesterday' | 'last7' | 'last30' | 'thisMonth' | 'lastMonth' | 'custom'
  from?: string
  to?: string
  timeOfDay?: 'morning' | 'afternoon' | 'evening' | 'night'
  quickTime?: 'lastHour' | 'last24h' | 'lastWeek'
  actionTypes?: string[]
  modifiedByUserId?: number
  performedByUserId?: number
  targetUserId?: number
  accessedByUserId?: number
  role?: 'Administrator' | 'MCIIS Staff' | 'Faculty'
  targetFacultyId?: number
  researchId?: number
  researchProgramId?: number
  year?: number
  adviserId?: number
  keywords?: Array<number | string>
  keywordFrequency?: 'most' | 'least'
  searchContext?: string
  affectedEntities?: {
    researchers?: boolean
    keywords?: boolean
    panelists?: boolean
    themes?: boolean
    basicInfo?: boolean
  }
  highUserFrequency?: boolean
  batchOps?: boolean
  accessPattern?: 'first' | 'repeat' | 'spike'
}

type FilterOptions = {
  users?: Option[]
  roles?: Option[]
  faculties?: Option[]
  departments?: Option[]
  positions?: Option[]
  researches?: Option[]
  programs?: Option[]
  years?: Option[]
  advisers?: Option[]
  keywords?: Option[]
}

type Props = {
  logType: LogType
  value: FilterState
  onChange: (next: FilterState) => void
  onApply?: (next: FilterState) => void
  options?: FilterOptions
  className?: string
  autoApply?: boolean
  debounceMs?: number
}

function setField<T extends keyof FilterState>(value: FilterState, field: T, v: FilterState[T]) {
  return { ...value, [field]: v }
}

function removeField(value: FilterState, field: keyof FilterState) {
  const next = { ...value }
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  ;(next as any)[field] = undefined
  return next
}

function usePresets(logType: LogType) {
  const key = `log-filter-presets:${logType}`
  const [presets, setPresets] = useState<Array<{ name: string; state: FilterState }>>([])
  useEffect(() => {
    try {
      const raw = localStorage.getItem(key)
      setPresets(raw ? JSON.parse(raw) : [])
    } catch {
      setPresets([])
    }
  }, [key])
  const save = (name: string, state: FilterState) => {
    const next = [...presets.filter((p) => p.name !== name), { name, state }]
    setPresets(next)
    try { localStorage.setItem(key, JSON.stringify(next)) } catch {}
  }
  return { presets, save }
}

function activeChips(logType: LogType, value: FilterState, options?: FilterOptions) {
  const chips: Array<{ key: keyof FilterState; label: string; valueLabel?: string }> = []
  if (value.datePreset && value.datePreset !== 'custom') chips.push({ key: 'datePreset', label: 'Date', valueLabel: value.datePreset })
  if (value.from || value.to) chips.push({ key: 'from', label: 'From', valueLabel: value.from }, { key: 'to', label: 'To', valueLabel: value.to })
  if (value.quickTime) chips.push({ key: 'quickTime', label: 'Quick', valueLabel: value.quickTime })
  if (value.timeOfDay) chips.push({ key: 'timeOfDay', label: 'Time', valueLabel: value.timeOfDay })
  if (value.actionTypes && value.actionTypes.length) chips.push({ key: 'actionTypes', label: 'Action', valueLabel: value.actionTypes.join(', ') })
  const optLabel = (opts?: Option[], id?: string | number) => opts?.find((o) => o.value === id)?.label
  if (value.modifiedByUserId) chips.push({ key: 'modifiedByUserId', label: 'Modified By', valueLabel: optLabel(options?.users, value.modifiedByUserId) })
  if (value.performedByUserId) chips.push({ key: 'performedByUserId', label: 'Performed By', valueLabel: optLabel(options?.users, value.performedByUserId) })
  if (value.targetUserId) chips.push({ key: 'targetUserId', label: 'Target User', valueLabel: optLabel(options?.users, value.targetUserId) })
  if (value.accessedByUserId) chips.push({ key: 'accessedByUserId', label: 'Accessed By', valueLabel: optLabel(options?.users, value.accessedByUserId) })
  if (value.role) chips.push({ key: 'role', label: 'Role', valueLabel: value.role })
  if (value.targetFacultyId) chips.push({ key: 'targetFacultyId', label: 'Faculty', valueLabel: optLabel(options?.faculties, value.targetFacultyId) })
  if (value.researchId) chips.push({ key: 'researchId', label: 'Research', valueLabel: optLabel(options?.researches, value.researchId) })
  if (value.researchProgramId) chips.push({ key: 'researchProgramId', label: 'Program', valueLabel: optLabel(options?.programs, value.researchProgramId) })
  if (value.year) chips.push({ key: 'year', label: 'Year', valueLabel: String(value.year) })
  if (value.adviserId) chips.push({ key: 'adviserId', label: 'Adviser', valueLabel: optLabel(options?.advisers, value.adviserId) })
  if (value.keywords && value.keywords.length) chips.push({ key: 'keywords', label: 'Keywords', valueLabel: String(value.keywords.length) })
  if (value.keywordFrequency) chips.push({ key: 'keywordFrequency', label: 'Keyword Freq', valueLabel: value.keywordFrequency })
  if (value.searchContext) chips.push({ key: 'searchContext', label: 'Context', valueLabel: value.searchContext })
  if (value.affectedEntities && Object.values(value.affectedEntities).some(Boolean)) chips.push({ key: 'affectedEntities', label: 'Entities', valueLabel: 'custom' })
  if (value.highUserFrequency) chips.push({ key: 'highUserFrequency', label: 'Unusual Activity' })
  if (value.batchOps) chips.push({ key: 'batchOps', label: 'Batch Ops' })
  if (value.accessPattern) chips.push({ key: 'accessPattern', label: 'Access', valueLabel: value.accessPattern })
  return chips
}

export default function LogFilters({ logType, value, onChange, onApply, options, className, autoApply = false, debounceMs = 300 }: Props) {
  const isMobile = useIsMobile()
  const { presets, save } = usePresets(logType)
  const chips = useMemo(() => activeChips(logType, value, options), [logType, value, options])
  const [open, setOpen] = useState(false)
  const [presetName, setPresetName] = useState('')

  const set = (field: keyof FilterState, v: unknown) => onChange(setField(value, field as any, v as any))
  const clearAll = () => {
    onChange({})
  }

  useEffect(() => {
    if (!autoApply || !onApply) return
    const id = setTimeout(() => onApply(value), debounceMs)
    return () => clearTimeout(id)
  }, [autoApply, debounceMs, value, onApply])

  const actionOptions = useMemo(() => {
    switch (logType) {
      case 'user_audit':
      case 'faculty_audit':
        return [
          { value: 'create', label: 'Create' },
          { value: 'update', label: 'Update' },
          { value: 'delete', label: 'Delete' },
        ]
      case 'research_entry':
        return [
          { value: 'create', label: 'Create' },
          { value: 'modify', label: 'Modify' },
        ]
      default:
        return []
    }
  }, [logType])

  const renderFilters = () => (
    <div className="flex h-full flex-col gap-3 p-3">
      <div className="flex items-center justify-between">
        <div className="text-sm text-muted-foreground">{chips.length} filters applied</div>
        <div className="flex items-center gap-2">
          <Select onValueChange={(v) => {
            const p = presets.find((x) => x.name === v)
            if (p) onChange({ ...p.state })
          }}>
            <SelectTrigger className="h-8 w-[180px]"><SelectValue placeholder="Load preset" /></SelectTrigger>
            <SelectContent>
              {presets.map((p) => (<SelectItem key={p.name} value={p.name}>{p.name}</SelectItem>))}
            </SelectContent>
          </Select>
          <Input value={presetName} onChange={(e) => setPresetName(e.target.value)} placeholder="Preset name" className="h-8 w-[160px]" aria-label="Preset name" />
          <Button size="sm" variant="outline" onClick={() => presetName && save(presetName, value)} aria-label="Save preset">
            <Save className="mr-1 size-4" /> Save
          </Button>
          <Button size="sm" variant="ghost" onClick={clearAll} aria-label="Clear all filters">
            <X className="mr-1 size-4" /> Clear all
          </Button>
        </div>
      </div>

      <div className="flex flex-wrap gap-2">
        {chips.map((c, i) => (
          <Badge key={`${String(c.key)}-${i}`} variant="secondary" className="flex items-center gap-2">
            <span>{c.label}{c.valueLabel ? `: ${c.valueLabel}` : ''}</span>
            <button className="rounded px-1 hover:bg-muted" aria-label={`Remove ${c.label}`} onClick={() => onChange(removeField(value, c.key))}>
              <X className="size-3" />
            </button>
          </Badge>
        ))}
      </div>

      <Collapsible defaultOpen>
        <CollapsibleTrigger className="flex w-full items-center justify-between rounded-md border p-2">
          <span className="flex items-center gap-2"><CalendarRange className="size-4" /> Time-Based</span>
          <SlidersHorizontal className="size-4" />
        </CollapsibleTrigger>
        <CollapsibleContent className="p-2">
          <div className="grid grid-cols-1 gap-2 sm:grid-cols-2">
            <Select value={value.datePreset} onValueChange={(v) => set('datePreset', v)}>
              <SelectTrigger className="h-8"><SelectValue placeholder="Preset" /></SelectTrigger>
              <SelectContent>
                {['today','yesterday','last7','last30','thisMonth','lastMonth','custom'].map((p) => (
                  <SelectItem key={p} value={p}>{p}</SelectItem>
                ))}
              </SelectContent>
            </Select>
            <Select value={value.quickTime} onValueChange={(v) => set('quickTime', v)}>
              <SelectTrigger className="h-8"><SelectValue placeholder="Quick" /></SelectTrigger>
              <SelectContent>
                <SelectItem value="lastHour">Last hour</SelectItem>
                <SelectItem value="last24h">Last 24 hours</SelectItem>
                <SelectItem value="lastWeek">Last week</SelectItem>
              </SelectContent>
            </Select>
            <div className="flex items-center gap-2">
              <Input type="date" value={value.from ?? ''} onChange={(e) => set('from', e.target.value)} aria-label="From date" />
              <Input type="date" value={value.to ?? ''} onChange={(e) => set('to', e.target.value)} aria-label="To date" />
            </div>
            <Select value={value.timeOfDay} onValueChange={(v) => set('timeOfDay', v)}>
              <SelectTrigger className="h-8"><SelectValue placeholder="Time of day" /></SelectTrigger>
              <SelectContent>
                <SelectItem value="morning">Morning (6-12)</SelectItem>
                <SelectItem value="afternoon">Afternoon (12-6)</SelectItem>
                <SelectItem value="evening">Evening (6-12)</SelectItem>
                <SelectItem value="night">Night (12-6)</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </CollapsibleContent>
      </Collapsible>

      {(logType === 'user_audit' || logType === 'faculty_audit' || logType === 'research_entry') && (
        <Collapsible defaultOpen>
          <CollapsibleTrigger className="flex w-full items-center justify-between rounded-md border p-2">
            <span className="flex items-center gap-2"><Clock className="size-4" /> Action-Based</span>
            <SlidersHorizontal className="size-4" />
          </CollapsibleTrigger>
          <CollapsibleContent className="p-2">
            <div className="grid grid-cols-2 gap-2">
              {actionOptions.map((opt) => (
                <label key={String(opt.value)} className="flex items-center gap-2 text-sm">
                  <Checkbox
                    checked={!!value.actionTypes?.includes(String(opt.value))}
                    onCheckedChange={(v) => {
                      const curr = new Set(value.actionTypes ?? [])
                      if (v) curr.add(String(opt.value))
                      else curr.delete(String(opt.value))
                      set('actionTypes', Array.from(curr))
                    }}
                  />
                  {opt.label}
                </label>
              ))}
            </div>
            {logType === 'research_entry' && (
              <div className="mt-3 grid grid-cols-1 gap-2">
                <label className="flex items-center gap-2 text-sm">
                  <Checkbox checked={!!value.affectedEntities?.basicInfo} onCheckedChange={(v) => set('affectedEntities', { ...value.affectedEntities, basicInfo: !!v })} />
                  Only changes to research metadata
                </label>
                <label className="flex items-center gap-2 text-sm">
                  <Checkbox checked={!!value.affectedEntities?.keywords} onCheckedChange={(v) => set('affectedEntities', { ...value.affectedEntities, keywords: !!v })} />
                  Include entity changes (keywords)
                </label>
                <div className="grid grid-cols-2 gap-2">
                  <label className="flex items-center gap-2 text-sm">
                    <Checkbox checked={!!value.affectedEntities?.researchers} onCheckedChange={(v) => set('affectedEntities', { ...value.affectedEntities, researchers: !!v })} />
                    Changes to researchers
                  </label>
                  <label className="flex items-center gap-2 text-sm">
                    <Checkbox checked={!!value.affectedEntities?.panelists} onCheckedChange={(v) => set('affectedEntities', { ...value.affectedEntities, panelists: !!v })} />
                    Changes to panelists
                  </label>
                  <label className="flex items-center gap-2 text-sm">
                    <Checkbox checked={!!value.affectedEntities?.themes} onCheckedChange={(v) => set('affectedEntities', { ...value.affectedEntities, themes: !!v })} />
                    Changes to themes (Agenda, SDG, SRIG)
                  </label>
                </div>
              </div>
            )}
          </CollapsibleContent>
        </Collapsible>
      )}

      <Collapsible defaultOpen>
        <CollapsibleTrigger className="flex w-full items-center justify-between rounded-md border p-2">
          <span className="flex items-center gap-2"><Users className="size-4" /> Users & Roles</span>
          <SlidersHorizontal className="size-4" />
        </CollapsibleTrigger>
        <CollapsibleContent className="p-2">
          <div className="grid grid-cols-1 gap-2 sm:grid-cols-2">
            {(logType === 'user_audit' || logType === 'faculty_audit') && (
              <Select value={value.modifiedByUserId ? String(value.modifiedByUserId) : undefined} onValueChange={(v) => set('modifiedByUserId', Number(v))}>
                <SelectTrigger className="h-8"><SelectValue placeholder="Modified By" /></SelectTrigger>
                <SelectContent>
                  {options?.users?.map((u) => (<SelectItem key={String(u.value)} value={String(u.value)}>{u.label}</SelectItem>))}
                </SelectContent>
              </Select>
            )}
            {logType === 'research_entry' && (
              <Select value={value.performedByUserId ? String(value.performedByUserId) : undefined} onValueChange={(v) => set('performedByUserId', Number(v))}>
                <SelectTrigger className="h-8"><SelectValue placeholder="Performed By" /></SelectTrigger>
                <SelectContent>
                  {options?.users?.map((u) => (<SelectItem key={String(u.value)} value={String(u.value)}>{u.label}</SelectItem>))}
                </SelectContent>
              </Select>
            )}
            {logType === 'user_audit' && (
              <Select value={value.targetUserId ? String(value.targetUserId) : undefined} onValueChange={(v) => set('targetUserId', Number(v))}>
                <SelectTrigger className="h-8"><SelectValue placeholder="Target User" /></SelectTrigger>
                <SelectContent>
                  {options?.users?.map((u) => (<SelectItem key={String(u.value)} value={String(u.value)}>{u.label}</SelectItem>))}
                </SelectContent>
              </Select>
            )}
            {logType === 'research_access' && (
              <Select value={value.accessedByUserId ? String(value.accessedByUserId) : undefined} onValueChange={(v) => set('accessedByUserId', Number(v))}>
                <SelectTrigger className="h-8"><SelectValue placeholder="Accessed By" /></SelectTrigger>
                <SelectContent>
                  {options?.users?.map((u) => (<SelectItem key={String(u.value)} value={String(u.value)}>{u.label}</SelectItem>))}
                </SelectContent>
              </Select>
            )}
            <Select value={value.role} onValueChange={(v) => set('role', v)}>
              <SelectTrigger className="h-8"><SelectValue placeholder="Role" /></SelectTrigger>
              <SelectContent>
                {['Administrator', 'MCIIS Staff', 'Faculty'].map((r) => (<SelectItem key={r} value={r}>{r}</SelectItem>))}
              </SelectContent>
            </Select>
          </div>
        </CollapsibleContent>
      </Collapsible>

      {(logType === 'faculty_audit' || logType === 'research_entry' || logType === 'research_access') && (
        <Collapsible defaultOpen>
          <CollapsibleTrigger className="flex w-full items-center justify-between rounded-md border p-2">
            <span className="flex items-center gap-2"><Filter className="size-4" /> Entities</span>
            <SlidersHorizontal className="size-4" />
          </CollapsibleTrigger>
          <CollapsibleContent className="p-2">
            <div className="grid grid-cols-1 gap-2 sm:grid-cols-2">
              {logType === 'faculty_audit' && (
                <Select value={value.targetFacultyId ? String(value.targetFacultyId) : undefined} onValueChange={(v) => set('targetFacultyId', Number(v))}>
                  <SelectTrigger className="h-8"><SelectValue placeholder="Target Faculty" /></SelectTrigger>
                  <SelectContent>
                    {options?.faculties?.map((f) => (<SelectItem key={String(f.value)} value={String(f.value)}>{f.label}</SelectItem>))}
                  </SelectContent>
                </Select>
              )}
              {(logType === 'research_entry' || logType === 'research_access') && (
                <Select value={value.researchId ? String(value.researchId) : undefined} onValueChange={(v) => set('researchId', Number(v))}>
                  <SelectTrigger className="h-8"><SelectValue placeholder="Research" /></SelectTrigger>
                  <SelectContent>
                    {options?.researches?.map((r) => (<SelectItem key={String(r.value)} value={String(r.value)}>{r.label}</SelectItem>))}
                  </SelectContent>
                </Select>
              )}
              {(logType === 'research_entry' || logType === 'research_access') && (
                <Select value={value.researchProgramId ? String(value.researchProgramId) : undefined} onValueChange={(v) => set('researchProgramId', Number(v))}>
                  <SelectTrigger className="h-8"><SelectValue placeholder="Program" /></SelectTrigger>
                  <SelectContent>
                    {options?.programs?.map((p) => (<SelectItem key={String(p.value)} value={String(p.value)}>{p.label}</SelectItem>))}
                  </SelectContent>
                </Select>
              )}
              {(logType === 'research_entry' || logType === 'research_access') && (
                <Select value={value.year ? String(value.year) : undefined} onValueChange={(v) => set('year', Number(v))}>
                  <SelectTrigger className="h-8"><SelectValue placeholder="Year" /></SelectTrigger>
                  <SelectContent>
                    {options?.years?.map((y) => (<SelectItem key={String(y.value)} value={String(y.value)}>{y.label}</SelectItem>))}
                  </SelectContent>
                </Select>
              )}
              {(logType === 'research_entry' || logType === 'research_access') && (
                <Select value={value.adviserId ? String(value.adviserId) : undefined} onValueChange={(v) => set('adviserId', Number(v))}>
                  <SelectTrigger className="h-8"><SelectValue placeholder="Adviser" /></SelectTrigger>
                  <SelectContent>
                    {options?.advisers?.map((a) => (<SelectItem key={String(a.value)} value={String(a.value)}>{a.label}</SelectItem>))}
                  </SelectContent>
                </Select>
              )}
            </div>
          </CollapsibleContent>
        </Collapsible>
      )}

      {(logType === 'keyword_search') && (
        <Collapsible defaultOpen>
          <CollapsibleTrigger className="flex w-full items-center justify-between rounded-md border p-2">
            <span className="flex items-center gap-2"><Tag className="size-4" /> Keyword Filters</span>
            <SlidersHorizontal className="size-4" />
          </CollapsibleTrigger>
          <CollapsibleContent className="p-2">
            <div className="grid grid-cols-1 gap-2 sm:grid-cols-2">
              <Select value={value.keywordFrequency} onValueChange={(v) => set('keywordFrequency', v)}>
                <SelectTrigger className="h-8"><SelectValue placeholder="Frequency" /></SelectTrigger>
                <SelectContent>
                  <SelectItem value="most">Most searched</SelectItem>
                  <SelectItem value="least">Least searched</SelectItem>
                </SelectContent>
              </Select>
              <Input value={value.searchContext ?? ''} onChange={(e) => set('searchContext', e.target.value)} placeholder="Search context" className="h-8" />
              <div className="col-span-1 sm:col-span-2 grid grid-cols-2 gap-2">
                {(options?.keywords ?? []).map((k) => (
                  <label key={String(k.value)} className="flex items-center gap-2 text-sm">
                    <Checkbox
                      checked={!!value.keywords?.includes(k.value)}
                      onCheckedChange={(v) => {
                        const curr = new Set(value.keywords ?? [])
                        if (v) curr.add(k.value)
                        else curr.delete(k.value)
                        set('keywords', Array.from(curr))
                      }}
                    />
                    {k.label}
                  </label>
                ))}
              </div>
            </div>
          </CollapsibleContent>
        </Collapsible>
      )}

      <Collapsible defaultOpen>
        <CollapsibleTrigger className="flex w-full items-center justify-between rounded-md border p-2">
          <span className="flex items-center gap-2"><SlidersHorizontal className="size-4" /> Patterns</span>
          <SlidersHorizontal className="size-4" />
        </CollapsibleTrigger>
        <CollapsibleContent className="p-2">
          <div className="grid grid-cols-1 gap-2 sm:grid-cols-2">
            <label className="flex items-center gap-2 text-sm">
              <Checkbox checked={!!value.highUserFrequency} onCheckedChange={(v) => set('highUserFrequency', !!v)} />
              Show unusual activity
            </label>
            <label className="flex items-center gap-2 text-sm">
              <Checkbox checked={!!value.batchOps} onCheckedChange={(v) => set('batchOps', !!v)} />
              Show batch operations
            </label>
            {logType === 'research_access' && (
              <Select value={value.accessPattern} onValueChange={(v) => set('accessPattern', v)}>
                <SelectTrigger className="h-8"><SelectValue placeholder="Access pattern" /></SelectTrigger>
                <SelectContent>
                  <SelectItem value="first">First-time access</SelectItem>
                  <SelectItem value="repeat">Repeated access</SelectItem>
                  <SelectItem value="spike">Access spike</SelectItem>
                </SelectContent>
              </Select>
            )}
          </div>
        </CollapsibleContent>
      </Collapsible>

      <div className="mt-auto flex items-center justify-end gap-2">
        <Button variant="outline" onClick={clearAll}><X className="mr-1 size-4" /> Reset</Button>
        <Button onClick={() => onApply?.(value)}><Filter className="mr-1 size-4" /> Apply</Button>
      </div>
    </div>
  )

  if (isMobile) {
    return (
      <div className={cn('relative', className)}>
        <Sheet open={open} onOpenChange={setOpen}>
          <SheetTrigger asChild>
            <Button className="fixed bottom-4 right-4 rounded-full shadow-lg" aria-label="Open filters">
              <SlidersHorizontal className="mr-2 size-5" /> Filters
            </Button>
          </SheetTrigger>
          <SheetContent side="bottom" className="h-[85vh]">
            <SheetHeader>
              <SheetTitle>Filters</SheetTitle>
            </SheetHeader>
            {renderFilters()}
          </SheetContent>
        </Sheet>
      </div>
    )
  }

  return (
    <aside className={cn('w-full max-w-[320px] rounded-md border bg-background', className)} aria-label="Filter sidebar">
      <div className="flex items-center justify-between border-b p-3">
        <div className="font-medium">Filters</div>
        <div className="text-xs text-muted-foreground">{chips.length} applied</div>
      </div>
      {renderFilters()}
    </aside>
  )
}

export type { Props as LogFiltersProps, FilterState as LogFilterState, FilterOptions as LogFilterOptions, LogType }
