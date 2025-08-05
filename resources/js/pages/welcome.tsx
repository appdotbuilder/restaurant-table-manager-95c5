import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';


interface Table {
    id: number;
    table_name: string;
    capacity: number;
    status: 'available' | 'occupied' | 'reserved' | 'cleaning' | 'billed';
    position_x: number;
    position_y: number;
    current_session_id?: number;
    current_session?: {
        start_time: string;
        pax: number;
        customer_name?: string;
    };
    reservations?: Array<{
        id: number;
        customer_name: string;
        reservation_time: string;
        pax: number;
    }>;
}

interface Reservation {
    id: number;
    customer_name: string;
    customer_phone: string;
    pax: number;
    reservation_time: string;
    assigned_table: {
        table_name: string;
    };
}

interface Props {
    tables: Table[];
    upcomingReservations: Reservation[];
    [key: string]: unknown;
}

export default function Welcome({ tables, upcomingReservations }: Props) {
    const [selectedTable, setSelectedTable] = useState<Table | null>(null);
    const [showTableModal, setShowTableModal] = useState(false);
    const [customerName, setCustomerName] = useState('');
    const [pax, setPax] = useState(1);

    const getTableColor = (status: string) => {
        switch (status) {
            case 'available':
                return 'bg-green-500 hover:bg-green-600 border-green-600';
            case 'occupied':
                return 'bg-red-500 hover:bg-red-600 border-red-600';
            case 'reserved':
                return 'bg-yellow-500 hover:bg-yellow-600 border-yellow-600';
            case 'cleaning':
                return 'bg-blue-500 hover:bg-blue-600 border-blue-600';
            case 'billed':
                return 'bg-orange-500 hover:bg-orange-600 border-orange-600';
            default:
                return 'bg-gray-500 hover:bg-gray-600 border-gray-600';
        }
    };

    const getStatusBadgeColor = (status: string) => {
        switch (status) {
            case 'available':
                return 'bg-green-100 text-green-800 border-green-300';
            case 'occupied':
                return 'bg-red-100 text-red-800 border-red-300';
            case 'reserved':
                return 'bg-yellow-100 text-yellow-800 border-yellow-300';
            case 'cleaning':
                return 'bg-blue-100 text-blue-800 border-blue-300';
            case 'billed':
                return 'bg-orange-100 text-orange-800 border-orange-300';
            default:
                return 'bg-gray-100 text-gray-800 border-gray-300';
        }
    };

    const handleTableClick = (table: Table) => {
        setSelectedTable(table);
        setShowTableModal(true);
        setCustomerName('');
        setPax(1);
    };

    const handleStatusChange = (newStatus: string) => {
        if (!selectedTable) return;

        const data: { status: string; customer_name?: string; pax?: number } = { status: newStatus };
        
        if (newStatus === 'occupied') {
            data.customer_name = customerName;
            data.pax = pax;
        }

        router.post(route('tables.status.store', selectedTable.id), data, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                setShowTableModal(false);
                setSelectedTable(null);
            }
        });
    };

    const formatTime = (dateString: string) => {
        return new Date(dateString).toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString();
    };

    const calculateDuration = (startTime: string) => {
        const start = new Date(startTime);
        const now = new Date();
        const diffMinutes = Math.floor((now.getTime() - start.getTime()) / (1000 * 60));
        const hours = Math.floor(diffMinutes / 60);
        const minutes = diffMinutes % 60;
        return hours > 0 ? `${hours}h ${minutes}m` : `${minutes}m`;
    };

    return (
        <>
            <Head title="🍽️ Restaurant Floor Plan" />
            
            <div className="min-h-screen bg-gradient-to-b from-slate-50 to-slate-100">
                {/* Header */}
                <div className="bg-white shadow-sm border-b">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                        <div className="flex justify-between items-center">
                            <div>
                                <h1 className="text-3xl font-bold text-gray-900">
                                    🍽️ Restaurant Floor Plan
                                </h1>
                                <p className="text-gray-600 mt-1">
                                    Manage table statuses and reservations in real-time
                                </p>
                            </div>
                            <div className="flex space-x-2">
                                <a href="/login">
                                    <Button variant="outline" size="sm">
                                        🔑 Staff Login
                                    </Button>
                                </a>
                                <a href="/reservations">
                                    <Button size="sm">
                                        📋 View All Reservations
                                    </Button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div className="grid grid-cols-1 lg:grid-cols-4 gap-8">
                        {/* Floor Plan */}
                        <div className="lg:col-span-3">
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center justify-between">
                                        <span>🏢 Restaurant Floor Plan</span>
                                        <div className="flex space-x-4 text-sm">
                                            <span className="flex items-center">
                                                <div className="w-3 h-3 bg-green-500 rounded-full mr-1"></div>
                                                Available
                                            </span>
                                            <span className="flex items-center">
                                                <div className="w-3 h-3 bg-red-500 rounded-full mr-1"></div>
                                                Occupied
                                            </span>
                                            <span className="flex items-center">
                                                <div className="w-3 h-3 bg-yellow-500 rounded-full mr-1"></div>
                                                Reserved
                                            </span>
                                            <span className="flex items-center">
                                                <div className="w-3 h-3 bg-blue-500 rounded-full mr-1"></div>
                                                Cleaning
                                            </span>
                                            <span className="flex items-center">
                                                <div className="w-3 h-3 bg-orange-500 rounded-full mr-1"></div>
                                                Billed
                                            </span>
                                        </div>
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="relative bg-gray-50 rounded-lg border-2 border-dashed border-gray-200 overflow-hidden" style={{ height: '600px' }}>
                                        {tables.map((table) => (
                                            <button
                                                key={table.id}
                                                onClick={() => handleTableClick(table)}
                                                className={`absolute rounded-lg border-2 text-white font-semibold shadow-lg transition-all transform hover:scale-105 flex flex-col items-center justify-center ${getTableColor(table.status)}`}
                                                style={{
                                                    left: `${table.position_x}px`,
                                                    top: `${table.position_y}px`,
                                                    width: '80px',
                                                    height: '80px',
                                                }}
                                            >
                                                <div className="text-sm font-bold">
                                                    {table.table_name}
                                                </div>
                                                <div className="text-xs">
                                                    {table.capacity} seats
                                                </div>
                                                {table.current_session && (
                                                    <div className="text-xs mt-1">
                                                        {calculateDuration(table.current_session.start_time)}
                                                    </div>
                                                )}
                                            </button>
                                        ))}
                                    </div>
                                </CardContent>
                            </Card>
                        </div>

                        {/* Sidebar */}
                        <div className="space-y-6">
                            {/* Stats */}
                            <Card>
                                <CardHeader>
                                    <CardTitle>📊 Quick Stats</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-3">
                                        <div className="flex justify-between">
                                            <span>Available:</span>
                                            <Badge className="bg-green-100 text-green-800">
                                                {tables.filter(t => t.status === 'available').length}
                                            </Badge>
                                        </div>
                                        <div className="flex justify-between">
                                            <span>Occupied:</span>
                                            <Badge className="bg-red-100 text-red-800">
                                                {tables.filter(t => t.status === 'occupied').length}
                                            </Badge>
                                        </div>
                                        <div className="flex justify-between">
                                            <span>Reserved:</span>
                                            <Badge className="bg-yellow-100 text-yellow-800">
                                                {tables.filter(t => t.status === 'reserved').length}
                                            </Badge>
                                        </div>
                                        <div className="flex justify-between">
                                            <span>Cleaning:</span>
                                            <Badge className="bg-blue-100 text-blue-800">
                                                {tables.filter(t => t.status === 'cleaning').length}
                                            </Badge>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Upcoming Reservations */}
                            <Card>
                                <CardHeader>
                                    <CardTitle>⏰ Upcoming Reservations</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-3">
                                        {upcomingReservations.length > 0 ? (
                                            upcomingReservations.map((reservation) => (
                                                <div key={reservation.id} className="bg-gray-50 p-3 rounded-lg">
                                                    <div className="font-medium text-sm">
                                                        {reservation.customer_name}
                                                    </div>
                                                    <div className="text-xs text-gray-600 mt-1">
                                                        {reservation.assigned_table.table_name} • {reservation.pax} guests
                                                    </div>
                                                    <div className="text-xs text-gray-500">
                                                        {formatDate(reservation.reservation_time)} at {formatTime(reservation.reservation_time)}
                                                    </div>
                                                </div>
                                            ))
                                        ) : (
                                            <p className="text-sm text-gray-500 text-center py-4">
                                                No upcoming reservations
                                            </p>
                                        )}
                                    </div>
                                </CardContent>
                            </Card>
                        </div>
                    </div>
                </div>

                {/* Table Modal */}
                <Dialog open={showTableModal} onOpenChange={setShowTableModal}>
                    <DialogContent className="max-w-md">
                        <DialogHeader>
                            <DialogTitle>
                                🍽️ {selectedTable?.table_name} - {selectedTable?.capacity} seats
                            </DialogTitle>
                            <DialogDescription>
                                Current status: 
                                <Badge className={`ml-2 ${getStatusBadgeColor(selectedTable?.status || '')}`}>
                                    {selectedTable?.status?.toUpperCase()}
                                </Badge>
                            </DialogDescription>
                        </DialogHeader>

                        <div className="space-y-4">
                            {selectedTable?.current_session && (
                                <div className="bg-blue-50 p-3 rounded-lg">
                                    <div className="text-sm font-medium">Current Session</div>
                                    <div className="text-xs text-gray-600 mt-1">
                                        Customer: {selectedTable.current_session.customer_name || 'Walk-in'}
                                    </div>
                                    <div className="text-xs text-gray-600">
                                        Duration: {calculateDuration(selectedTable.current_session.start_time)}
                                    </div>
                                    <div className="text-xs text-gray-600">
                                        Guests: {selectedTable.current_session.pax}
                                    </div>
                                </div>
                            )}

                            {selectedTable?.status === 'available' && (
                                <div className="space-y-3">
                                    <div>
                                        <Label htmlFor="customerName">Customer Name (Optional)</Label>
                                        <Input
                                            id="customerName"
                                            value={customerName}
                                            onChange={(e) => setCustomerName(e.target.value)}
                                            placeholder="Enter customer name"
                                        />
                                    </div>
                                    <div>
                                        <Label htmlFor="pax">Number of Guests</Label>
                                        <Input
                                            id="pax"
                                            type="number"
                                            min="1"
                                            max={selectedTable?.capacity}
                                            value={pax}
                                            onChange={(e) => setPax(parseInt(e.target.value) || 1)}
                                        />
                                    </div>
                                </div>
                            )}

                            <div className="grid grid-cols-2 gap-2">
                                {selectedTable?.status !== 'available' && (
                                    <Button
                                        onClick={() => handleStatusChange('available')}
                                        className="bg-green-500 hover:bg-green-600"
                                    >
                                        ✅ Set Available
                                    </Button>
                                )}
                                
                                {selectedTable?.status !== 'occupied' && (
                                    <Button
                                        onClick={() => handleStatusChange('occupied')}
                                        className="bg-red-500 hover:bg-red-600"
                                        disabled={selectedTable?.status === 'available' && !customerName && pax < 1}
                                    >
                                        👥 Seat Customers
                                    </Button>
                                )}
                                
                                {selectedTable?.status !== 'reserved' && (
                                    <Button
                                        onClick={() => handleStatusChange('reserved')}
                                        className="bg-yellow-500 hover:bg-yellow-600"
                                    >
                                        📅 Mark Reserved
                                    </Button>
                                )}
                                
                                {selectedTable?.status !== 'cleaning' && (
                                    <Button
                                        onClick={() => handleStatusChange('cleaning')}
                                        className="bg-blue-500 hover:bg-blue-600"
                                    >
                                        🧽 Set Cleaning
                                    </Button>
                                )}
                                
                                {selectedTable?.status === 'occupied' && (
                                    <Button
                                        onClick={() => handleStatusChange('billed')}
                                        className="bg-orange-500 hover:bg-orange-600"
                                    >
                                        💳 Mark Billed
                                    </Button>
                                )}
                            </div>
                        </div>
                    </DialogContent>
                </Dialog>
            </div>
        </>
    );
}