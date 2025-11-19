import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Target, Layers, Globe } from 'lucide-react'

interface Props {
  agendaTotal: number
  srigTotal: number
  sdgTotal: number
  onClick?: (type: 'agenda' | 'srig' | 'sdg') => void
}

export default function AlignmentStats({ agendaTotal, srigTotal, sdgTotal, onClick }: Props) {
  return (
    <div className="grid gap-4 md:grid-cols-3">
      <Card onClick={() => onClick?.('agenda')} className="cursor-pointer">
        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle className="text-sm font-medium">Agenda</CardTitle>
          <Target className="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
          <div className="text-2xl font-bold">{agendaTotal}</div>
        </CardContent>
      </Card>
      <Card onClick={() => onClick?.('srig')} className="cursor-pointer">
        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle className="text-sm font-medium">SRIG</CardTitle>
          <Layers className="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
          <div className="text-2xl font-bold">{srigTotal}</div>
        </CardContent>
      </Card>
      <Card onClick={() => onClick?.('sdg')} className="cursor-pointer">
        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle className="text-sm font-medium">SDG</CardTitle>
          <Globe className="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
          <div className="text-2xl font-bold">{sdgTotal}</div>
        </CardContent>
      </Card>
    </div>
  )
}