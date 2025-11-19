import { useMemo, useState, useRef } from 'react'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Button } from '@/components/ui/button'

interface Alignment {
  label: string
  percentage: number
}

interface Datum {
  year: number
  count: number
  topAlignments: Alignment[]
}

interface Props {
  data: Datum[]
  onBarClick?: (year: number) => void
  onExport?: (node: HTMLElement) => void
}

export default function YearBarChart({ data, onBarClick, onExport }: Props) {
  const [selected, setSelected] = useState<number | null>(null)
  const max = useMemo(() => Math.max(0, ...data.map((d) => d.count)), [data])
  const ref = useRef<HTMLDivElement>(null)

  return (
    <Card ref={ref}>
      <CardHeader>
        <CardTitle>Research Count per Year</CardTitle>
        <CardDescription>Years on x-axis</CardDescription>
      </CardHeader>
      <CardContent>
        <div className="space-y-4">
          {data.map((d) => {
            const w = max > 0 ? (d.count / max) * 100 : 0
            const sel = selected === d.year
            return (
              <div key={d.year} className="space-y-2">
                <div
                  className={`p-3 rounded-lg border transition-all cursor-pointer hover:bg-accent ${sel ? 'bg-accent border-primary' : ''}`}
                  onClick={() => {
                    setSelected(sel ? null : d.year)
                    onBarClick?.(d.year)
                  }}
                >
                  <div className="flex items-center justify-between text-sm mb-2">
                    <span className="font-medium truncate flex-1">{d.year}</span>
                    <span className="text-muted-foreground ml-2">{d.count}</span>
                  </div>
                  <div className="w-full bg-secondary rounded-full h-3">
                    <div className="bg-primary rounded-full h-3 transition-all" style={{ width: `${w}%`, minWidth: w > 0 ? '8px' : '0' }} />
                  </div>
                  {sel && (
                    <div className="mt-4 pt-4 border-t space-y-2">
                      {d.topAlignments.slice(0, 5).map((a) => (
                        <div key={a.label} className="flex items-center justify-between text-sm">
                          <span className="font-medium">{a.label}</span>
                          <span className="text-muted-foreground">{a.percentage}%</span>
                        </div>
                      ))}
                    </div>
                  )}
                </div>
              </div>
            )
          })}
        </div>
        <div className="mt-4">
          <Button variant="outline" size="sm" onClick={() => { if (ref.current) onExport?.(ref.current) }}>Export Chart</Button>
        </div>
      </CardContent>
    </Card>
  )
}