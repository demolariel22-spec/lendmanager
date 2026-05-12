@extends('layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mt-2 mb-3">
        <h1 class="fs-4 m-0">Sales</h1>
        <a href="{{ route('home') }}" class="btn btn-secondary py-1">Back</a>
    </div>

    <div class="table-group table-responsive border border-dark" style="max-height: 85vh">
        <table class="table table-hover mb-0">
            <thead class="sticky-top z-1">
                <tr>
                    <th>Date</th>
                    <th>Barcode</th>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $sale)
                    <tr>
                        <td>{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                        <td>{{ $sale->barcode ?? '--' }}</td>
                        <td>{{ $sale->item }}</td>
                        <td>{{ $sale->qty }}</td>
                        <td>{{ number_format($sale->price, 2) }}</td>
                        <td>{{ number_format($sale->total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No sales found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
