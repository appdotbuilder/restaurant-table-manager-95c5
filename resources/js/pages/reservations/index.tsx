import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

interface Reservation {
    id: number;
    customer_name: string;
    customer_phone: string;
    pax: number;
    reservation_time: string;
    status: 'confirmed' | 'seated' | 'cancelled' | 'completed';
    assigned_table: {
        id: number;
        table_name: string;
        capacity: number;
    };
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginationMeta {
    total: number;
    per_page: number;
    current_page: number;
}

interface Props {
    reservations: {
        data: Reservation[];
        links: PaginationLink[];
        meta: PaginationMeta;
    };
    [key: string]: unknown;
}

export default function ReservationsIndex({ reservations }: Props) {
    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'confirmed':
                return <Badge className="bg-blue-100 text-blue-800">Confirmed</Badge>;
            case 'seated':
                return <Badge className="bg-green-100 text-green-800">Seated</Badge>;
            case 'cancelled':
                return <Badge className="bg-red-100 text-red-800">Cancelled</Badge>;
            case 'completed':
                return <Badge className="bg-gray-100 text-gray-800">Completed</Badge>;
            default:
                return <Badge>{status}</Badge>;
        }
    };

    const formatDateTime = (dateString: string) => {
        const date = new Date(dateString);
        return {
            date: date.toLocaleDateString(),
            time: date.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
            }),
        };
    };

    const formatPhone = (phone: string) => {
        // Simple phone formatting
        return phone.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
    };

    return (
        <>
            <Head title="📋 Reservations Management" />
            
            <div className="min-h-screen bg-gradient-to-b from-slate-50 to-slate-100">
                {/* Header */}
                <div className="bg-white shadow-sm border-b">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                        <div className="flex justify-between items-center">
                            <div>
                                <h1 className="text-3xl font-bold text-gray-900">
                                    📋 Reservations Management
                                </h1>
                                <p className="text-gray-600 mt-1">
                                    View and manage all restaurant reservations
                                </p>
                            </div>
                            <div className="flex space-x-2">
                                <Link href="/">
                                    <Button variant="outline" size="sm">
                                        🏢 Floor Plan
                                    </Button>
                                </Link>
                                <Link href="/reservations/create">
                                    <Button size="sm">
                                        ➕ New Reservation
                                    </Button>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center justify-between">
                                <span>All Reservations</span>
                                <span className="text-sm font-normal text-gray-500">
                                    Total: {reservations.meta?.total || reservations.data.length}
                                </span>
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            {reservations.data.length > 0 ? (
                                <div className="overflow-hidden rounded-lg border">
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>Customer</TableHead>
                                                <TableHead>Phone</TableHead>
                                                <TableHead>Table</TableHead>
                                                <TableHead>Guests</TableHead>
                                                <TableHead>Date</TableHead>
                                                <TableHead>Time</TableHead>
                                                <TableHead>Status</TableHead>
                                                <TableHead className="text-right">Actions</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {reservations.data.map((reservation) => {
                                                const { date, time } = formatDateTime(reservation.reservation_time);
                                                return (
                                                    <TableRow key={reservation.id}>
                                                        <TableCell className="font-medium">
                                                            {reservation.customer_name}
                                                        </TableCell>
                                                        <TableCell>
                                                            <a
                                                                href={`tel:${reservation.customer_phone}`}
                                                                className="text-blue-600 hover:text-blue-800"
                                                            >
                                                                {formatPhone(reservation.customer_phone)}
                                                            </a>
                                                        </TableCell>
                                                        <TableCell>
                                                            <div className="flex flex-col">
                                                                <span className="font-medium">
                                                                    {reservation.assigned_table.table_name}
                                                                </span>
                                                                <span className="text-xs text-gray-500">
                                                                    {reservation.assigned_table.capacity} seats
                                                                </span>
                                                            </div>
                                                        </TableCell>
                                                        <TableCell>
                                                            <Badge variant="outline">
                                                                {reservation.pax} {reservation.pax === 1 ? 'guest' : 'guests'}
                                                            </Badge>
                                                        </TableCell>
                                                        <TableCell>{date}</TableCell>
                                                        <TableCell className="font-mono">{time}</TableCell>
                                                        <TableCell>
                                                            {getStatusBadge(reservation.status)}
                                                        </TableCell>
                                                        <TableCell className="text-right">
                                                            <div className="flex justify-end space-x-2">
                                                                <Link href={`/reservations/${reservation.id}`}>
                                                                    <Button variant="outline" size="sm">
                                                                        View
                                                                    </Button>
                                                                </Link>
                                                                <Link href={`/reservations/${reservation.id}/edit`}>
                                                                    <Button variant="outline" size="sm">
                                                                        Edit
                                                                    </Button>
                                                                </Link>
                                                            </div>
                                                        </TableCell>
                                                    </TableRow>
                                                );
                                            })}
                                        </TableBody>
                                    </Table>
                                </div>
                            ) : (
                                <div className="text-center py-12">
                                    <div className="text-6xl mb-4">📋</div>
                                    <h3 className="text-lg font-medium text-gray-900 mb-2">
                                        No reservations found
                                    </h3>
                                    <p className="text-gray-500 mb-6">
                                        Get started by creating your first reservation.
                                    </p>
                                    <Link href="/reservations/create">
                                        <Button>
                                            ➕ Create First Reservation
                                        </Button>
                                    </Link>
                                </div>
                            )}

                            {/* Pagination */}
                            {reservations.links && reservations.links.length > 3 && (
                                <div className="flex justify-center mt-6 space-x-2">
                                    {reservations.links.map((link, index: number) => (
                                        <Link
                                            key={index}
                                            href={link.url || '#'}
                                            className={`px-3 py-2 text-sm rounded-md ${
                                                link.active
                                                    ? 'bg-blue-500 text-white'
                                                    : link.url
                                                    ? 'bg-white text-gray-700 hover:bg-gray-50 border'
                                                    : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                            }`}
                                            preserveState
                                        >
                                            <span dangerouslySetInnerHTML={{ __html: link.label }} />
                                        </Link>
                                    ))}
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}