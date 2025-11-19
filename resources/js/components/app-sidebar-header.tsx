import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { dashboard, profile } from '@/routes';
import { index as usersIndex } from '@/routes/users';
import { index as logsIndex } from '@/routes/logs';
import { index as facultiesIndex } from '@/routes/faculties';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { BookOpen, Folder, LayoutGrid, UserCog, UserCircle, ScrollText, Home, Users } from 'lucide-react';
import AppLogo from './app-logo';
import { type BreadcrumbItem } from '@/types';

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/react-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#react',
        icon: BookOpen,
    },
];

export function AppSidebarHeader({ breadcrumbs = [] }: { breadcrumbs?: BreadcrumbItem[] }) {
    return (
        <header className="flex items-center gap-2 px-3 py-2">
            <AppLogo />
            {breadcrumbs.length > 0 && (
                <nav aria-label="Breadcrumb" className="ml-auto text-sm text-muted-foreground">
                    {breadcrumbs.map((b, i) => (
                        <span key={b.href ?? i}>
                            {i > 0 && ' / '}
                            {b.href ? <Link href={b.href}>{b.title}</Link> : b.title}
                        </span>
                    ))}
                </nav>
            )}
        </header>
    );
}

export function AppSidebar() {
    const { auth } = usePage<SharedData>().props;
    const isAdmin = auth.user.roles?.some(role => role.name === 'Administrator') ?? false;


    const mainNavItems: NavItem[] = [
        {
            title: 'Home',
            href: '/',
            icon: Home,
        },
        ...(isAdmin
            ? [
                  {
                      title: 'Dashboard',
                      href: dashboard(),
                      icon: LayoutGrid,
                  },
              ]
            : []),
        {
            title: 'My Profile',
            href: profile(),
            icon: UserCircle,
        },
        {
            title: 'View Faculty',
            href: facultiesIndex.url(),
            icon: Users,
        },
        ...(isAdmin
            ? [
                  {
                      title: 'User Management',
                      href: usersIndex(),
                      icon: UserCog,
                  },
                  {
                      title: 'View Logs',
                      href: logsIndex(),
                      icon: ScrollText,
                  },
              ]
            : []),
    ];


    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/" prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>


            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>


            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}