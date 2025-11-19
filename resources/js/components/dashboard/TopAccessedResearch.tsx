import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Link } from '@inertiajs/react'

interface Item {
  id: number | string
  title: string
  count: number
  lastAccessed: string
}

interface Props {
  items: Item[]
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })
}

export default function TopAccessedResearch({ items }: Props) {
  return (
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead>Title</TableHead>
          <TableHead className="text-right">Views</TableHead>
          <TableHead className="text-right">Last Accessed</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        {items.map((i) => (
          <TableRow key={i.id}>
            <TableCell className="font-medium">
              <Link href={`/research/${i.id}`} className="line-clamp-1 hover:underline">{i.title}</Link>
            </TableCell>
            <TableCell className="text-right">{i.count}</TableCell>
            <TableCell className="text-right">{formatDate(i.lastAccessed)}</TableCell>
          </TableRow>
        ))}
      </TableBody>
    </Table>
  )
}